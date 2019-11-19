<?php

namespace Magento\PagaLuPaymentGateway\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public $scopeConfig;
    public $order;
    public $modelOrder;
    public $cart;
    protected $_objectManager;
    protected $_curl;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Model\Order $modelOrder,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\ObjectManagerInterface $_objectManager,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        $this->order            = $order;
        $this->modelOrder = $modelOrder;
        $this->cart             = $cart;
        $this->scopeConfig      = $scopeConfig;
        $this->_objectManager   = $_objectManager;
        $this->_curl = $curl;
    }

    public function getPostData()
    {

        // get checkout object
        $checkout = $this->_objectManager->get('Magento\Checkout\Model\Type\Onepage')->getCheckout();
        //print_r($this->_objectManager);
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

        $url = $this -> processPagaluPayment();
        return str($url);
        //return "http://binrequest.herokuapp.com/1aiacg81";  // TODO: re configure dynamic post URL?
//            $this->scopeConfig->getValue(
//            'payment/pagalu/post_url',
//            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
//        );
    }

    public function processPagaluPayment(){

        $redirect_url_ = '';

        $params                              = array();
        $params[ 'value' ]                   = '24';
        $params[ 'reference' ]               = '0090972';
        $params[ 'success_url' ]             = 'http://localhost:8080'; //url where IPN messages will be sent after purchase, then validate in the ipn() method
        $params[ 'reject_url' ]              = 'http://localhost:4300'; //url where IPN messages will be sent after purchase, then validate in the ipn() method
        $params[ 'order_status_update_url' ] = 'http://pagalu.co'; //url where IPN messages will be sent after purchase, then validate in the ipn() method
        $params[ 'origin_request_url' ]      = 'http://localhost:8080/pagalu/payment/'; //url where users will be sent after purchase process
        $params[ 'extras' ]                  = 'Test PagaLu';
        $params[ 'phone_number' ]            = '848077430';
        $params[ 'client_contact']           = '848077430';
        $params[ 'email' ]                   = 'jr.pelembeobadias@gmail.com';


        //if ( $this->mode == 'yes' ) {
        //    $this->pagalu_url = $this->pagalu_sandbox_url;
       // }


        //$ch = curl_init();
        $params = json_encode($params); // Json encodes $params array
        
        $this->_curl->addHeader("Authorization: Bearer 5xBCgKMv8WMxJUo4PXwlfjTqISuosu");
        $this->_curl->addHeader("Content-Type", "application/json");
        $this->_curl->post('http://sandbox.pagalu.co.mz/pagamento-ext/api/pay-ext/', $params);

        $response = $this->_curl->getBody();

        return $response;
    }   
}
