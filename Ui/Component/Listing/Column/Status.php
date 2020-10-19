<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to https://www.fcamara.com.br/ for more information.
 *
 * @category  FCamara
 * @package   FCamara_
 * @copyright Copyright (c) 2020 FCamara Formação e Consultoria
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */

namespace FCamara\Getnet\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Backend\Block\Widget\Context as ContextBackend;
use FCamara\Getnet\Model\Seller\SellerClient;
use FCamara\Getnet\Model\Seller\SellerClientPj;

/**
 * Class Actions
 */
class Status extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var SellerClient
     */
    protected $sellerClient;

    /**
     * @var SellerClientPj
     */
    protected $sellerClientPj;

    /**
     * Actions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ContextBackend $contextBackend
     * @param SellerClient $sellerClient
     * @param SellerClientPj $sellerClientPj
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ContextBackend $contextBackend,
        SellerClient $sellerClient,
        SellerClientPj $sellerClientPj,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $contextBackend->getUrlBuilder();
        $this->sellerClient = $sellerClient;
        $this->sellerClientPj = $sellerClientPj;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['type'] == 'PF') {
                    $item['status'] = $this->getStatusSeller($item['merchant_id'], $item['legal_document_number']);
                    continue;
                }

                $item['status'] = $this->getStatusSellerPj($item['merchant_id'], $item['legal_document_number']);
            }
        }

        return $dataSource;
    }

    /**
     * @param $merchantId
     * @param $cpf
     * @return mixed
     */
    public function getStatusSeller($merchantId, $cpf)
    {
        $responseBody = $this->sellerClient->pfCallback($merchantId, $cpf);
        return $responseBody['status'];
    }

    /**
     * @param $merchantId
     * @param $cpf
     * @return mixed
     */
    public function getStatusSellerPj($merchantId, $cpf)
    {
        $responseBody = $this->sellerClientPj->pjCallback($merchantId, $cpf);
        return $responseBody['status'];
    }
}

