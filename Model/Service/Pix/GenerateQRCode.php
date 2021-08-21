<?php

namespace FCamara\Getnet\Model\Service\Pix;

use chillerlan\QRCode\Data\QRCodeDataException;
use Magento\Framework\HTTP\ZendClientFactory;
use Psr\Log\LoggerInterface;
use FCamara\Getnet\Api\GenerateQRCodeInterface;
use chillerlan\QRCode\QRCode;
use FCamara\Getnet\Model\Ui\Pix\ConfigProvider as Config;
use Magento\Framework\Serialize\Serializer\Json;

class GenerateQRCode implements GenerateQRCodeInterface
{

    const PATH = 'v1/payments/qrcode/pix';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ZendClientFactory
     */
    private $httpClientFactory;

    /**
     * @var LoggerInterface
     */
    private $loggerInterface;

    private $json;

    /**
     * GenerateQRCode constructor.
     * @param Config $config
     * @param ZendClientFactory $httpClientFactory
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        Config $config,
        ZendClientFactory $httpClientFactory,
        LoggerInterface $loggerInterface,
        Json $json
    ) {
        $this->config = $config;
        $this->httpClientFactory = $httpClientFactory;
        $this->loggerInterface = $loggerInterface;
        $this->json = $json;
    }

    /**
     * @return mixed|string
     * @throws \Zend_Http_Client_Exception
     */
    private function getTokenPix()
    {
        $clientId = $this->config->clientId();
        $clientSecret = $this->config->clientSecret();
        $authorization = base64_encode($clientId . ':' . $clientSecret);

        $client = $this->httpClientFactory->create();
        $client->setUri($this->config->authorizationEndpoint());
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

        return $responseBody;
    }

    /**
     * @param string $amount
     * @param string $currency
     * @param string $orderId
     * @param string $customerId
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    public function execute(string $amount, string $currency, string $orderId, string $customerId)
    {
        $data = [];
        $response = [];
        $requestParameters = [
            'amount' => (float) $amount,
            'currency' => $currency,
            'order_id' => $orderId,
            'customer_id' => $customerId
        ];

        $token = $this->getTokenPix();
        $responseBody = false;
        $client = $this->httpClientFactory->create();
        $client->setUri($this->config->endpoint().self::PATH);
        $client->setHeaders(['Content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $token);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($requestParameters));

        try {
            $responseBody = json_decode($client->request()->getBody(), true);

            if ($responseBody['status'] !== 'DENIED') {
                $data = $responseBody['additional_data']['qr_code'];
                $response = [
                    'status' => $responseBody['status'],
                    'code_generated' => (new QRCode)->render($data),
                    'code' => $data
                ];
            }


        } catch (\Exception $e) {
            $data['status'] = 'EXCEPTION';
            $this->loggerInterface->critical('Error message', ['exception' => $e]);
        }

        return $this->json->serialize($response);
    }
}
