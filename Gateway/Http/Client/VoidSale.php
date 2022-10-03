<?php
/**
 * Custom payment method in Magento 2
 * @category    CcDirect
 * @package     Apexx_CcDirect
 */
namespace Apexx\Googlepay\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\HTTP\Client\Curl;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Apexx\Googlepay\Helper\Data as GooglePayHelper;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;

/**
 * Class VoidSale
 * @package Apexx\Googlepay\Gateway\Http\Client
 */
class VoidSale implements ClientInterface
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

    /**
     * VoidSale constructor.
     * @param ApexxBaseHelper $apexxBaseHelper
     * @param GooglePayHelper $googlepayHelper
     * @param CustomLogger $customLogger
     */
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
     * @param TransferInterface $transferObject
     * @return array|mixed
     */
     public function placeRequest(TransferInterface $transferObject)
    {
        $request = $transferObject->getBody();

        $apiType = $this->apexxBaseHelper->getApiType();
        if ($apiType == 'Atomic') {
            $url = $this->apexxBaseHelper->getApiEndpoint().'cancel/payment/'.$request['transactionId'];
        } else {
            // Set void url
            $url = $this->apexxBaseHelper->getApiEndpoint().$request['transactionId'].'/cancel';
        }
        
        //Set parameters for curl
        unset($request['transactionId']);
        $resultCode = json_encode($request);

        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);
        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);

        $this->customLogger->debug('Googlepay Void Request:', $request);
        $this->customLogger->debug('Googlepay Void Response:', $responseResult);

        return $responseResult;
    }
}
