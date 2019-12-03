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

namespace FCamara\Getnet\Gateway\Http\Client\CreditCard;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;

class AuthorizeClient implements ClientInterface
{
    const SUCCESS = 1;
    const FAILURE = 0;

    /**
     * @var array
     */
    private $results = [
        self::SUCCESS,
        self::FAILURE
    ];

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    private $httpClientFactory;

    /**
     * @var \FCamara\Getnet\Model\ConfigInterface
     */
    private $config;

    /**
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \FCamara\Getnet\Model\ConfigInterface $config
    ) {
        $this->logger = $logger;
        $this->httpClientFactory = $httpClientFactory;
        $this->config = $config;
    }

    /**
     * Places request to gateway. Returns result as ENV array
     *
     * @param TransferInterface $transferObject
     * @return array
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $response = $this->generateResponseForCode(
            $this->getResultCode(
                $transferObject
            )
        );

        $this->logger->debug(
            [
                'request' => $transferObject->getBody(),
                'response' => $response
            ]
        );

        return $response;
    }

    /**
     * Generates response
     *
     * @return array
     */
    protected function generateResponseForCode($resultCode)
    {
        return array_merge(
            [
                'RESULT_CODE' => $resultCode,
                'TXN_ID' => $this->generateTxnId()
            ],
            $this->getFieldsBasedOnResponseType($resultCode)
        );
    }

    /**
     * @return string
     */
    protected function generateTxnId()
    {
        return md5(mt_rand(0, 1000));
    }

    /**
     * Returns result code
     *
     * @param TransferInterface $transfer
     * @return int
     * @throws \Zend_Http_Client_Exception
     */
    private function getResultCode(TransferInterface $transfer)
    {
        $headers = $transfer->getHeaders();
        $body = $transfer->getBody();

        $client = $this->httpClientFactory->create();
        $client->setUri($this->config->endpoint() . '/v1/payments/credit');
        $client->setHeaders(['content-type: application/json; charset=utf-8']);
        $client->setHeaders('Authorization', 'Bearer ' . $headers['authorization']);
        $client->setMethod(\Zend_Http_Client::POST);
        $client->setRawData(json_encode($body));

        $request = $client->request();
        $responseBody = $request->getBody();

        if ($request->getStatus() != 202) {
            throw new \Exception($responseBody);
        }

        if (isset($headers['force_result'])) {
            return (int)$headers['force_result'];
        }

        return $responseBody;
    }

    /**
     * Returns response fields for result code
     *
     * @param int $resultCode
     * @return array
     */
    private function getFieldsBasedOnResponseType($resultCode)
    {
        switch ($resultCode) {
            case self::FAILURE:
                return [
                    'FRAUD_MSG_LIST' => [
                        'Stolen card',
                        'Customer location differs'
                    ]
                ];
        }

        return [];
    }
}
