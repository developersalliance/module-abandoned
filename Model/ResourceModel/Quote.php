<?php declare(strict_types=1);
/**
 * @author Developers-Alliance team
 * @copyright Copyright (c) 2024 Developers-alliance (https://www.developers-alliance.com)
 * @package Abandoned Cart for Magento 2
 */

namespace DevAll\Abandoned\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Resource model for quote
 */
class Quote extends AbstractDb
{
    /** @var string Main table name */
    const MAIN_TABLE = 'quote';

    /** @var string Main table primary key field name */
    const ID_FIELD_NAME = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, self::ID_FIELD_NAME);
    }
}
