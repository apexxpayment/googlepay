<?php
/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */
namespace Apexx\Googlepay\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use \Magento\Store\Model\ScopeInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Framework\Encryption\EncryptorInterface ;
use \Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Framework\Serialize\Serializer\Json as SerializeJson;
use \Magento\Framework\HTTP\Adapter\CurlFactory;
use \Magento\Framework\HTTP\Header as HttpHeader;
use \Magento\Sales\Model\OrderRepository;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use \Psr\Log\LoggerInterface;

/**
 * Class Data
 * @package Apexx\Googlepay\Helper
 */
class Data extends AbstractHelper
{
    /**
     * Config paths
     */
    const XML_CONFIG_PATH_GOOGLEPAYPAYMENT  = 'payment/googlepay_gateway';
    const XML_PATH_PAYMENT_GOOGLEPAY        = 'payment/apexx_section/apexxpayment/googlepay_gateway';
    const XML_PATH_DYNAMIC_DESCRIPTOR       = '/dynamic_descriptor';
    const XML_PATH_SHOPPER_INTERACTION      = '/shopper_interaction';
    const XML_PATH_3DS_REQ                  = '/three_d_status';
    const XML_PATH_CAPTURE_MODE             = '/capture_mode';
    const XML_PATH_PAYMENT_MODES            = '/payment_modes';
    const XML_PATH_PAYMENT_TYPE             = '/payment_type';
    const XML_PATH_PAYMENT_ACTION           = '/payment_action';
    const XML_PATH_WEBHOOK_URL              = '/webhook_transaction_update';
    const XML_PATH_DATE_OF_BIRTH            = '/date_of_birth';
    const XML_PATH_RECURRING_TYPE           = '/recurring_type';
    const XML_PATH_ALLOW_CURRENCY           = '/allow';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var SerializeJson
     */
    protected $serializeJson;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var HttpHeader
     */
    protected $httpHeader;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var TransactionRepositoryInterface
     */
    protected $transactionRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchBuilder;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Data constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param JsonFactory $resultJsonFactory
     * @param SerializeJson $serializeJson
     * @param CurlFactory $curlFactory
     * @param HttpHeader $httpHeader
     * @param OrderRepository $orderRepository
     * @param TransactionRepositoryInterface $transactionRepository
     * @param SearchCriteriaBuilder $searchBuilder
     * @param FilterBuilder $filterBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        JsonFactory $resultJsonFactory,
        SerializeJson $serializeJson,
        curlFactory $curlFactory,
        HttpHeader $httpHeader,
        OrderRepository $orderRepository,
        TransactionRepositoryInterface $transactionRepository,
        SearchCriteriaBuilder $searchBuilder,
        FilterBuilder $filterBuilder,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor ;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->serializeJson = $serializeJson;
        $this->curlFactory = $curlFactory;
        $this->httpHeader = $httpHeader;
        $this->orderRepository  = $orderRepository;
        $this->transactionRepository = $transactionRepository;
        $this->searchBuilder = $searchBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->logger = $logger;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getConfigPathValue($key)
    {
        return $this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_GOOGLEPAYPAYMENT . $key,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get config value at the specified key
     *
     * @param string $key
     * @return mixed
     */
    public function getConfigValue($key)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PAYMENT_GOOGLEPAY . $key,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getDynamicDescriptor()
    {
        return $this->getConfigPathValue(self::XML_PATH_DYNAMIC_DESCRIPTOR);
    }

    /**
     * @return string
     */
    public function getThreeDsRequired()
    {
        $threeDReq = $this->getConfigValue(self::XML_PATH_3DS_REQ);
        if ($threeDReq) {
            return 'true';
        } else {
            return 'false';
        }
    }

    /**
     * @return mixed
     */
    public function getCaptureMode()
    {
        return $this->getConfigValue(self::XML_PATH_CAPTURE_MODE);
    }

    /**
     * @return mixed
     */
    public function getShopperInteraction()
    {
        return $this->getConfigPathValue(self::XML_PATH_SHOPPER_INTERACTION);
    }

    /**
     * @return string
     */
    public function getCustomPaymentType()
    {
        return $this->getConfigValue(self::XML_PATH_PAYMENT_TYPE);
    }

     /**
     * @return string
     */
    public function getGooglepayPaymentAction()
    {
         $hostPaymentAction = $this->getConfigPathValue(self::XML_PATH_PAYMENT_ACTION);
        if ($hostPaymentAction == 'authorize') {
            return 'false';
        } else {
            return 'true';
        }
    }

    /**
     * @return mixed
     */
    public function getWebhookUrl()
    {
        return $this->getConfigValue(self::XML_PATH_WEBHOOK_URL);
    }

    /**
     * @return mixed
     */
    public function getCustomerDateOfBirth()
    {
        return $this->getConfigValue(self::XML_PATH_DATE_OF_BIRTH);
    }

    /**
     * @return mixed
     */
    public function getRecurringType()
    {
        return $this->getConfigValue(self::XML_PATH_RECURRING_TYPE);
    }

    /**
     * @param $currency
     * @return array
     */
    public function getAllowPaymentCurrency($currency) {
        $allowCurrencyList = $this->getConfigValue(self::XML_PATH_ALLOW_CURRENCY);

        if (!empty($allowCurrencyList)) {
            $currencyList = explode(",", $allowCurrencyList);
            if (!empty($currencyList)) {
                $currencyInfo = [];
                foreach ($currencyList as $key => $value) {
                    if ($value == $currency) {
                        $currencyInfo['currency_code'] = $value;
                    }
                }

                return $currencyInfo;
            }
        }
    }
}
