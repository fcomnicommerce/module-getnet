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
use Magento\Framework\View\Result\PageFactory;
use mysql_xdevapi\Exception;

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

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * Successaction constructor.
     * @param Context $context
     * @param QuoteManagement $quoteManagement
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        QuoteManagement $quoteManagement,
        Session $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        PageFactory $pageFactory
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->pageFactory = $pageFactory;

        parent::__construct($context);
    }


    public function execute()
    {
        $quote = $this->checkoutSession->getQuote();
        $result = false;

        if (!$quote->getId()) {
            return $this->_redirect('checkout/cart');
        }

        try {
            $quote->getPayment()->importData(['method' => 'getnet_checkout_iframe']);
            $quote->collectTotals();
            $this->quoteRepository->save($quote);
            $order = $this->quoteManagement->submit($quote);
            $order->setEmailSent(1);

            if ($order->getEntityId()) {
                $this->_eventManager->dispatch(
                    'checkout_onepage_controller_success_action',
                    [
                        'order_ids' => [$quote->getLastOrderId()],
                        'order' => $quote->getLastRealOrder()
                    ]
                );
            } else {
                throw new Exception('Error in create order!');
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->_redirect('checkout/cart');
        }

        return $this->pageFactory->create();
    }
}
