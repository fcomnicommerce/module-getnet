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
 * @copyright Copyright (c) 2019 FCamara Formação e Consultoria
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Jonatan Santos <jonatan.santos@fcamara.com.br>
 */

namespace FCamara\Getnet\Gateway\Request\DebitCard;

use FCamara\Getnet\Model\ConfigInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;

class DataRequest implements BuilderInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param ConfigInterface $config
     * @param TimezoneInterface $timezone
     * @param Session $checkoutSession
     */
    public function __construct(
        ConfigInterface $config,
        TimezoneInterface $timezone,
        Session $checkoutSession
    ) {
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
        $this->timezone = $timezone;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Date_Exception
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

        $ccNumberToken = $payment->getAdditionalInformation('cc_number_token');
        $ccName = $payment->getAdditionalInformation('cc_name');
        $ccExpMonth = $payment->getAdditionalInformation('cc_exp_month');
        $ccExpYear = $payment->getAdditionalInformation('cc_exp_year');
        $ccCid = $payment->getAdditionalInformation('cc_cid');
        $ccType = $payment->getAdditionalInformation('cc_type');
        $address = $this->checkoutSession->getQuote()->getBillingAddress();
        $shipping = $this->checkoutSession->getQuote()->getShippingAddress();
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

        $response = [
            'body' => [
                'seller_id' => $this->config->sellerId(),
                'amount' => $order->getGrandTotalAmount(),
                'currency' => 'BRL',
                'order' => [
                        'order_id' => $order->getOrderIncrementId(),
                    ],
                'customer' => [
                        'customer_id' => $customer->getId(),
                        'first_name' => $customer->getFirstname(),
                        'last_name' => $customer->getLastname(),
                        'name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                        'email' => $customer->getEmail(),
                        'document_type' => 'CPF',
                        'document_number' => $customer->getTaxvat(),
                        'phone_number' => $address->getTelephone(),
                        'billing_address' =>[
                                'street' => $street,
                                'number' => $number,
                                'complement' => $complement,
                                'district' => $district,
                                'city' => $address->getCity(),
                                'state' => $address->getRegion(),
                                'country' => 'Brasil',
                                'postal_code' => $address->getPostcode(),
                            ],
                    ],
                'shippings' => [
                        0 => [
                                'first_name' => $shipping->getFirstname(),
                                'name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                                'email' => $customer->getEmail(),
                                'phone_number' =>  $address->getTelephone(),
                                'shipping_amount' => 3000,
                                'address' =>[
                                    'street' => $street,
                                    'number' => $number,
                                    'complement' => $complement,
                                    'district' => $district,
                                    'city' => $shipping->getCity(),
                                    'state' => $shipping->getRegion(),
                                    'country' => 'Brasil',
                                    'postal_code' => $shipping->getPostcode(),
                                    ],
                            ],
                    ],
                'credit' => [
                        'delayed' => false,
                        'authenticated' => false,
                        'pre_authorization' => true,
                        'save_card_data' => false,
                        'transaction_type' => 'FULL',
                        'number_installments' => 1,
                        'card' => [
                                'number_token' => $ccNumberToken,
                                'cardholder_name' => 'Jonatan Santos',
                                'security_code' => $ccCid,
                                'brand' => 'Mastercard',
                                'expiration_month' => $ccExpMonth,
                                'expiration_year' => $ccExpYear,
                            ],
                    ],
            ]
        ];

        return $response;
    }
}
