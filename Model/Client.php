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

use Magento\Checkout\Model\Cart;

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
     * @var Cart
     */
    private $cart;

    /**
     * Client constructor.
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param Config\CreditCardConfig $creditCardConfig
     * @param Cart $cart
     */
    public function __construct(
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        Config\CreditCardConfig $creditCardConfig,
        Cart $cart
    ) {
        $this->creditCardConfig = $creditCardConfig;
        $this->httpClientFactory = $httpClientFactory;
        $this->cart = $cart;
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
            $e->getMessage();
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
            $e->getMessage();
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
     * {@inheritDoc}
     */
    public function authorize($requestParameters = [])
    {
        $token = $this->authentication();
        $responseBody = false;
        $requestParameters['seller_id'] = $this->creditCardConfig->sellerId();
        $client = $this->httpClientFactory->create();
        $client->setUri($this->creditCardConfig->authorizeEndpoint());
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParameters));

        try {
            $quoteItems = $this->cart->getQuote()->getAllVisibleItems();

            foreach ($quoteItems as $item) {
                $product = $item->getProduct();
                if ($product->getData('is_recurrence') && $product->getData('recurrence_plan_id')) {
                    $registerCustomer = $this->customers($requestParameters['customer']);

                    if ($registerCustomer) {
                        $requestParameters['plan_id'] = $product->getData('recurrence_plan_id');
                        $subscription = $this->subscriptions($requestParameters);

                        if (!$subscription) {
                            throw new \Exception('Error saving recurrence.');
                        }

                        $responseBody = $subscription;
                    } else {
                        throw new \Exception('Error saving recurrence.');
                    }
                }
            }

            $responseBody = json_decode($client->request()->getBody(), true);
            if (isset($responseBody['status']) && $responseBody['status'] == 'AUTHORIZED' || $responseBody['status'] == 'APPROVED') {
                $this->saveCardData($requestParameters);
            }
        } catch (\Exception $e) {
            $e->getMessage();
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
        $requestParameters['seller_id'] = $this->creditCardConfig->sellerId();
        $client = $this->httpClientFactory->create();
        $client->setUri(str_replace('{payment_id}', $requestParameters['payment_id'], $this->creditCardConfig->captureEndpoint()));
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParameters));

        try {
            $quoteItems = $this->cart->getQuote()->getAllVisibleItems();

            foreach ($quoteItems as $item) {
                $product = $item->getProduct();
                if ($product->getData('is_recurrence') && $product->getData('recurrence_plan_id')) {
                    $registerCustomer = $this->customers($requestParameters['customer']);

                    if ($registerCustomer) {
                        $subscription = $this->subscriptions($requestParameters);

                        if (!$subscription) {
                            throw new \Exception('Error saving recurrence.');
                        }

                        $responseBody = $subscription;
                    } else {
                        throw new \Exception('Error saving recurrence.');
                    }
                }
            }

            $responseBody = json_decode($client->request()->getBody(), true);
            if (isset($responseBody['status']) && $responseBody['status'] == 'APPROVED') {
                $this->saveCardData($requestParameters);
            }
        } catch (\Exception $e) {
            $e->getMessage();
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
     * @return bool|mixed
     */
    protected function saveCardData($requestParameters)
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
                'card_number' => $requestParameters['cc_number'],
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
            $e->getMessage();
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
            $e->getMessage();
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
            $e->getMessage();
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
            $e->getMessage();
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
        $responseStatus = false;
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
            $responseStatus = in_array($client->request()->getStatus(), self::SUCCESS_CODES);
        } catch (\Exception $e) {
            $e->getMessage();
        }

        return $responseStatus;
    }

    /**
     * @param $data
     * @return bool|mixed
     */
    public function subscriptions($data)
    {
        $token = $this->authentication();
        $responseStatus = false;
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

        try {
            $responseStatus = in_array($client->request()->getStatus(), self::SUCCESS_CODES);
        } catch (\Exception $e) {
            $e->getMessage();
        }

        return $responseStatus;
    }
}
