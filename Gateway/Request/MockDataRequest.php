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
 * @author    Jonatan Santos <jonatan.santos@fcamara.com.br>
 */

namespace FCamara\Getnet\Gateway\Request;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class MockDataRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @param \FCamara\Getnet\Model\ConfigInterface $config
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \FCamara\Getnet\Model\ConfigInterface $config,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $payment = $paymentDO->getPayment();
        $order = $paymentDO->getOrder();

        $transactionResult = $payment->getAdditionalInformation('transaction_result');
        $address = $this->checkoutSession->getQuote()->getBillingAddress();
        $customer = $this->checkoutSession->getQuote()->getCustomer();
        $streetData = $address->getStreet();
        $district = $complement = $number = $street = '';

        if (isset($streetData[0])) {
            $street = $streetData[0];
        }
        if (isset($streetData[1])) {
            $number = $streetData[1];
        }
        if (isset($streetData[2])) {
            $complement = $streetData[2];
        }
        if (isset($streetData[3])) {
            $district = $streetData[3];
        }

        return [
            'body' => [
                'seller_id' => $this->config->sellerId(),
                'amount' => $order->getGrandTotalAmount(),
                'currency' => 'BRL',
                'order' => [
                    'order_id' => $order->getOrderIncrementId(),
                    'sales_tax' => 0,
                    'product_type' => 'service',
                ],
                'boleto' =>[
                    //'our_number' => '000001946598',
                    'document_number' => '170500000019763',
                    'expiration_date' => '30/11/2019',
                    'instructions' => 'Não receber após o vencimento',
                    'provider' => 'santander',
                ],
                'customer' => [
                    'first_name' => $customer->getFirstname(),
                    'name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                    'document_type' => 'CPF',
                    'document_number' => $customer->getTaxvat(),
                    'billing_address' => [
                        'street' => $street,
                        'number' => $number,
                        'complement' => $complement,
                        'district' => $district,
                        'city' => $address->getCity(),
                        'state' => $address->getRegionCode(),
                        'postal_code' => $address->getPostcode(),
                    ],
                ],
            ]
        ];
    }
}
