<?php declare(strict_types=1);
/**
 * @author Developers-Alliance team
 * @copyright Copyright (c) 2024 Developers-alliance (https://www.developers-alliance.com)
 * @package Abandoned Cart for Magento 2
 */

namespace DevAll\Abandoned\Controller\Adminhtml\History;

use DevAll\Abandoned\Model\TriggerEmail;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface;

/**
 * Class SendEmail for manually sending the email
 */
class SendEmail extends Action
{
    const ADMIN_RESOURCE = 'DevAll_Abandoned::resend_email';

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var TriggerEmail
     */
    private $triggerEmail;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param EventManager $eventManager
     * @param LoggerInterface $logger
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        EventManager $eventManager,
        LoggerInterface $logger,
        TriggerEmail  $triggerEmail,
        Filter  $filter
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->context = $context;
        $this->triggerEmail = $triggerEmail;
    }

    /**
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->collectionFactory->create();

        try {
            $items = $this->filter->getCollection($collection);
            $this->triggerEmail->collectDataEmail($items->getData());
            $this->messageManager->addSuccessMessage(__('We sent the email successfully'));
        } catch (\Throwable $t) {
            $this->messageManager->addErrorMessage(__('We could not send the email'));
            $this->logger->debug($t->getMessage());
        }

        $resultRedirect->setPath('*/*/');
        return $resultRedirect;
    }
}
