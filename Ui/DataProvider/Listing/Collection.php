<?php declare(strict_types=1);
/**
 * @author Developers-Alliance team
 * @copyright Copyright (c) 2024 Developers-alliance (https://www.developers-alliance.com)
 * @package Abandoned Cart for Magento 2
 */

namespace DevAll\Abandoned\Ui\DataProvider\Listing;

use DevAll\Abandoned\Model\TimeFilter;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 */
class Collection extends SearchResult
{
    /**
     * @var TimeFilter
     */
    private $time;

    /**
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param TimeFilter $time
     * @param string $mainTable
     * @param null $resourceModel
     * @param null $identifierName
     * @param null $connectionName
     * @throws LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger        $logger,
        FetchStrategy $fetchStrategy,
        EventManager  $eventManager,
        TimeFilter    $time,
        $mainTable,
        $resourceModel = null,
        $identifierName = null,
        $connectionName = null
    ) {
        $this->time = $time;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $identifierName,
            $connectionName
        );
    }

    /**
     * @return $this|Collection
     */
    protected function _initSelect(): Collection
    {
        parent::_initSelect();
        $this->CustomFilter();

        return $this;
    }

    /**
     * @return $this
     */
    public function CustomFilter(): Collection
    {
        $formattedDate = $this->time->filterDataTime();

        $this->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('items_count', array('gt' => 0))
            ->addFieldToFilter('customer_email', array('notnull' => true))
            ->addFieldToFilter('updated_at', ['gteq' => $formattedDate]);

        return $this;
    }
}
