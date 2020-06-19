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
 * @copyright Copyright (c) 2020 FCamara Formação e Consultoria
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */
namespace FCamara\Getnet\Model\Seller;

use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use FCamara\Getnet\Model\Config\SellerConfig;

class SellerClient
{
    const SUCCESS_CODES = [
        200,
        201,
        202
    ];

    const CONFIG_HTTP_CLIENT = [
        'maxredirects'    => 5,
        'strictredirects' => false,
        'useragent'       => 'Zend_Http_Client',
        'timeout'         => 10,
        'adapter'         => 'Zend_Http_Client_Adapter_Socket',
        'httpversion'     => \Zend_Http_Client::HTTP_1,
        'keepalive'       => false,
        'storeresponse'   => true,
        'strict'          => false,
        'output_stream'   => false,
        'encodecookies'   => true,
        'rfc3986_strict'  => false
    ];

    /**
     * @var SellerConfig
     */
    private $sellerConfig;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    private $httpClientFactory;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SellerClient constructor.
     * @param ZendClientFactory $httpClientFactory
     * @param SellerConfig $sellerConfig
     * @param Session $session
     * @param LoggerInterface $logger
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        ZendClientFactory $httpClientFactory,
        SellerConfig $sellerConfig,
        Session $session,
        LoggerInterface $logger
    ) {
        $this->sellerConfig = $sellerConfig;
        $this->httpClientFactory = $httpClientFactory;
        $this->quote = $session->getQuote();
        $this->logger = $logger;
    }

    /**
     * @return bool|mixed
     */
    public function authentication()
    {
        $responseBody = false;
        $authorization = base64_encode($this->sellerConfig->clientId() . ':' . $this->sellerConfig->clientSecret());
        $client = $this->httpClientFactory->create();
        $client->setUri($this->sellerConfig->authenticationEndpoint());
        $client->setHeaders(['content-type: application/x-www-form-urlencoded']);
        $client->setHeaders('authorization', 'Basic ' . $authorization);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData('scope=mgm&grant_type=client_credentials');

        try {
            $responseBody = json_decode($client->request()->getBody(), true);

            if (!isset($responseBody['access_token'])) {
                $responseBody = false;
                throw new \Exception('Can\'t get token');
            }

            $responseBody = $responseBody['access_token'];
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param array $sellerData
     * @return bool|mixed
     */
    public function createSellerPf($sellerData = [])
    {
        $token = $this->authentication();
        $responseBody = false;
        $sellerData['merchant_id'] = $this->sellerConfig->merchantId();

        if (!$token) {
            return $responseBody;
        }

        $birthDate = date_create($sellerData['birth_date']);
        $businessAddress = json_decode($sellerData['business_address'], true);
        $bankAccounts = json_decode($sellerData['bank_accounts'], true);

        $data = [
            'merchant_id' => $sellerData['merchant_id'],
            'legal_document_number' => (int) $sellerData['legal_document_number'],
            'legal_name' => $sellerData['legal_name'],
            'birth_date' => date_format($birthDate, 'Y-m-d'),
            'mothers_name' => $sellerData['mothers_name'],
            'occupation' => $sellerData['occupation'],
            'monthly_gross_income' => (double) $sellerData['monthly_gross_income'],
            'business_address' => [
                'mailing_address_equals' => 'S',
                'street' => $businessAddress['street'],
                'number' => $businessAddress['number'],
                'district' => $businessAddress['district'],
                'city' => $businessAddress['city'],
                'state' => $businessAddress['state'],
                'postal_code' => $businessAddress['postal_code']
            ],
            'mailing_address' => $businessAddress,
            'working_hours' => [json_decode($sellerData['working_hours'], true)],
            'phone' => json_decode($sellerData['phone'], true),
            'cellphone' => json_decode($sellerData['cellphone'], true),
            'email' => $sellerData['email'],
            'acquirer_merchant_category_code' => $sellerData['acquirer_merchant_category_code'],
            'bank_accounts' => [
                'type_accounts' => 'unique',
                'unique_account' => [
                    'bank' => $bankAccounts['bank'],
                    'agency' => $bankAccounts['agency'],
                    'account' => $bankAccounts['account'],
                    'account_type' => $bankAccounts['account_type'],
                    'account_digit' => $bankAccounts['account_digit']
                ]
            ],
            'list_commissions' => [json_decode($sellerData['list_commissions'], true)],
            'accepted_contract' => $sellerData['accepted_contract'],
            'liability_chargeback' => $sellerData['liability_chargeback'],
            'marketplace_store' => $sellerData['marketplace_store'],
            'payment_plan' => $sellerData['payment_plan']
        ];

        $client = $this->httpClientFactory->create();
        $client->setUri($this->sellerConfig->pfCreatePreSubSellerEndpoint());
        $client->setConfig(self::CONFIG_HTTP_CLIENT);
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($data));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param array $sellerData
     * @return bool|mixed
     */
    public function pfUpdateSubSeller($sellerData = [])
    {
        $token = $this->authentication();
        $responseBody = false;

        if (!$token) {
            return $responseBody;
        }

        $birthDate = date_create($sellerData['birth_date']);
        $businessAddress = json_decode($sellerData['business_address'], true);
        $bankAccounts = json_decode($sellerData['bank_accounts'], true);

        $data = [
            'subseller_id' => $sellerData['subseller_id'],
            'merchant_id' => $sellerData['merchant_id'],
            'legal_document_number' => (int) $sellerData['legal_document_number'],
            'legal_name' => $sellerData['legal_name'],
            'birth_date' => date_format($birthDate, 'Y-m-d'),
            'mothers_name' => $sellerData['mothers_name'],
            'occupation' => $sellerData['occupation'],
            'monthly_gross_income' => (double) $sellerData['monthly_gross_income'],
            'business_address' => [
                'mailing_address_equals' => 'S',
                'street' => $businessAddress['street'],
                'number' => $businessAddress['number'],
                'district' => $businessAddress['district'],
                'city' => $businessAddress['city'],
                'state' => $businessAddress['state'],
                'postal_code' => $businessAddress['postal_code']
            ],
            'mailing_address' => $businessAddress,
            'working_hours' => [json_decode($sellerData['working_hours'], true)],
            'phone' => json_decode($sellerData['phone'], true),
            'cellphone' => json_decode($sellerData['cellphone'], true),
            'email' => $sellerData['email'],
            'acquirer_merchant_category_code' => $sellerData['acquirer_merchant_category_code'],
            'bank_accounts' => [
                'type_accounts' => 'unique',
                'unique_account' => [
                    'bank' => $bankAccounts['bank'],
                    'agency' => $bankAccounts['agency'],
                    'account' => $bankAccounts['account'],
                    'account_type' => $bankAccounts['account_type'],
                    'account_digit' => $bankAccounts['account_digit']
                ]
            ],
            'list_commissions' => [json_decode($sellerData['list_commissions'], true)],
            'accepted_contract' => $sellerData['accepted_contract'],
            'liability_chargeback' => $sellerData['liability_chargeback'],
            'marketplace_store' => $sellerData['marketplace_store'],
            'payment_plan' => (int) $sellerData['payment_plan']
        ];

        $client = $this->httpClientFactory->create();
        $client->setUri($this->sellerConfig->pfUpdateSubSellerEndpoint());
        $client->setConfig(self::CONFIG_HTTP_CLIENT);
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::PUT);
        $client->setRawData(json_encode($data));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @return bool|mixed
     */
    public function pfConsultPaymentPlans()
    {
        $token = $this->authentication();
        $responseBody = false;

        if (!$token) {
            return $responseBody;
        }

        $client = $this->httpClientFactory->create();
        $client->setUri($this->sellerConfig->pfConsultPaymentPlansEndpoint($this->sellerConfig->merchantId()));
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::GET);

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param $merchantId
     * @param $cpf
     * @return bool|mixed
     */
    public function pfCallback($merchantId, $cpf)
    {
        $token = $this->authentication();
        $responseBody = false;

        if (!$token) {
            return $responseBody;
        }

        $client = $this->httpClientFactory->create();
        $client->setUri($this->sellerConfig->pfCallbackEndpoint($merchantId, $cpf));
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::GET);

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param $subSellerId
     * @return bool|mixed
     */
    public function pfDeAccredit($subSellerId)
    {
        $token = $this->authentication();
        $responseBody = false;

        if (!$token) {
            return $responseBody;
        }

        $client = $this->httpClientFactory->create();
        $client->setUri($this->sellerConfig->pfDeAccreditEndpoint($this->sellerConfig->merchantId(), $subSellerId));
        $client->setConfig(self::CONFIG_HTTP_CLIENT);
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        //$client->setRawData(json_encode([]));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }
}
