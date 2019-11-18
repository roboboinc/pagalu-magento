<?php

namespace Magento\PagaLuPaymentGateway\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $scopeConfig;
    public $order;
    public $modelOrder;
    public $cart;
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Model\Order $modelOrder,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\ObjectManagerInterface $_objectManager
    ) {
        $this->order            = $order;
        $this->modelOrder = $modelOrder;
        $this->cart             = $cart;
        $this->scopeConfig      = $scopeConfig;
        $this->_objectManager   = $_objectManager;
    }

    public function getPostData()
    {

        // get checkout object
        $checkout = $this->_objectManager->get('Magento\Checkout\Model\Type\Onepage')->getCheckout();

        // get order object
        $this->order->loadByIncrementId($checkout->getLastRealOrder()->getEntityId());


        // get extra order data
        $orderData = $this->modelOrder->load($checkout->getLastRealOrder()->getEntityId());

        // get merchant ID
        $merchantId = $this->scopeConfig->getValue(
            'payment/pagalu/merchant_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        // get username
        $username = $this->scopeConfig->getValue(
            'payment/pagalu/username',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        // get password
        $password = $this->scopeConfig->getValue(
            'payment/pagalu/password',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        // get confirm url
        $confirm = $this->scopeConfig->getValue(
            'payment/pagalu/confirm',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        // get cancel url
        $cancel = $this->scopeConfig->getValue(
            'payment/pagalu/cancel',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        // get additional information NOTE: We currently have no need for installments!!
//        $ai = $this->order->getPayment()->getAdditionalInformation();
//
//        // installments
//        if (isset($ai['installments'])) {
//            $orderInstallments = $ai['installments'];
//        } else {
//            $orderInstallments = '';
//        }

        // get oder id
        $orderId = $checkout->getLastRealOrderId();

        // set order description as #ORDERID
        $orderDesc = __('#').$checkout->getLastRealOrderId();

        // get order amount
        $orderAmount = round($this->order->getData('base_grand_total'), 2);

        // get order currency code e.g. EUR
        $currency = strtoupper($orderData->getOrderCurrencyCode());

        // get customer's email
        $payerEmail = $orderData->getCustomerEmail();

        // create request  TODO: Create a Json request here, wait for response URL and return this as the URL
        $ticketRequest = [
            'mid'=>$merchantId,
            'orderid'=>$orderId,
            'orderDesc'=>$orderDesc,
            'orderAmount'=>$orderAmount,
            'currency'=> $currency,
            'payerEmail'=>$payerEmail,
            'confirmUrl'=>$confirm,
            'cancelUrl'=>$cancel,
            'digest'=>base64_encode(sha1($merchantId.$orderId.$orderDesc.$orderAmount.$currency.$payerEmail.$confirm.$cancel.$password, true))
        ];

        return $ticketRequest;
    }

    // TODO: add support for installments
    public function getInstallments()
    {
        return $this->scopeConfig->getValue(
            'payment/pagalu/installments',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    // TODO: add support for installments
    public function getAvailableInstallments()
    {
        $available = array();
        $installments = $this->getInstallments();
//
//        if (strpos($installments, ";")!==false) {
//            $bgt = $this->cart->getQuote()->getData('base_grand_total');
//            $installments = explode(";", $installments);
//            foreach ($installments as $inst) {
//                $inst = explode(":", $inst);
//                if ($inst[0] <= $bgt) {
//                    array_push($available, $inst[1]);
//                }
//            }
//            return $available;
//        } else {
            return [];
//        }
    }

    // get PagaLu's post url
    public function getPostUrl()
    {
        return "http://binrequest.herokuapp.com/1aiacg81";  // TODO: re configure dynamic post URL?
//            $this->scopeConfig->getValue(
//            'payment/pagalu/post_url',
//            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
//        );
    }
}
