<?php

namespace Magento\PagaLuPaymentGateway\Controller\Payment;

class Cancel extends \Magento\Framework\App\Action\Action
{

    public $context;
    protected $_helper;
    protected $_invoiceService;
    protected $_order;
    protected $request;
    protected $transaction;
    protected $transactionRepository;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\Service\InvoiceService $_invoiceService,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository,
        \Magento\PagaLuPaymentGateway\Helper\Data $_helper
    ) {
        $this->_helper         = $_helper;
        $this->_invoiceService = $_invoiceService;
        $this->_order          = $order;
        $this->context         = $context;
        $this->request         = $request;
        $this->transaction     = $transaction;
        $this->transactionRepository = $transactionRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            // parse GET data
            $parameters = $this->request->getParams();
            $payment_uuid = $parameters['payment_uuid'];

            if (isset($payment_uuid)) {
                $this->getTransactionStatusOnPagalu($payment_uuid);
            }else {
                $this->_redirect('/');
            }
        } catch (Exception $e) {
        	echo $e;
        }
    }

    public function updateTransactionOnMagento($order_id=1){
        $orderId = (int)$order_id;

        $this->_order->loadByIncrementId($orderId);
        $this->_order->setState($this->_order->getState())->save();
        $this->_order->setStatus('canceled')->save();
        $this->_order->addStatusToHistory($this->_order->getStatus(), __('Payment successful. Transaction ID: '))->save();
        $this->_order->save();
        $this->_redirect('checkout/onepage/failure');
    }

    public function getTransactionStatusOnPagalu($payment_uuid='') {
        $ch = curl_init();
        $authorization = "Authorization: Bearer ".$this->_helper->pagalu_api_key();

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_POSTREDIR, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, 'http://sandbox.pagalu.co.mz/pagamento-ext/api/pay-ext/'.$payment_uuid);

        $server_output = curl_exec ($ch);
        //close connection
        curl_close ($ch);
        flush();

        $json = json_decode($server_output, true);

        if (json_last_error() == JSON_ERROR_NONE) {
            if(isset($json['status'])){
                $order_id = (int)$json['reference'];
                $this->updateTransactionOnMagento($order_id);
            }else {
                echo "Nao tem status: ".$json;
            }

        }else {
            echo "tem erros graves... ".$json;
        }

    }
}
