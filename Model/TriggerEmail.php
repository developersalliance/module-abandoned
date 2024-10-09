<?php declare(strict_types=1);
/**
 * @author Developers-Alliance team
 * @copyright Copyright (c) 2024 Developers-alliance (https://www.developers-alliance.com)
 * @package Abandoned Cart for Magento 2
 */

namespace DevAll\Abandoned\Model;

use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as QuoteItemCollectionFactory;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use DevAll\Abandoned\ViewModel\AbandonedItemsImages;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Area;
use Psr\Log\LoggerInterface;

/**
 * Class TriggerEmail for sending emails.
 * Collects data about abandoned carts and sends an email to the customer.
 */
class TriggerEmail
{
    /**
     * Paths to the configuration settings related to the sender's name.
     */
    const PATH_EMAIL_SENDER = 'devall_abandoned_section_general/devall_abandoned_email_group_general/sender_name';

    /**
     * Paths to the configuration settings related to the email template.
     */
    const PATH_EMAIL_TEMPLATE = 'devall_abandoned_section_general/devall_abandoned_email_group_general/email_template';

    /**
     * Paths to the configuration settings related to the email subject.
     */
    const PATH_EMAIL_SUBJECT = 'devall_abandoned_section_general/devall_abandoned_email_group_general/email_subject';

    /**
     * Paths to the configuration settings related to the sender's email.
     */
    const PATH_EMAIL_SENDER_EMAIL = 'devall_abandoned_section_general/devall_abandoned_email_group_general/sender_email';

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var QuoteItemCollectionFactory
     */
    private $quoteItemFactory;

    /**
     * @var AbandonedItemsImages
     */
    private $abandonedItemsImages;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param TransportBuilder $transportBuilder
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param CollectionFactory $quoteCollectionFactory
     * @param QuoteItemCollectionFactory $quoteItemFactory
     * @param AbandonedItemsImages $abandonedItemsImages
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        TransportBuilder           $transportBuilder,
        ScopeConfigInterface       $scopeConfig,
        StoreManagerInterface      $storeManager,
        LoggerInterface            $logger,
        CollectionFactory          $quoteCollectionFactory,
        QuoteItemCollectionFactory $quoteItemFactory,
        AbandonedItemsImages       $abandonedItemsImages,
        ProductRepositoryInterface $productRepository
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->abandonedItemsImages = $abandonedItemsImages;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $dataAbandoned
     * @return void
     */
    public function collectDataEmail($dataAbandoned)
    {
        $emailSubject = $this->scopeConfig->getValue(self::PATH_EMAIL_SUBJECT);
        try {
            foreach ($dataAbandoned as $data) {
                $transport = array();
                $indexer = 0;

                $userEmail = $data['customer_email'];

                $quote = (int)$data['entity_id'];

                $templatesVars = [
                    "firstname" => $data['customer_firstname'],
                    "cart_total" => number_format((float)$data['subtotal'], 2),
                    "email_subject" => $emailSubject
                ];

                $collectionItems = $this->quoteItemFactory->create();
                $productDetails = $collectionItems->addFieldToSelect(['quote_id', 'parent_item_id', 'product_type', 'product_id', 'qty', 'price'])
                    ->addFieldToFilter('quote_id', array('eq' => $quote));

                foreach ($productDetails as $productData) {

                    $price = (int)$productData->getPrice();
                    $qty = $productData->getQty();

                    if ($price > 0 && $qty > 0) {
                        $transport[$indexer] = [
                            "price" => $price * $qty,
                            "qty" => $qty
                        ];
                    }

                    if ($productData->getData('product_type') !== "configurable" || $productData->getData('parent_item_id') !== null) {
                        $product = $this->productRepository->getById($productData->getData('product_id'));
                        $retrievePhotoProduct = $product->getMediaGalleryImages();
                        foreach ($retrievePhotoProduct as $image) {
                            $transport[$indexer] += ["images" => $image->getUrl()];
                            $transport[$indexer] = array_reverse($transport[$indexer]);
                            $indexer += 1;
                            break;
                        }
                    }
                }
                $this->abandonedItemsImages->prepareEmail($transport);
                $this->sendEmail($templatesVars, $userEmail);
            }
        } catch (NoSuchEntityException $e) {
            $this->logger->error("Failed to send email for quote: " . $e->getMessage());
        }
    }

    /**
     * @param $templatesVars
     * @param $userEmail
     * @return void
     */
    public function sendEmail($templatesVars, $userEmail)
    {
        $emailTemplate = $this->scopeConfig->getValue(self::PATH_EMAIL_TEMPLATE);
        $emailSender = $this->scopeConfig->getValue(self::PATH_EMAIL_SENDER);
        $emailSenderEmail = $this->scopeConfig->getValue(self::PATH_EMAIL_SENDER_EMAIL);

        $sender = [
            'name'  => $emailSender,
            'email' => $emailSenderEmail
        ];

        try {
            $templateOptions = [
                'area' => Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId()
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars(["customVariableEmail" => $templatesVars])
                ->setFromByScope($sender, $templateOptions['store'])
                ->addTo($userEmail)
                ->getTransport();
            $transport->sendMessage();
        } catch (\Throwable $t) {
            $this->logger->error("Failed to send email for quote: " . $t->getMessage());
        }
    }
}
