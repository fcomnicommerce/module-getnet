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

class AuthorizationRequest implements BuilderInterface
{
    const AUTHORIZATION = 'AUTHORIZATION';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    private $httpClientFactory;
    /**
     * @var \FCamara\Getnet\Model\ConfigInterface
     */
    private $configProvider;

    /**
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param ConfigInterface $config
     */
    public function __construct(
        ConfigInterface $config,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \FCamara\Getnet\Model\ConfigInterface $configProvider
    ) {
        $this->config = $config;
        $this->configProvider = $configProvider;
        $this->httpClientFactory = $httpClientFactory;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     * @throws \Zend_Http_Client_Exception
     */
    public function build(array $buildSubject)
    {
        if (!isset($buildSubject['payment'])
            || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        $clientId = $this->configProvider->clientId();
        $clientSecret = $this->configProvider->clientSecret();
        $authorization = base64_encode($clientId . ':' . $clientSecret);

        $client = $this->httpClientFactory->create();
        $client->setUri($this->configProvider->authorizationEndpoint());
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
            $responseBody = $e->getMessage();
        }

        return [
            self::AUTHORIZATION => $responseBody
        ];
    }
}
