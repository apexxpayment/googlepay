<?php
/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */
namespace Apexx\Googlepay\Model\Adminhtml\Source;

/**
 * Class Paymenttype
 * @package Apexx\Googlepay\Model\Adminhtml\Source
 */
class Paymenttype
{
    /**
     * Different payment type.
     */
    const GOOGLEPAY_PAYMENT_TYPE = 'Invoice';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
                    [
                        'value' => self::GOOGLEPAY_PAYMENT_TYPE,
                        'label' => __('Invoice')
                    ]
        ];
    }
}
