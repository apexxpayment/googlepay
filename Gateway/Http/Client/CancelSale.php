<?php
/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */
namespace Apexx\Googlepay\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Apexx\Googlepay\Helper\Data as GooglePayHelper;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;

/**
 * Class CancelSale
 * @package Apexx\Googlepay\Gateway\Http\Client
 */
class CancelSale implements ClientInterface
{
    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * @var GooglePayHelper
     */
    protected  $googlepayHelper;

    /**
     * @var CustomLogger
     */
    protected $customLogger;


    public function __construct(
        ApexxBaseHelper $apexxBaseHelper,
        GooglePayHelper $googlepayHelper,
        CustomLogger $customLogger
    ) {
        $this->apexxBaseHelper = $apexxBaseHelper;
        $this->googlepayHelper = $googlepayHelper;
        $this->customLogger = $customLogger;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     */
   public function placeRequest(TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();

        // Set cancel url
        $url = $this->apexxBaseHelper->getApiEndpoint().$request['transactionId'].'/cancel';

        unset($request['transactionId']);
        //Set parameters for curl
        $resultCode = json_encode($request);

        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);
        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);

        $this->customLogger->debug('Googlepay Cancel Request:', $request);
        $this->customLogger->debug('Googlepay Cancel Response:', $responseResult);

        return $responseResult;
    }
}
