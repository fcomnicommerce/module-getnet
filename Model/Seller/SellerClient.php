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
namespace FCamara\Getnet\Model;

use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;
use Magento\Framework\HTTP\ZendClientFactory;

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
        ZendClientFactory $httpClientFactory,
        Config\CreditCardConfig $creditCardConfig,
        Session $session,
        LoggerInterface $logger
    ) {
        $this->creditCardConfig = $creditCardConfig;
        $this->httpClientFactory = $httpClientFactory;
        $this->quote = $session->getQuote();
        $this->logger = $logger;
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
}
