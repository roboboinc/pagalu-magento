<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * This is a test page to debug and verify that views are accessible
 */
namespace Magento\PagaLuPaymentGateway\Controller\Payment;
class View2 extends \Magento\Framework\App\Action\Action
{
    protected $helper;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
       \Magento\PagaLuPaymentGateway\Helper\Data $_helper)
    {
       $this->resultJsonFactory = $resultJsonFactory;
       parent::__construct($context);
       $this->helper = $_helper;
      
      }

    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {


       $message = $this->current_url();

       $success = $this->success_url();

       $failure = $this->failure_url();
       $result = $this->resultJsonFactory->create();
       //$data = ['message' => str($this->helper->processPagaluPayment())];
        $data['message'] = [$message];
        $data['success_url'] = [$success];
        $data['failure_url'] = [$failure];
      return $result->setData($data);
    }

function current_url()
{
    $url      = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $validURL = str_replace("&", "&amp", $url);
    $ph =  basename($url);
    $path = str_replace($ph,"", $url); // outputs Hello Dolly!

    return $path;
}

function success_url(){

   return $this->current_url().'success';
}

function failure_url(){
   return $this->current_url().'failure';
}
}