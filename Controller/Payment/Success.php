<?php

namespace Magento\PagaLuPaymentGateway\Controller\Payment;

use Magento\Sales\Model\Order;

class Success extends \Magento\Framework\App\Action\Action
{
    public $context;
    protected $_invoiceService;
    protected $order;
    protected $transaction;
    protected $request;
    protected $transactionRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\Service\InvoiceService $_invoiceService,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository
    ) {
        $this->_invoiceService = $_invoiceService;
        $this->transaction    = $transaction;
        $this->order          = $order;
        $this->context         = $context;
        $this->request = $request;
        $this->transactionRepository = $transactionRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        return $this->_redirect('/');
        try {
            // parse GET data
            $order_id = $this->getRequest()->getParams('order_id');

            if (isset($order_id)) {
                $this->_order->loadByIncrementId(1);
                $this->_order->setState($this->_order->getState())->save();
                $this->_order->setStatus('complete')->save();
                $this->_order->addStatusToHistory($this->_order->getStatus(), __('Payment successful. Transaction ID: '))->save();
                $this->_order->save();
                $this->_redirect('checkout/onepage/success');
            } else {
                $this->_redirect('/');
            }
        } catch (Exception $e) {
        	echo $e;
        }

//        try {
//
//            $postData = $this->getRequest()->getPostValue();
//
//            // if data looks fine
////            if (isset($postData['orderid']) && isset($postData['paymentRef'])) {
//
//                // get object manager
//                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//
//                // set order status
//                $this->order->loadByIncrementId($postData['orderid']);
//                $this->order->setState($this->order->getState())->save();
//                $this->order->setStatus('payment_review')->save();
//                $this->order->addStatusToHistory($this->order->getStatus(), __('Payment successful. Transaction ID: ') . $postData['paymentRef'])->save();
//                $this->order->save();
//
//                // send order email
//                $emailSender = $objectManager->create('\Magento\Sales\Model\Order\Email\Sender\OrderSender');
//                $emailSender->send($this->order);
//
//                // redirect to success page
//                $this->_redirect('checkout/onepage/success');
////            } else {
////                $this->_redirect('/');
////            }
//        } catch (Exception $e) {
//            echo $e;
//        }
    }
}
