<?php declare(strict_types=1);
/**
 * @author Developers-Alliance team
 * @copyright Copyright (c) 2024 Developers-alliance (https://www.developers-alliance.com)
 * @package Abandoned Cart for Magento 2
 */

namespace DevAll\Abandoned\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class TimePeriodOptions
 */
class TimePeriodOptions implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '0 */6 * * *', 'label' => __('Four Times a Day')],
            ['value' => '0 */12 * * *', 'label' => __('Twice a Day')],
            ['value' => '0 */24 * * *', 'label' => __('Once a Day')],
        ];
    }
}
