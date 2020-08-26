<?php
/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */
namespace Apexx\Googlepay\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Model\Session As CheckoutSession;
use Apexx\Googlepay\Helper\Data As GooglepayHelper;

/**
 * Class DisabledGooglepayCurrency
 * @package Apexx\Googlepay\Observer
 */
class DisabledGooglepayCurrency implements ObserverInterface
{
    /**
     * @var Session
     */
	protected $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var GooglepayHelper
     */
    protected $googlePayHelper;

    /**
     * DisabledPaypalCurrency constructor.
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param CartRepositoryInterface $quoteRepository
     * @param CheckoutSession $checkoutSession
     * @param GooglepayHelper $googlePayHelper
     */
	public function __construct(
	    Session $customerSession,
        CustomerRepositoryInterface $customerRepositoryInterface,
        CartRepositoryInterface $quoteRepository,
        CheckoutSession $checkoutSession,
        GooglepayHelper $googlePayHelper
    ) {
		$this->customerSession = $customerSession;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->googlePayHelper = $googlePayHelper;
	}

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
	
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $paymentMethod = $observer->getEvent()->getMethodInstance()->getCode();
        $result = $observer->getEvent()->getResult();

        $quoteCurrency = $this->checkoutSession->getQuote()->getQuoteCurrencyCode();
        $allowCurrency = $this->googlePayHelper->getAllowPaymentCurrency($quoteCurrency); 

        if ($this->customerSession->isLoggedIn()) {
            if ($paymentMethod == 'googlepay_gateway') {
                if (!empty($allowCurrency)) {
                    $result->setData('is_available', true);
                    return;
                } else {
                    $result->setData('is_available', false);
                    return;
                }
            }
        } else {
            if ($paymentMethod == 'googlepay_gateway') {
             if (!empty($allowCurrency)) {
                    $result->setData('is_available', true);
                    return;
                } else {
                    $result->setData('is_available', false);
                    return;
                }
            }
        }
    }
}
