<?php

/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */

namespace Apexx\Googlepay\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Apexx\Googlepay\Helper\Data as GooglePayHelper;
use Magento\Sales\Model\Order;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Magento\Payment\Gateway\Helper\SubjectReader;

/**
 * Class AuthorizationRequest
 * @package Apexx\Googlepay\Gateway\Request
 */
class AuthorizationRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var GooglePayHelper
     */
    protected $googlepayHelper;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * AuthorizationRequest constructor.
     * @param ConfigInterface $config
     * @param GooglePayHelper $googlepayHelper
     * @param ApexxBaseHelper $apexxBaseHelper
     * @param SubjectReader $subjectReader
     */
    public function __construct(
            ConfigInterface $config,
            GooglePayHelper $googlepayHelper,
            ApexxBaseHelper $apexxBaseHelper,
            SubjectReader $subjectReader    

    ) {
        $this->config = $config;
        $this->googlepayHelper = $googlepayHelper;
        $this->apexxBaseHelper = $apexxBaseHelper;
        $this->subjectReader = $subjectReader;

    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject) {

        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $payment */
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();
        
        $delivery = $order->getShippingAddress();
        $billing = $order->getBillingAddress();
        $amount = $buildSubject['amount']*100;

        $formFields = [];

        $requestData = [
            //"organisation" => $this->googlepayHelper->getOrganizationId(),
            //"account" => $this->googlepayHelper->getAccountId(),
            "account" => '79da6583ac01482f9061bbe1136b90c9',
            "amount" => $amount,
            "capture_now" => $this->googlepayHelper->getGooglepayPaymentAction(),
            "customer_ip" => $order->getRemoteIp(),
            "dynamic_descriptor" => $this->googlepayHelper->getDynamicDescriptor(),
            "merchant_reference" => 'JOURNEYBOX'.$order->getOrderIncrementId(),
            "recurring_type" => $this->googlepayHelper->getRecurringType(),
            "user_agent" => $this->apexxBaseHelper->getUserAgent(),
            "webhook_transaction_update" => $this->googlepayHelper->getWebhookUrl(),
            "currency" => $order->getCurrencyCode(),
            "shopper_interaction" => $this->googlepayHelper->getShopperInteraction(),
            "three_ds" => [
                "three_ds_required" => $this->googlepayHelper->getThreeDsRequired(),
            ],
            "billing_address" => [
                "first_name" => $billing->getFirstname(),
                "last_name" => $billing->getLastname(),
                "email" => $billing->getEmail(),
                "address" => $billing->getStreetLine1() . '' . $billing->getStreetLine2(),
                "city" => $billing->getCity(),
                "state" => $billing->getRegionCode(),
                "postal_code" => $billing->getPostcode(),
                "country" => $billing->getCountryId()
            ],
            "customer" => [
                "customer_id" => $order->getCustomerId(),
                "last_name" => $delivery->getLastname(),
                "postal_code" => $delivery->getPostcode(),
                "account_number" => $order->getCustomerId(),
                "date_of_birth" => $this->googlepayHelper->getCustomerDateOfBirth()
            ]
        ];

        $formFields['card']['googlepay']['cryptogram'] =  $payment->getAdditionalInformation("cryptogram");
        $formFields['card']['googlepay']['expiry_month'] = $payment->getAdditionalInformation("encrypted_data");
        $formFields['card']['googlepay']['expiry_year'] = $payment->getAdditionalInformation("expiry_year");
        $formFields['card']['googlepay']['dpan'] = $payment->getAdditionalInformation("dpan");
        $formFields['card']['googlepay']['eci'] = $payment->getAdditionalInformation("eci");

        $finalRequest = array_merge($requestData, $formFields);

       return $finalRequest;
    }
}
