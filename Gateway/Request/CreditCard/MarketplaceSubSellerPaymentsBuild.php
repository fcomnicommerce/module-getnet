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

class MarketplaceSubSellerPaymentsBuild implements BuilderInterface
{
    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $order = $paymentDO->getOrder();
        $sellers = [];

        foreach ($order->getItems() as $item) {
            if ($item->getData('seller_id') && $item->getData('price') > 0) {
                $sellers[$item->getData('seller_id')] = [
                    'price' => $item->getData('price'),
                    'discount_amount' => $item->getData('discount_amount'),
                    'sku' => $item->getData('sku'),
                    'name' => $item->getData('name'),
                    'qty_ordered' => $item->getData('qty_ordered')
                ];
            }
        }

        $response = [
            'marketplace_subseller_payments' => [
                'subseller_sales_amount' => 10202,
                'subseller_id' => 10,
                'order_items' => [
                    'amount' => 10202,
                    'currency' => 'BRL',
                    'id' => 1,
                    'description' => 'Descrição do Item/Produto'
                ]
            ]
        ];

        return $response;
    }
}
