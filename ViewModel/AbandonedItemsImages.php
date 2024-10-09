<?php declare(strict_types=1);
/**
 * @author Developers-Alliance team
 * @copyright Copyright (c) 2024 Developers-alliance (https://www.developers-alliance.com)
 * @package Abandoned Cart for Magento 2
 */

namespace DevAll\Abandoned\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * This class is responsible for passing variables to the email.
 */
class AbandonedItemsImages implements ArgumentInterface
{
    public $prepareEmail = [];

    /**
     * @param $transportUrls
     * @return void
     */
    public function prepareEmail($transportUrls)
    {
       $this->prepareEmail = $transportUrls;
    }
}
