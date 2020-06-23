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

namespace FCamara\Getnet\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use FCamara\Getnet\Model\Config\CreditCardConfig;
use FCamara\Getnet\Model\Client;
use Magento\Customer\Model\Session as CustomerSession;

class CheckoutIframeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var CreditCardConfig
     */
    private $creditCardConfig;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * CheckoutIframeConfigProvider constructor.
     * @param Session $checkoutSession
     * @param CreditCardConfig $creditCardConfig
     * @param Client $client
     * @param CustomerSession $customerSession
     */
    public function __construct(
        Session $checkoutSession,
        CreditCardConfig $creditCardConfig,
        Client $client,
        CustomerSession $customerSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->creditCardConfig = $creditCardConfig;
        $this->client = $client;
        $this->customerSession = $customerSession;
    }

    /**
     * @return array|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
        if (!$this->creditCardConfig->isEnabledCheckoutIframe()) {
            return [];
        }

        $output['payment']['getnet_checkout_iframe'] = [];
        $quote = $this->checkoutSession->getQuote();
        $customer = $this->customerSession->getCustomer();
        $customerDocument = $this->customerDocument($customer);
        $billingAddress = $quote->getBillingAddress();
        $shippingAddress = $quote->getShippingAddress();
        $address = $this->getAddressLines($billingAddress);
        $shippingAddressLines = $this->getAddressLines($shippingAddress);
        $postcode = $this->cleanZipcode($billingAddress->getPostcode());
        $postcodeShippingAddress = $this->cleanZipcode($shippingAddress->getPostcode());

        $output['payment']['getnet_checkout_iframe'] = [
            'url' => $this->creditCardConfig->urlCheckoutIframe(),
            'seller_id' => $this->creditCardConfig->sellerId(),
            'token' => 'Bearer ' . $this->client->authentication(),
            'amount' => number_format($quote->getData('grand_total'), 2, '.', ''),
            'customerid' => $customer->getId(),
            'installments' => $this->creditCardConfig->qtyInstallments(),
            'orderid' => $quote->getId(),
            'customer' => [
                'seller_id' => $this->creditCardConfig->sellerId(),
                'first_name' => $customer->getData('firstname'),
                'last_name' => $customer->getData('lastname'),
                'document_type' => $customerDocument['document_type'],
                'document_number' => $customerDocument['document_number'],
                'email' => $customer->getData('email'),
                'phone_number' => $shippingAddress->getTelephone(),
                'billing_address' => [
                    'street' => $address[0],
                    'number' => $address[1],
                    'complement' => $address[2],
                    'neighborhood' => $address[3],
                    'city' => $billingAddress->getCity(),
                    'state' => $billingAddress->getRegionCode(),
                    'country' => $billingAddress->getCountryId(),
                    'postal_code' => $postcode,
                ],
                'address' => [
                    'street' => $shippingAddressLines[0],
                    'number' => $shippingAddressLines[1],
                    'complement' => $shippingAddressLines[2],
                    'district' => $shippingAddressLines[3],
                    'city' => $shippingAddress->getCity(),
                    'state' => $shippingAddress->getRegionCode(),
                    'country' => $shippingAddress->getCountryId(),
                    'postal_code' => $postcodeShippingAddress,
                ],
                'shipping_address' => [
                    'first_name' => $customer->getData('firstname'),
                    'name' => $customer->getData('firstname') . ' ' . $customer->getData('lastname'),
                    'email' => $customer->getData('email'),
                    'phone_number' => $shippingAddress->getTelephone(),
                    'shipping_amount' => number_format($shippingAddress->getShippingAmount(), 2, '.', ''),
                    'address' => [
                        'street' => $shippingAddressLines[0],
                        'complement' => $shippingAddressLines[2],
                        'number' => $shippingAddressLines[1],
                        'district' => $shippingAddressLines[3],
                        'city' => $shippingAddress->getCity(),
                        'state' => $shippingAddress->getRegionCode(),
                        'country' => $shippingAddress->getCountryId(),
                        'postal_code' => $postcodeShippingAddress,
                    ]
                ]
            ]
        ];

        foreach ($quote->getAllVisibleItems() as $item) {
            $output['payment']['getnet_checkout_iframe']['items'][] = [
                'name' => $item->getName(),
                'description' => '',
                'value' => $item->getPrice(),
                'quantity' => $item->getQty(),
                'sku' => $item->getSku()
            ];
        }

        $output['payment']['getnet_checkout_iframe']['items'] = json_encode(
            $output['payment']['getnet_checkout_iframe']['items']
        );

        return $output;
    }

    /**
     * @param $customer
     * @return array
     */
    private function customerDocument($customer)
    {
        $documentType = 'CPF';
        $documentAttribute = $this->creditCardConfig->documentAttribute();
        $documentNumber = 'NÃO INFORMADO';
        $customerData = $customer->getData();

        if ($this->creditCardConfig->cpfSameAsCnpj() && isset($customerData[$documentAttribute])) {
            $documentNumber = preg_replace('/[^0-9]/', '', $customerData[$documentAttribute]);
            if (strlen($documentNumber) == 14) {
                $documentType = 'CNPJ';
            }
            return ['document_type' => $documentType, 'document_number' => $documentNumber];
        }

        $cpfAttribute = $this->creditCardConfig->cpfAttribute();
        $cnpjAttribute = $this->creditCardConfig->cnpjAttribute();

        if (isset($customerData[$cpfAttribute])) {
            $cpfNumber = preg_replace('/[^0-9]/', '', $customerData[$cpfAttribute]);
        } else {
            $cpfNumber = 0;
        }

        if (isset($customerData[$cnpjAttribute])) {
            $cnpjNumber = preg_replace('/[^0-9]/', '', $customerData[$cnpjAttribute]);
        } else {
            $cnpjNumber = 0;
        }

        if (strlen($cpfNumber) == 11) {
            $documentType = 'CPF';
            $documentNumber = $cnpjNumber;
        }

        if (strlen($cnpjNumber) == 14) {
            $documentType = 'CNPJ';
            $documentNumber = $cnpjNumber;
        }

        return ['document_type' => $documentType, 'document_number' => $documentNumber];
    }

    /**
     * @param $billingAddress
     * @return array
     */
    private function getAddressLines($billingAddress)
    {
        $streetPos = $this->creditCardConfig->streetLine() != null ? $this->creditCardConfig->streetLine() + 1 : 0;
        $numberPos = $this->creditCardConfig->numberLine() != null ? $this->creditCardConfig->numberLine() + 1 : 0;

        $complementPos = $this->creditCardConfig->complementLine() != null
            ? $this->creditCardConfig->complementLine() + 1 : 0;

        $districtPos = $this->creditCardConfig->districtLine() != null
            ? $this->creditCardConfig->districtLine() + 1 : 0;

        $positions = [$streetPos, $numberPos, $complementPos, $districtPos];
        $addressLines = [];

        foreach ($positions as $position) {
            $function_pos = 'getStreetLine';
            $addressLines[] = $billingAddress->$function_pos($position);
        }

        return $addressLines;
    }

    /**
     * @param $postcode
     * @return string
     */
    public function cleanZipcode($postcode)
    {
        $postcode = explode("-", $postcode);
        return count($postcode) > 1 ? $postcode[0] . $postcode[1] : $postcode;
    }
}
