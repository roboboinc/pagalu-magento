<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * This is a test page to debug and verify that views are accessible
 */
namespace Magento\PagaLuPaymentGateway\Controller\Payment;
class Debug extends \Magento\Framework\App\Action\Action
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
       \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory)
    {
       $this->_helper          = $_helper;
       $this->resultJsonFactory = $resultJsonFactory;
       parent::__construct($context);}
    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */

    public function execute()
    {
       $result = $this->resultJsonFactory->create();
       $data = $this->_helper->getPostData();   //['message' => $this->_helper->getPostData()];   //'Hello world!'

        return $result->setData($data);
    }
}
