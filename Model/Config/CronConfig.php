<?php declare(strict_types=1);
/**
 * @author Developers-Alliance team
 * @copyright Copyright (c) 2024 Developers-alliance (https://www.developers-alliance.com)
 * @package Abandoned Cart for Magento 2
 */

namespace DevAll\Abandoned\Model\Config;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;

/**
 * This class is responsible for saving the cron expression in the database
 */
class CronConfig extends Value
{
    /**
     * Path to the cron expression in the database
     */
    const CRON_STRING_PATH = 'crontab/default/jobs/devall_abandoned_send_email_abandoned/schedule/cron_expr';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param LoggerInterface $logger
     * @param WriterInterface $configWriter
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LoggerInterface $logger,
        WriterInterface $configWriter,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $resource,
            $resourceCollection,
            $data
        );
        $this->logger = $logger;
        $this->configWriter = $configWriter;
    }

    /**
     * @return CronConfig
     * @throws LocalizedException
     */
    public function afterSave(): CronConfig
    {
        $time = $this->getData('value');

        try {
            $this->configWriter->save(self::CRON_STRING_PATH, $time);
        } catch (\Throwable $t) {
            $this->logger->error('Error saving cron expression: ' . $t->getMessage());
            throw new LocalizedException(__('Something went wrong. We can\'t save the cron expression.'));
        }
        return parent::afterSave();
    }


}
