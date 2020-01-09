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

namespace FCamara\Getnet\Model;


class Client implements ClientInterface
{
    /**
     * @var Config\CreditCardConfig
    */
    private $creditCardConfig;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
    */
    private $httpClientFactory;

    /**
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param Config\CreditCardConfig $creditCardConfig
     */
    public function __construct(
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        Config\CreditCardConfig $creditCardConfig
    ) {
        $this->creditCardConfig = $creditCardConfig;
        $this->httpClientFactory = $httpClientFactory;
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
        // TODO: Implement tokenCard() method.
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
            $responseBody = json_decode($client->request()->getBody(), true);
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
            $responseBody = json_decode($client->request()->getBody(), true);
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
}
