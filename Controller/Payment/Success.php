<?php

namespace Magento\PagaLuPaymentGateway\Controller\Payment;

use Magento\Sales\Model\Order;

class Success extends \Magento\Framework\App\Action\Action
{
    public $context;
    protected $_invoiceService;
    protected $_order;
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
        try {
            // parse GET data
            $order_id = $this->request->getRequest()->getParams('order_id');

            if (isset($order_id)) {
                $this->_order->loadByIncrementId($order_id);
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
    }
}
