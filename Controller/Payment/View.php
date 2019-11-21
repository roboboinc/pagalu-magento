<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * This is a test page to debug and verify that views are accessible
 */
namespace Magento\PagaLuPaymentGateway\Controller\Payment;
class View extends \Magento\Framework\App\Action\Action
{
    protected $_helper;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
       \Magento\PagaLuPaymentGateway\Helper\Data $_helper,
       \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
       \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory)
    {
       $this->_helper          = $_helper;
       $this->resultJsonFactory = $resultJsonFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
       parent::__construct($context);}
    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */

    // get REDIRECT URL FROM PAGALU
    public function getEndpointFromPagaLu(){

        $result = $this->resultJsonFactory->create();
       $data = $this->_helper->getPostData();   //['message' => $this->_helper->getPostData()];   //'Hello world!'

        $ch = curl_init();
        $params = json_encode($data); // Json encodes $params array
        $authorization = "Authorization: Bearer ";
        $authorization .=  $this->_helper->pagalu_api_key();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $this->_helper->getPostUrl());
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        // receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        //close connection
        curl_close ($ch);
        flush();

        $json = json_decode($server_output, true);

        try{
            if (json_last_error() == JSON_ERROR_NONE) {
                // SUccess return Redirect to PagaLu
                echo "Passa";

                // $json_url = $json;

                // return $json_url;

            } else {
                //return FAIL URL internally
                // TODO: Fix handling failure
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl('/pagalu/payment/failure/');

                return $resultRedirect;

            }
        } catch (exception $e) {
            //In Case Auth details are not provided
            $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setUrl('/pagalu/payment/failure/');
        }
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = ['url' => $this->getEndpointFromPagaLu()];

        return $result->setData($data);

    }
}
