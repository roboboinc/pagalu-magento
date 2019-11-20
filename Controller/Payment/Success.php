<?php

namespace Magento\PagaLuPaymentGateway\Controller\Payment;

use Magento\Sales\Model\Order;

class Success extends \Magento\Framework\App\Action\Action
{
    public $context;
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
        \Magento\Sales\Api\TransactionRepositoryInterface $transactionRepository
    ) {
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
        // try {
        //     // parse GET data
        //     $order_id = $this->request->getParams();
        //     $order_id = (int) $order_id['order_id'];

        //     if (isset($order_id)) {
        //         $this->_order->loadByIncrementId($order_id);
        //         $this->_order->setState($this->_order->getState())->save();
        //         $this->_order->setStatus('complete')->save();
        //         $this->_order->addStatusToHistory($this->_order->getStatus(), __('Payment successful. Transaction ID: '))->save();
        //         $this->_order->save();
        //         $this->_redirect('checkout/onepage/success');
        //     } else {
        //         $this->_redirect('/');
        //     }
        // } catch (Exception $e) {
        // 	echo $e;
        // }

        $this->updateTransactionOnPagalu();
    }

    public function updateTransactionOnPagalu($payment_uuid='fb67d2e0-6702-4dd6-ac34-09387fd7eb6a') {
        $ch = curl_init();
        $authorization = "Authorization: Bearer jM5RnJbV3604p5DamkuUvVIQSIepsv";
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

        $json = json_encode($server_output);

        if (json_last_error() == JSON_ERROR_NONE) {
            if(isset($json['status'])){
                $order_id = $json['reference'];

                echo $order_id;
            }else {
                echo "Nao tem status? ".$json;
            }

        }else {
            echo "tem erros graves... ".$json;
        }

    }
}
