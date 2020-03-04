<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to https://www.fcamara.com.br/ for more information.
 *
 * @category  FCamara
 * @package   FCamara_Getnet
 * @copyright Copyright (c) 2020 Getnet
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */

namespace FCamara\Getnet\Controller\CheckoutIframe;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Quote\Model\QuoteManagement;
use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;

class Successaction extends Action
{
    /**
     * @var QuoteManagement
     */
    private $quoteManagement;

    /**
     * @var Session
     */
    private $checkoutSession;

    private $quoteRepository;

    /**
     * Successaction constructor.
     * @param Context $context
     * @param QuoteManagement $quoteManagement
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        Context $context,
        QuoteManagement $quoteManagement,
        Session $checkoutSession,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;

        parent::__construct($context);
    }


    public function execute()
    {
        $quote = $this->checkoutSession->getQuote();
        $quote->getPayment()->importData(['method' => 'getnet_checkout_iframe']);
        $quote->collectTotals();
        $this->quoteRepository->save($quote);
        $order = $this->quoteManagement->submit($quote);

        $order->setEmailSent(0);
        $incrementId = $order->getRealOrderId();

        if ($order->getEntityId()) {
            $result['order_id'] = $order->getRealOrderId();
        } else {
            $result = ['error' => 1, 'msg' => 'Your custom message'];
        }

        $this->_redirect('checkout/onepage/success');
    }
}
