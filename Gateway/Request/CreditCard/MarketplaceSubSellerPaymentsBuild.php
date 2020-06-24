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

class MarketplaceSubSellerPaymentsBuild implements BuilderInterface
{
    /**
     * @var SellerConfig
     */
    protected $sellerConfig;

    /**
     * MarketplaceSubSellerPaymentsBuild constructor.
     * @param SellerConfig $sellerConfig
     */
    public function __construct(SellerConfig $sellerConfig)
    {
        $this->sellerConfig = $sellerConfig;
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
                $sellers[$product->getSellerId()]['order_items'][] = [
                    'amount' => (($item->getPrice() - $item->getDiscountAmount()) * $item->getQtyOrdered()) * 100,
                    'currency' => 'BRL',
                    'id' => $product->getId(),
                    'description' => $product->getName()
                ];
            }
        }

        $amount = 0;
        foreach ($sellers as $sellerId => $seller) {
            foreach ($seller['order_items'] as $orderItem) {
                $amount += $orderItem['amount'];
                $subSellerSalesAmount[$sellerId] = ['subseller_sales_amount' => $amount];
            }

            $response['marketplace_subseller_payments'][] = [
                'subseller_sales_amount' => (int) $subSellerSalesAmount[$sellerId]['subseller_sales_amount'],
                'subseller_id' => $sellerId,
                'order_items' => $sellers[$sellerId]['order_items']
            ];
        }

        return $response;
    }
}
