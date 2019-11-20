<?php

namespace Magento\PagaLuPaymentGateway\Helper;

use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    //Get Global Config variables
    const XML_PATH_PAGALU = 'payment/';

	public function getConfigValue($field, $storeId = null)
	{
		return $this->scopeConfig->getValue(
			$field, ScopeInterface::SCOPE_STORE, $storeId
		);
	}

	public function getGeneralConfig($code, $storeId = null)
	{

		return $this->getConfigValue(self::XML_PATH_PAGALU .'sample_gateway/'. $code, $storeId);
	}

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $session;
    protected $_order;
    protected $_customerSession;

    public $scopeConfig;
    public $order;
    public $modelOrder;
    public $cart;
    protected $_objectManager;


    public function __construct(
        // Replace all with this:
        \Magento\Checkout\Model\Session $session,
        \Magento\Checkout\Model\Session $customerSession,

        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Model\Order $modelOrder,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\ObjectManagerInterface $_objectManager
    ) {
        // NEW Session manager
        $this->session = $session;
        $this->_order = $order;
        $this->_customerSession = $customerSession;

        $this->order            = $order;
        $this->modelOrder       = $modelOrder;
        $this->cart             = $cart;
        $this->scopeConfig      = $scopeConfig;
        $this->_objectManager   = $_objectManager;
    }

    public function getOrder(){
        $orderId = $this->_customerSession->getData('last_order_id');

        return $orderId;
    }

    public function getPostData()
    {

        // get checkout object
        $checkout = $this->_objectManager->get('Magento\Checkout\Model\Type\Onepage')->getCheckout();


        // get order object
        $this->order->loadByIncrementId($checkout->getLastRealOrder()->getEntityId());


        // get extra order data
        $orderData = $this->modelOrder->load($checkout->getLastRealOrder()->getEntityId());

        $order_old = $this->session->getLastRealOrder();
        $order = $this->_order->load($this->_customerSession->getLastOrderId());
        $order_id = $this->getOrder(); //$order->getId(); //order ID
        $order_amount = round($order->getGrandTotal(), 2); //Order amount
        $order_currency = $order->getBaseCurrenyCode(); //order currency code
        $customer_email = $order->getCustomerEmail();

        $customer_phone_no = $order->getShippingAddress()->getTelephone();

        $customer_name = $order->getCustomerName(); //customer email ID
        $checkout_session_data = $this->_customerSession->getQuote()->getData(); //checkout session data


        // get oder id
        $orderId = $order->getId();


        // set order description as #ORDERID
        $orderDesc = __('#').$checkout->getLastRealOrderId();

        // get order amount
        $orderAmount = round($this->order->getData('base_grand_total'), 2);

        // get order currency code e.g. EUR
        $currency = strtoupper($orderData->getOrderCurrencyCode());


        // get Success url concat the orderID in GET
        $success_url = $this->getBaseUrl().'pagalu/payment/success'.'/?order_id='.$orderId;

        // get Reject url concat the orderID in GET
        $reject_url = $this->getBaseUrl().'pagalu/payment/failure'.'/?order_id='.$orderId;


        // get customer's email
        $payerEmail = $orderData->getCustomerEmail();

        // create request  TODO: Create a Json request here, wait for response URL and return this as the URL
        $ticketRequest = [
            'reference'=> $order_id,
            'orderDesc'=>$orderDesc,
            'value'=>$order_amount,
            'currency'=> $currency,
            'email'=>$customer_email,
            'phone_number'=>$customer_phone_no,
            'success_url'=>$success_url,
            'reject_url'=>$reject_url,
            'payer_name'=>$customer_name,
            'extras'=>$customer_name,
            'digest'=>base64_encode(sha1($orderId.$orderDesc.$orderAmount.$currency.$payerEmail.$success_url.$reject_url, true))
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

    // static variables
    public function pagalu_api_key(){
        // get PagaLu  API Key
        return $this->getGeneralConfig('pagalu_api_key');
    }

    // get base URL
    public function getBaseUrl()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        return $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    // get PagaLu's post url
    public function getPostUrl()
    {
        // 0 = Production and 1 = Sandbox
        if ($this->getGeneralConfig('debug') == 0) {
            return $this->getGeneralConfig('production_url');
        } else {
            return $this->getGeneralConfig('sandbox_url');

        }
    }
}
