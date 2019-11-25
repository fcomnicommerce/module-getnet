<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace FCamara\Getnet\Block\Billet;

class Success extends \Magento\Checkout\Block\Success
{
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $orderFactory, $data);
    }

    /**
     * @return int
     */
    public function getRealOrderId()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_orderFactory->create()->load($this->getLastOrderId());
        return $order->getIncrementId();
    }

    public function isBillet()
    {
        $order = $this->getRealOrderId();
        if ($order->getPayment()->getMethodInstance()->getCode() ==  \FCamara\Getnet\Model\Ui\Billet\ConfigProvider::CODE) {
            return true;
        }
        return false;
    }

    public function getPaymentInfo()
    {
        $order = $this->getRealOrderId();

        return $order->getPayment()->getAdditionalInformation('return');
    }
}
