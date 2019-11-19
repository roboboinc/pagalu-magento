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
       $this->$helper = $_helper;
      
      }

    /**
     * View  page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */public
    public function execute()
    {

       
       $result = $this->resultJsonFactory->create();
       $data = ['message' => $this->helper->processPagaluPayment()];

        return $result->setData($data);
    }
}