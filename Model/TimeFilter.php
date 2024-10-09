<?php declare(strict_types=1);
/**
 * @author Developers-Alliance team
 * @copyright Copyright (c) 2024 Developers-alliance (https://www.developers-alliance.com)
 * @package Abandoned Cart for Magento 2
 */

namespace DevAll\Abandoned\Model;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * This class returns the time period for the abandoned cart
 */
class TimeFilter
{
    const PATH_TIME_ABANDONED = 'devall_abandoned_section_general/devall_abandoned_email_group_general/time_period';

    /**
     * @var DateTime
     */
    private $time;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param DateTime $time
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        DateTime $time,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->time = $time;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return false|string
     */
    public function filterDataTime()
    {
        $hours = (int) $this->scopeConfig->getValue(self::PATH_TIME_ABANDONED);
        return $this->time->gmtDate('Y-m-d H:i:s', strtotime("-$hours hours"));
    }
}
