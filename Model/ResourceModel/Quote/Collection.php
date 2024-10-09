<?php declare(strict_types=1);
/**
 * @author Developers-Alliance team
 * @copyright Copyright (c) 2024 Developers-alliance (https://www.developers-alliance.com)
 * @package Abandoned Cart for Magento 2
 */

namespace DevAll\Abandoned\Model\ResourceModel\Quote;

use DevAll\Abandoned\Model\Quote;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection for quote
 */
class Collection extends AbstractCollection
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Quote::class, \DevAll\Abandoned\Model\ResourceModel\Quote::class);
    }
}
