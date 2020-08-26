<?php
/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */
namespace Apexx\Googlepay\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\HTTP\Client\Curl;
use Apexx\Base\Helper\Data as ApexxBaseHelper;
use Apexx\Googlepay\Helper\Data as GooglePayHelper;
use Apexx\Base\Helper\Logger\Logger as CustomLogger;

/**
 * Class RefundSale
 * @package Apexx\Googlepay\Gateway\Http\Client
 */
class RefundSale implements ClientInterface
{
    const SUCCESS = 1;
    const FAILURE = 0;

    /**
     * @var array
     */
    private $results = [
        self::SUCCESS,
        self::FAILURE
    ];

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var GooglePayHelper
     */
    protected  $googlepayHelper;

    /**
     * @var ApexxBaseHelper
     */
    protected  $apexxBaseHelper;

    /**
     * @var CustomLogger
     */
    protected $customLogger;

    /**
     * RefundSale constructor.
     * @param Curl $curl
     * @param ApexxBaseHelper $apexxBaseHelper
     * @param GooglePayHelper $googlepayHelper
     * @param CustomLogger $customLogger
     */
    public function __construct(
        Curl $curl,
        ApexxBaseHelper $apexxBaseHelper,
        GooglePayHelper $googlepayHelper,
        CustomLogger $customLogger
    ) {
        $this->curlClient = $curl;
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

        // Set refund url
        $url = $this->apexxBaseHelper->getApiEndpoint().'refund/'.$request['transactionId'];

        //Set parameters for curl
          unset($request['transactionId']);
        $resultCode = json_encode($request);
        $response = $this->apexxBaseHelper->getCustomCurl($url, $resultCode);

        $resultObject = json_decode($response);
        $responseResult = json_decode(json_encode($resultObject), True);

        $this->customLogger->debug('Googlepay Refund Request:', $request);
        $this->customLogger->debug('Googlepay Refund Response:', $responseResult);

        return $responseResult;
    }
}
