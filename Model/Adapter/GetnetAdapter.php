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

namespace FCamara\Getnet\Model\Adapter;

use FCamara\Getnet\Model\ClientFactory;

class GetnetAdapter
{
    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * GetnetAdapter constructor.
     * @param ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param $requestParameters
     * @param bool $captureNow
     * @return mixed
     */
    public function authorize($requestParameters, $captureNow = false)
    {
        $client = $this->clientFactory->create();

        $response = $client->authorize($requestParameters);

        return $response;
    }

    /**
     * @param $requestParameters
     * @return mixed
     */
    public function capture($requestParameters)
    {
        $client = $this->clientFactory->create();
        $response = $client->capture($requestParameters);
        return $response;
    }

    /**
     * @param $requestParameters
     * @return mixed
     */
    public function debitAuthorize($requestParameters)
    {
        $client = $this->clientFactory->create();

        $response = $client->debitAuthorize($requestParameters);

        return $response;
    }

    /**
     * @param $requestParameters
     * @return mixed
     */
    public function billetAuthorize($requestParameters)
    {
        $client = $this->clientFactory->create();

        $response = $client->billetAuthorize($requestParameters);

        return $response;
    }
}
