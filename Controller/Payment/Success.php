<?php

namespace Magento\PagaLuPaymentGateway\Controller\Payment;

class Success extends \Magento\Framework\App\Action\Action
{
    public $context;
    protected $_invoiceService;
    protected $_order;
    protected $_transaction;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\Service\InvoiceService $_invoiceService,
        \Magento\Sales\Model\Order $_order,
        \Magento\Framework\DB\Transaction $_transaction
    ) {
        $this->_invoiceService = $_invoiceService;
        $this->_transaction    = $_transaction;
        $this->_order          = $_order;
        $this->context         = $context;
        parent::__construct($context);
    }

    public function execute()
    {

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
    }
}
