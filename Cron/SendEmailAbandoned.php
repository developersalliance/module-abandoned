<?php declare(strict_types=1);
/**
 * @author Developers-Alliance team
 * @copyright Copyright (c) 2024 Developers-alliance (https://www.developers-alliance.com)
 * @package Abandoned Cart for Magento 2
 */

namespace DevAll\Abandoned\Cron;

use DevAll\Abandoned\Model\TriggerEmail;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use DevAll\Abandoned\Model\TimeFilter;
use Psr\Log\LoggerInterface;

/**
 * This class checks if there are any abandoned carts and sends an email to the customer
 */
class SendEmailAbandoned
{
    /**
     * @var CollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var TimeFilter
     */
    private $date;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TriggerEmail
     */
    private $triggerEmail;

    /**
     * @param CollectionFactory $quoteCollectionFactory
     * @param TimeFilter $date
     * @param EventManager $eventManager
     * @param LoggerInterface $logger
     * @param TriggerEmail $triggerEmail
     */
    public function __construct(
        CollectionFactory          $quoteCollectionFactory,
        TimeFilter                 $date,
        EventManager               $eventManager,
        LoggerInterface            $logger,
        TriggerEmail               $triggerEmail
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->date = $date;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
        $this->triggerEmail = $triggerEmail;
    }

    /**
     * @return void
     */
    public function execute(): void
    {
        $formattedDate = $this->date->filterDataTime();

        $collection = $this->quoteCollectionFactory->create();
        $quoteData = $collection->addFieldToSelect(['entity_id', 'updated_at', 'is_active', 'items_count', 'customer_email', 'customer_firstname','subtotal'])
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('items_count', array('gt' => 0))
            ->addFieldToFilter('customer_email', array('notnull' => true))
            ->addFieldToFilter('updated_at', ['gteq' => $formattedDate]);

        try {
            $this->triggerEmail->collectDataEmail($quoteData->getData());
        } catch (\Throwable $t) {
            $this->logger->debug($t->getMessage());
        }
    }
}
