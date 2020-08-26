<?php
/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */
namespace Apexx\Googlepay\Model\Adminhtml\Source;

/**
 * Class ThreedSecureStatus
 * @package Apexx\Googlepay\Model\Adminhtml\Source
 */
class ThreedSecureStatus
{
    public function toOptionArray()
    {
        return [
                ['value' => 'y', 'label' => __('Y')],
                ['value' => 'n', 'label' => __('N')],
                ['value' => 'u', 'label' => __('U')],
                ['value' => 'a', 'label' => __('A')],
                ['value' => 'r', 'label' => __('R')],
        ];
    }
}
