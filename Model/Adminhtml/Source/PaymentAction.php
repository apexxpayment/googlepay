<?php
/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */
namespace Apexx\Googlepay\Model\Adminhtml\Source;

/**
 * Class PaymentAction
 * @package Apexx\Googlepay\Model\Adminhtml\Source
 */

class PaymentAction implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'authorize',
                'label' => __('Authorize'),
            ],
            [
                'value' => 'authorize_capture',
                'label' => __('Authorize and Capture'),
            ],
        ];
    }
}