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

namespace FCamara\Getnet\Model;

use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;
use FCamara\Getnet\Model\ReportFactory;

class Client implements ClientInterface
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
     * @var Config\CreditCardConfig
    */
    private $creditCardConfig;

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
     * @var ReportFactory
     */
    protected $report;

    /**
     * Client constructor.
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param Config\CreditCardConfig $creditCardConfig
     * @param Session $session
     * @param LoggerInterface $logger
     * @param \FCamara\Getnet\Model\ReportFactory $report
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        Config\CreditCardConfig $creditCardConfig,
        Session $session,
        LoggerInterface $logger,
        ReportFactory $report
    ) {
        $this->creditCardConfig = $creditCardConfig;
        $this->httpClientFactory = $httpClientFactory;
        $this->quote = $session->getQuote();
        $this->logger = $logger;
        $this->report = $report;
    }

    /**
     * {@inheritDoc}
     */
    public function setClientId($clientId)
    {
        // TODO: Implement setClientId() method.
    }

    /**
     * {@inheritDoc}
     */
    public function setClientSecret($clientSecret)
    {
        // TODO: Implement setClientSecret() method.
    }

    /**
     * {@inheritDoc}
     */
    public function setEndpoint($endpoint)
    {
        // TODO: Implement setEndpoint() method.
    }

    /**
     * {@inheritDoc}
     */
    public function authentication()
    {
        $responseBody = false;
        $authorization = base64_encode($this->creditCardConfig->clientId()
            . ':' . $this->creditCardConfig->clientSecret());
        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->authenticationEndpoint());
        $client->setHeaders(['content-type: application/x-www-form-urlencoded']);
        $client->setHeaders('authorization', 'Basic ' . $authorization);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData('scope=oob&grant_type=client_credentials');

        try {
            $responseBody = json_decode($client->request()->getBody(), true);

            if (!isset($responseBody['access_token'])) {
                throw new \ErrorException('Can\'t get token');
            }
            $responseBody = $responseBody['access_token'];
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * {@inheritDoc}
     */
    public function tokenCard($requestParameters = [])
    {
        $token = $this->authentication();
        $responseBody = false;
        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->tokensCardEndpoint());
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParameters));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody['number_token'];
    }

    /**
     * {@inheritDoc}
     */
    public function verifyCard($requestParameters = [])
    {
        // TODO: Implement verifyCard() method.
    }

    /**
     * {@inheritDoc}
     */
    public function changePaymentAmount($requestParameters = [])
    {
        // TODO: Implement changePaymentAmount() method.
    }

    /**
     * @param array $requestParameters
     * @return array|bool|false|mixed
     */
    public function authorize($requestParameters = [])
    {
        $token = $this->authentication();
        $responseBody = false;
        $isRecurrence = false;
        $requestParameters['seller_id'] = $this->creditCardConfig->sellerId();
        $client = $this->httpClientFactory->create();
        $ccNumber = $requestParameters['cc_number'];
        unset($requestParameters['cc_number']);

        $client->setUri($this->creditCardConfig->authorizeEndpoint());
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParameters));

        $this->logger->info('Getnet - ' . $this->creditCardConfig->authorizeEndpoint());
        $this->logger->info('RequestBody:');
        $this->logger->info(json_encode($requestParameters));

        try {
            $quoteItems = $this->quote->getAllVisibleItems();

            foreach ($quoteItems as $item) {
                $product = $item->getProduct();
                if ($product->getData('is_recurrence') && $product->getData('recurrence_plan_id')) {
                    $isRecurrence = true;
                    $this->customers($requestParameters['customer']);
                    $requestParameters['plan_id'] = $product->getData('recurrence_plan_id');
                    $subscription = $this->subscriptions($requestParameters);

                    if (!$subscription) {
                        throw new \Exception('Error saving recurrence.');
                    }

                    $responseBody = $subscription;
                }
            }

            if (!$isRecurrence) {
                $responseBody = json_decode($client->request()->getBody(), true);

                $this->logger->info('Getnet - ' . $this->creditCardConfig->authorizeEndpoint());
                $this->logger->info('ResponseBody:');
                $this->logger->info(json_encode($responseBody));

                if (
                    isset($responseBody['status_code'])
                    && !array_key_exists($responseBody['status_code'], self::SUCCESS_CODES)
                ) {
                    $error = [];
                    $report = $this->report->create();

                    if (isset($responseBody['details'])) {
                        $error = $responseBody['details'][0];
                    }

                    $report->addData(['customer_name' => $requestParameters['customer']['name']]);
                    $report->addData(['customer_email' => $requestParameters['customer']['email']]);
                    $report->addData(['status' => 'DENIED']);

                    if (count($error)) {
                        $report->addData(['status_message' => $error['error_code'] . ': ' . $error['description']]);
                    }

                    $report->addData(['payment_type' => $this->quote->getPayment()->getMethod()]);
                    $report->addData(['request_body' => json_encode($requestParameters)]);
                    $report->addData(['response_body' => json_encode($responseBody)]);

                    $report->save();
                }

                if (
                    isset($responseBody['status'])
                    && ($responseBody['status'] == 'AUTHORIZED'
                    || $responseBody['status'] == 'APPROVED')
                ) {
                    $report = $this->report->create();

                    $report->addData(['customer_name' => $requestParameters['customer']['name']]);
                    $report->addData(['customer_email' => $requestParameters['customer']['email']]);
                    $report->addData(['status' => $responseBody['status']]);
                    $report->addData(['status_message' => $responseBody['status']
                        . ': Transação realizada com sucesso!']);
                    $report->addData(['payment_type' => $this->quote->getPayment()->getMethod()]);
                    $report->addData(['request_body' => json_encode($requestParameters)]);
                    $report->addData(['response_body' => json_encode($responseBody)]);

                    $report->save();
                    $this->saveCardData($requestParameters, $ccNumber);
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param array $requestParameters
     * @return bool|mixed
     */
    public function debitAuthorize($requestParameters = [])
    {
        $token = $this->authentication();
        $responseBody = false;
        $requestParameters['seller_id'] = $this->creditCardConfig->sellerId();
        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->debitAuthorizeEndpoint());
        $client->setHeaders(['Content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParameters));

        $this->logger->info('Getnet - ' . $this->creditCardConfig->authorizeEndpoint());
        $this->logger->info('RequestBody:');
        $this->logger->info(json_encode($requestParameters));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);

            if (!isset($responseBody['payment_id'])) {
                $error = [];
                $report = $this->report->create();

                if (isset($responseBody['details'])) {
                    $error = $responseBody['details'][0];
                }

                $report->addData(['customer_name' => $requestParameters['customer']['name']]);
                $report->addData(['customer_email' => $requestParameters['customer']['email']]);
                $report->addData(['status' => 'DENIED']);

                if (count($error)) {
                    $report->addData(['status_message' => $error['error_code'] . ': ' . $error['description']]);
                }

                $report->addData(['payment_type' => $this->quote->getPayment()->getMethod()]);
                $report->addData(['request_body' => json_encode($requestParameters)]);
                $report->addData(['response_body' => json_encode($responseBody)]);

                $report->save();
            }

            if (isset($responseBody['payment_id'])) {
                $report = $this->report->create();

                $report->addData(['customer_name' => $requestParameters['customer']['name']]);
                $report->addData(['customer_email' => $requestParameters['customer']['email']]);
                $report->addData(['status' => 'AUTHORIZED']);
                $report->addData(['status_message' => 'AUTHORIZED: Transação realizada com sucesso!']);
                $report->addData(['payment_type' => $this->quote->getPayment()->getMethod()]);
                $report->addData(['request_body' => json_encode($requestParameters)]);
                $report->addData(['response_body' => json_encode($responseBody)]);

                $report->save();
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param array $requestParameters
     * @return bool|mixed
     */
    public function billetAuthorize($requestParameters = [])
    {
        $token = $this->authentication();
        $responseBody = false;
        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->billetAuthorizeEndpoint());
        $client->setHeaders(['Content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParameters));

        $this->logger->info('Getnet - ' . $this->creditCardConfig->billetAuthorizeEndpoint());
        $this->logger->info('RequestBody:');
        $this->logger->info(json_encode($requestParameters));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);

            if (!isset($responseBody['payment_id'])) {
                $report = $this->report->create();

                $report->addData(['customer_name' => $requestParameters['customer']['name']]);
                $report->addData(['customer_email' => $this->quote->getCustomerEmail()]);
                $report->addData(['status' => 'DENIED']);
                $report->addData(['status_message' => 'DENIED: Erro ao tentar gerar o boleto!']);
                $report->addData(['payment_type' => $this->quote->getPayment()->getMethod()]);
                $report->addData(['request_body' => json_encode($requestParameters)]);
                $report->addData(['response_body' => json_encode($responseBody)]);

                $report->save();
            }

            if (isset($responseBody['payment_id'])) {
                $report = $this->report->create();

                $report->addData(['customer_name' => $requestParameters['customer']['name']]);
                $report->addData(['customer_email' => $this->quote->getCustomerEmail()]);
                $report->addData(['status' => 'AUTHORIZED']);
                $report->addData(['status_message' => 'AUTHORIZED: Transação realizada com sucesso!']);
                $report->addData(['payment_type' => $this->quote->getPayment()->getMethod()]);
                $report->addData(['request_body' => json_encode($requestParameters)]);
                $report->addData(['response_body' => json_encode($responseBody)]);

                $report->save();
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * {@inheritDoc}
     */
    public function capture($requestParameters)
    {
        $token = $this->authentication();
        $responseBody = false;
        $isRecurrence = false;
        $requestParameters['seller_id'] = $this->creditCardConfig->sellerId();
        $client = $this->httpClientFactory->create();
        $ccNumber = isset($requestParameters['cc_number']) ? $requestParameters['cc_number'] : false;
        unset($requestParameters['cc_number']);

        $client->setUri(str_replace('{payment_id}', $requestParameters['payment_id'], $this->creditCardConfig->captureEndpoint()));
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParameters));

        $this->logger->info(
            'Getnet - ' . str_replace(
                '{payment_id}',
                $requestParameters['payment_id'],
                $this->creditCardConfig->captureEndpoint()
            )
        );
        $this->logger->info('RequestBody:');
        $this->logger->info(json_encode($requestParameters));

        try {
            $quoteItems = $this->quote->getAllVisibleItems();

            foreach ($quoteItems as $item) {
                $product = $item->getProduct();
                if ($product->getData('is_recurrence') && $product->getData('recurrence_plan_id')) {
                    $isRecurrence = true;
                    $this->customers($requestParameters['customer']);
                    $requestParameters['plan_id'] = $product->getData('recurrence_plan_id');
                    $subscription = $this->subscriptions($requestParameters);

                    if (!$subscription) {
                        throw new \Exception('Error saving recurrence.');
                    }

                    $responseBody = $subscription;
                }
            }

            if (!$isRecurrence) {
                $responseBody = json_decode($client->request()->getBody(), true);

                if (
                    isset($responseBody['status_code'])
                    && !array_key_exists($responseBody['status_code'], self::SUCCESS_CODES)
                ) {
                    $error = [];
                    $report = $this->report->create();

                    if (isset($responseBody['details'])) {
                        $error = $responseBody['details'][0];
                    }

                    if (isset($requestParameters['customer'])) {
                        if (isset($requestParameters['customer']['name'])) {
                            $report->addData(['customer_name' => $requestParameters['customer']['name']]);
                        }

                        if (isset($requestParameters['customer']['email'])) {
                            $report->addData(['customer_email' => $requestParameters['customer']['email']]);
                        }
                    }

                    $report->addData(['status' => 'DENIED']);

                    if (count($error)) {
                        $report->addData(['status_message' => $error['error_code'] . ': ' . $error['description']]);
                    }

                    $report->addData(['payment_type' => $this->quote->getPayment()->getMethod()]);
                    $report->addData(['request_body' => json_encode($requestParameters)]);
                    $report->addData(['response_body' => json_encode($responseBody)]);

                    $report->save();
                }

                if (
                    isset($responseBody['status'])
                    && ($responseBody['status'] == 'APPROVED'
                    || $responseBody['status'] == 'CONFIRMED')
                ) {
                    $report = $this->report->create();

                    if (isset($requestParameters['customer'])) {
                        if (isset($requestParameters['customer']['name'])) {
                            $report->addData(['customer_name' => $requestParameters['customer']['name']]);
                        }

                        if (isset($requestParameters['customer']['email'])) {
                            $report->addData(['customer_email' => $requestParameters['customer']['email']]);
                        }
                    }

                    $report->addData(['status' => $responseBody['status']]);
                    $report->addData(['status_message' => $responseBody['status']
                        . ': Transação realizada com sucesso!']);
                    $report->addData(['payment_type' => 'getnet_credit_card']);
                    $report->addData(['request_body' => json_encode($requestParameters)]);
                    $report->addData(['response_body' => json_encode($responseBody)]);

                    $report->save();
                    $this->saveCardData($requestParameters, $ccNumber);
                }

                $this->logger->info(
                    'Getnet - ' . str_replace(
                        '{payment_id}',
                        $requestParameters['payment_id'],
                        $this->creditCardConfig->captureEndpoint()
                    )
                );
                $this->logger->info('ResponseBody:');
                $this->logger->info(json_encode($responseBody));

                if (isset($responseBody['status']) && $responseBody['status'] == 'APPROVED') {
                    $this->saveCardData($requestParameters, $ccNumber);
                }
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * {@inheritDoc}
     */
    public function void($requestParameters = [])
    {
        // TODO: Implement void() method.
    }

    /**
     * @param $requestParameters
     * @param $ccNumber
     * @return bool|mixed|void
     */
    protected function saveCardData($requestParameters, $ccNumber)
    {
        $token = $this->authentication();
        $responseBody = false;
        $requestParameters['seller_id'] = $this->creditCardConfig->sellerId();

        if (!$requestParameters['credit']['save_card_data']) {
            return;
        }

        $cardData = $requestParameters['credit']['card'];
        $requestCardParams = [
            'number_token' => $this->tokenCard([
                'card_number' => $ccNumber,
                'customer_id' => $requestParameters['customer']['customer_id']
            ]),
            'brand' =>  $cardData['brand'],
            'cardholder_name' => $cardData['cardholder_name'],
            'expiration_month' => $cardData['expiration_month'],
            'expiration_year' => $cardData['expiration_year'],
            'customer_id' => $requestParameters['customer']['customer_id'],
            'cardholder_identification' => $requestParameters['customer']['document_number'],
            'verify_card' => false,
            'security_code' => $cardData['security_code']
        ];

        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->vaultEndpoint());
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestCardParams));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param $customerId
     * @return bool|mixed
     */
    public function cardList($customerId)
    {
        $responseBody = false;
        $token = $this->authentication();
        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->vaultEndpoint());
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::GET);
        $client->setParameterGet('customer_id', $customerId);

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param $cardId
     * @return bool|mixed
     */
    public function deleteCard($cardId)
    {
        $responseBody = false;
        $token = $this->authentication();
        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->vaultEndpoint() . '/' . $cardId);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend\Http\Request::METHOD_DELETE);

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param $requestParams
     * @return bool|mixed
     */
    public function plans($requestParams)
    {
        $token = $this->authentication();
        $responseBody = false;
        $requestParams['seller_id'] = $this->creditCardConfig->sellerId();

        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->plansEndpoint());
        $client->setConfig(self::CONFIG_HTTP_CLIENT);
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setHeaders(['seller_id' => $requestParams['seller_id']]);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParams));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param $data
     * @return bool|mixed
     */
    public function customers($data)
    {
        $token = $this->authentication();
        $responseBody = false;
        $requestParams = [
            'seller_id' => $this->creditCardConfig->sellerId(),
            'customer_id' => $data['customer_id'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'document_type' => $data['document_type'],
            'document_number' => $data['document_number'],
            'phone_number' => $data['phone_number'],
            'email' => $data['email'],
            'address' => $data['billing_address']
        ];

        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->customersEndpoint());
        $client->setConfig(self::CONFIG_HTTP_CLIENT);
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setHeaders(['seller_id' => $requestParams['seller_id']]);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParams));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return isset($responseBody['status']) && $responseBody['status'] == 'success';
    }

    /**
     * @param $data
     * @return bool|mixed
     */
    public function subscriptions($data)
    {
        $token = $this->authentication();
        $responseBody = false;
        $requestParams = [
            'seller_id' => $this->creditCardConfig->sellerId(),
            'customer_id' => $data['customer']['customer_id'],
            'plan_id' => $data['plan_id'],
            'subscription' => [
                'payment_type' => [
                    'credit' => [
                        'transaction_type' =>  $data['credit']['transaction_type'],
                        'number_installments' => 1,
                        'card' => [
                            'number_token' => $data['credit']['card']['number_token'],
                            'cardholder_name' => $data['credit']['card']['cardholder_name'],
                            'brand' => $data['credit']['card']['brand'],
                            'expiration_month' => $data['credit']['card']['expiration_month'],
                            'expiration_year' => $data['credit']['card']['expiration_year'],
                            'bin' => substr($data['cc_number'], 0, 6)
                        ]
                    ]
                ]
            ]
        ];

        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->subscriptionsEndpoint());
        $client->setConfig(self::CONFIG_HTTP_CLIENT);
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setHeaders(['seller_id' => $requestParams['seller_id']]);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParams));

        $this->logger->info('Getnet - ' . $this->creditCardConfig->subscriptionsEndpoint());
        $this->logger->info('RequestBody:');
        $this->logger->info(json_encode($requestParams));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);

            $this->logger->info('Getnet - ' . $this->creditCardConfig->subscriptionsEndpoint());
            $this->logger->info('ResponseBody');
            $this->logger->info(json_encode($responseBody));

            if (isset($responseBody['subscription']['subscription_id'])) {
                $this->quote->addData(['subscription_id' => $responseBody['subscription']['subscription_id']]);
                $this->quote->save();
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return isset($responseBody['status']) && $responseBody['status'] == 'success';
    }

    /**
     * @param $subscriptionId
     * @return bool|mixed
     */
    public function getSubscription($subscriptionId)
    {
        $token = $this->authentication();
        $responseBody = false;
        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->getSubscriptionEndpoint($subscriptionId));
        $client->setConfig(self::CONFIG_HTTP_CLIENT);
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setHeaders(['seller_id' => $this->creditCardConfig->sellerId()]);
        $client->setMethod(\Zend_Http_Client::GET);

        try {
            $responseBody = json_decode($client->request()->getBody(), true);

            if (!isset($responseBody['subscription']['subscription_id'])) {
                $responseBody = false;
                throw new \Exception('Subscription Not Found!');
            }
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param $customerId
     * @return bool|mixed
     */
    public function getSubscriptionsList($customerId)
    {
        $token = $this->authentication();
        $responseBody = false;
        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->getSubscriptionsListEndpoint($customerId));
        $client->setConfig(self::CONFIG_HTTP_CLIENT);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setHeaders(['seller_id' => $this->creditCardConfig->sellerId()]);
        $client->setMethod(\Zend_Http_Client::GET);

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }

    /**
     * @param $subscriptionId
     * @return bool
     */
    public function cancelSubscription($subscriptionId)
    {
        $token = $this->authentication();
        $responseBody = false;
        $requestParams = [
            'seller_id' => $this->creditCardConfig->sellerId(),
            'status_details' => 'Cliente não tem mais interesse no serviço/produto'
        ];

        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->cancelSubscriptionEndpoint($subscriptionId));
        $client->setConfig(self::CONFIG_HTTP_CLIENT);
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setHeaders(['seller_id' => $requestParams['seller_id']]);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParams));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);
        } catch (\Exception $e) {
            $this->logger->critical('Error message', ['exception' => $e]);
        }

        return $responseBody;
    }
}
