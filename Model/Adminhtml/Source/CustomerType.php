<?php
/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */
namespace Apexx\Googlepay\Model\Adminhtml\Source;

/**
 * Class CustomerType
 * @package Apexx\Googlepay\Model\Adminhtml\Source
 */
class CustomerType
{
    /**
     * Different customer type.
     */
    const CUSTOMER_CATEGORY = 'Person';

    public function toOptionArray()
    {
        return [
                    [
                        'value' => self::CUSTOMER_CATEGORY,
                        'label' => __('Person')
                    ]
        ];
    }
}
