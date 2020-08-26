<?php
/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */
namespace Apexx\Googlepay\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Model\Order\Payment;
use Apexx\Googlepay\Helper\Data as GooglePayHelper;
use Apexx\Base\Helper\Data as ApexxBaseHelper;

/**
 * Class RefundDataBuilder
 * @package Apexx\Googlepay\Gateway\Request
 */
class RefundDataBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var GooglePayHelper
     */
    protected  $googlepayHelper;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * RefundDataBuilder constructor.
     * @param SubjectReader $subjectReader
     * @param GooglePayHelper $googlepayHelper
     */
    public function __construct(
        SubjectReader $subjectReader,
        GooglePayHelper $googlepayHelper,
        ApexxBaseHelper $apexxBaseHelper
    )
    {
        $this->subjectReader = $subjectReader;
        $this->googlepayHelper = $googlepayHelper;
        $this->apexxBaseHelper = $apexxBaseHelper;
    }

    /**
     * @param array $buildSubject
     * @return array
     */

     public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        /** @var Payment $orderPayment */
        $orderPayment = $paymentDO->getPayment();

        // Send Parameters to Paypal Payment Client
        $order = $paymentDO->getOrder();
        $amount = $buildSubject['amount'];

        //Get last transaction id for authorization
        $lastTransId = $this->apexxBaseHelper->getHostedPayTxnId($order->getId());

        if ($lastTransId != '') {
            $requestData = [
                "transactionId" => $lastTransId,
                "amount" => ($amount * 100),
                "reason" => time()."-".$order->getOrderIncrementId(),
                "capture_id" => $orderPayment->getParentTransactionId()
            ];
        } else {
            $requestData = [
                "transactionId" => $orderPayment->getParentTransactionId(),
                "amount" => ($amount * 100),
                "reason" => time()."-".$order->getOrderIncrementId()
            ];
        }

        return $requestData;
    }
}
