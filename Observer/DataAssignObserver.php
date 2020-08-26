<?php
/**
 * Custom payment method in Magento 2
 * @category    GooglePay
 * @package     Apexx_Googlepay
 */
namespace Apexx\Googlepay\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\LocalizedException;

use Magento\Payment\Model\InfoInterface;

class DataAssignObserver extends AbstractDataAssignObserver{
	
	/**
	 * @param Observer $observer
	 * @return void
	 */
	public function execute(Observer $observer) {
		$data = $this->readDataArgument($observer);
		$paymentInfo = $this->readPaymentModelArgument($observer);
		$additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
		
		
		if ($data->getDataByKey('transaction_result') !== null) {
			$paymentInfo->setAdditionalInformation(
					'transaction_result',
					$data->getDataByKey('transaction_result')
					);
		}
		
		if (!is_array($additionalData)) {
			return;
		}
		
		$paymentInfo->setAdditionalInformation( $additionalData );
		
	}
	
}
