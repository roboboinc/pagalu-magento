<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PagaLuPaymentGateway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\PaymentInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{

    protected $STATE_PENDING_PAYMENT = 'pending';
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $method = $this->readMethodArgument($observer);
        $data = $this->readDataArgument($observer);

        $paymentInfo = $method->getInfoInstance();

        $paymentInfo->setAdditionalInformation(
                'transaction_result',
                $this->STATE_PENDING_PAYMENT
            );
//        $stateObject->setStatus('pending_payment');

//        if ($data->getDataByKey('transaction_result') !== null) {
//            $paymentInfo->setAdditionalInformation(
//                'transaction_result',
//                $data->getDataByKey('transaction_result')
//            );
//        }
    }
}
