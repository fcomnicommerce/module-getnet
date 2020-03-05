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
use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * Successaction constructor.
     * @param Context $context
     * @param QuoteManagement $quoteManagement
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $quoteRepository
     * @param PageFactory $pageFactory
     * @param StoreManagerInterface $storeManager
     * @param OrderSender $orderSender
     */
    public function __construct(
        Context $context,
        QuoteManagement $quoteManagement,
        Session $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        PageFactory $pageFactory,
        StoreManagerInterface $storeManager,
        OrderSender $orderSender
    ) {
        $this->quoteManagement = $quoteManagement;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->pageFactory = $pageFactory;
        $this->storeManager = $storeManager;
        $this->orderSender = $orderSender;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $session = $this->checkoutSession;
        $quote = $session->getQuote();
        $result = false;

        if (!$quote->getId()) {
            return $this->_redirect('checkout/cart');
        }

        try {
            $quote->getPayment()->importData(['method' => 'getnet_checkout_iframe']);
            $quote->collectTotals();
            $this->quoteRepository->save($quote);
            $order = $this->quoteManagement->submit($quote);

            $session
                ->setLastQuoteId($quote->getId())
                ->setLastSuccessQuoteId($quote->getId())
                ->clearHelperData();

            if ($order->getEntityId()) {
                $this->_eventManager->dispatch(
                    'checkout_type_onepage_save_order_after',
                    ['order' => $order, 'quote' => $quote]
                );

                /**
                 * a flag to set that there will be redirect to third party after confirmation
                 */
                $redirectUrl = $quote->getPayment()->getOrderPlaceRedirectUrl();

                /**
                 * we only want to send to customer about new order when there is no redirect to third party
                 */
                if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
                    //$this->orderSender->send($order);
                }

                // add order information to the session
                $session
                    ->setLastOrderId($order->getId())
                    ->setRedirectUrl($redirectUrl)
                    ->setLastRealOrderId($order->getIncrementId())
                    ->setLastOrderStatus($order->getStatus());

                $this->_eventManager->dispatch(
                    'checkout_submit_all_after',
                    [
                        'order' => $order,
                        'quote' => $quote
                    ]
                );
            } else {
                throw new \Exception('Error in create order!');
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $this->_redirect('checkout/cart');
        }

        return $this->pageFactory->create();
    }
}
