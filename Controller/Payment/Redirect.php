<?php

namespace Magento\PagaLuPaymentGateway\Controller\Payment;

class Redirect extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Loading...'));

        // TODO: handle getting correct redirect URL from server in background ??

        $block = $resultPage->getLayout()
                ->createBlock('Magento\PagaLuPaymentGateway\Block\Payment\Redirect')
                ->setTemplate('Magento_PagaLuPaymentGateway::payment/redirect.phtml')
                ->toHtml();
        $this->getResponse()->setBody($block);
        return $this->resultPageFactory->create();
    }
}
