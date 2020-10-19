<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to https://www.fcamara.com.br/ for more information.
 *
 * @category  FCamara
 * @package   FCamara_
 * @copyright Copyright (c) 2020 FCamara Formação e Consultoria
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */

namespace FCamara\Getnet\Gateway\Request\CreditCard;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use FCamara\Getnet\Model\Config\SellerConfig;
use FCamara\Getnet\Model\SellerFactory;
use FCamara\Getnet\Model\Seller\SellerClient;
use FCamara\Getnet\Model\Seller\SellerClientPj;

class MarketplaceSubSellerPaymentsBuild implements BuilderInterface
{
    public const STATUS_APPROVED_TRANSACT = 'Aprovado Transacionar';
    public const STATUS_APPROVED_TRANSACT_TO_ANTICIPATE = 'Aprovado Transacionar e Antecipar';
    public const STATUS_APPROVED = 'Aprovado';

    /**
     * @var SellerFactory
     */
    protected $seller;

    /**
     * @var SellerClient
     */
    protected $clientPf;

    /**
     * @var SellerClientPj
     */
    protected $clientPj;

    /**
     * @var SellerConfig
     */
    protected $sellerConfig;

    /**
     * MarketplaceSubSellerPaymentsBuild constructor.
     * @param SellerConfig $sellerConfig
     * @param SellerFactory $seller
     * @param SellerClient $clientPf
     * @param SellerClientPj $clientPj
     */
    public function __construct(
        SellerConfig $sellerConfig,
        SellerFactory $seller,
        SellerClient $clientPf,
        SellerClientPj $clientPj

    ) {
        $this->sellerConfig = $sellerConfig;
        $this->seller = $seller;
        $this->clientPf = $clientPf;
        $this->clientPj = $clientPj;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        if (!$this->sellerConfig->isEnabled()) {
            return [];
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $order = $paymentDO->getOrder();
        $sellers = [];
        $subSellerSalesAmount = [];
        $response = [];

        foreach ($order->getItems() as $item) {
            if ($item->getPrice() <= 0) {
                continue;
            }

            $product = $item->getProduct();

            if ($product->getSellerId()) {
                $sellerData = $this->seller->create()->loadBySubSellerId($product->getSellerId());

                if (!$this->checkSellerIsApproved($sellerData)) {
                    continue;
                }

                $sellers[$product->getSellerId()]['order_items'][] = [
                    'amount' => (($item->getPrice() - $item->getDiscountAmount()) * $item->getQtyOrdered()) * 100,
                    'currency' => 'BRL',
                    'id' => $product->getId(),
                    'description' => $product->getName()
                ];
            }
        }

        foreach ($sellers as $sellerId => $seller) {
            $amount = 0;

            foreach ($seller['order_items'] as $orderItem) {
                $amount += $orderItem['amount'];
                $subSellerSalesAmount[$sellerId] = ['subseller_sales_amount' => $amount];
            }

            $response['marketplace_subseller_payments'][] = [
                'subseller_sales_amount' => (int) ceil($subSellerSalesAmount[$sellerId]['subseller_sales_amount']),
                'subseller_id' => $sellerId,
                'order_items' => $sellers[$sellerId]['order_items']
            ];
        }

        return $response;
    }

    /**
     * @param $seller
     * @return bool
     */
    protected function checkSellerIsApproved($seller)
    {
        $result = false;

        if ($seller['type'] == 'PF') {
            $pfCallback = $this->clientPf->pfCallback($seller['merchant_id'], $seller['legal_document_number']);

            if (
                $pfCallback
                && (
                    $pfCallback['status'] == self::STATUS_APPROVED
                    || $pfCallback['status'] == self::STATUS_APPROVED_TRANSACT
                    || $pfCallback['status'] == self::STATUS_APPROVED_TRANSACT_TO_ANTICIPATE
                )
            ) {
                $result = true;
            }
        }

        if ($seller['type'] == 'PJ') {
            $pjCallback = $this->clientPj->pjCallback($seller['merchant_id'], $seller['legal_document_number']);

            if (
                $pjCallback
                && (
                    $pjCallback['status'] == self::STATUS_APPROVED
                    || $pjCallback['status'] == self::STATUS_APPROVED_TRANSACT
                    || $pjCallback['status'] == self::STATUS_APPROVED_TRANSACT_TO_ANTICIPATE
                )
            ) {
                $result = true;
            }
        }

        return $result;
    }
}
