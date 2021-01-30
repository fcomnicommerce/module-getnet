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

namespace FCamara\Getnet\Model\Eav\Entity\Attribute\Source;

use FCamara\Getnet\Model\ResourceModel\Seller\CollectionFactory;
use FCamara\Getnet\Model\Seller\SellerClient;
use FCamara\Getnet\Model\Seller\SellerClientPj;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class SellerId extends AbstractSource
{
    const STATUS_APPROVED_TRANSACT = 'Aprovado Transacionar';
    const STATUS_APPROVED_TRANSACT_TO_ANTICIPATE = 'Aprovado Transacionar e Antecipar';
    const STATUS_APPROVED = 'Aprovado';

    /**
     * @var FCamara\Getnet\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $sellerCollection;

    /**
     * @var SellerClient
     */
    protected $clientPf;

    /**
     * @var SellerClientPj
     */
    protected $clientPj;

    /**
     * SellerId constructor.
     * @param CollectionFactory $sellerCollection
     */
    public function __construct(
        CollectionFactory $sellerCollection,
        SellerClient $clientPf,
        SellerClientPj $clientPj
    ) {
        $this->sellerCollection = $sellerCollection->create();
        $this->clientPf = $clientPf;
        $this->clientPj = $clientPj;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];

        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }

        return $_options;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $items = $this->sellerCollection->getItems();

            $this->_options = [['value' => '', 'label' => __('-- Please Select --')]];
            foreach ($items as $item) {
                if (!$this->checkSellerIsApproved($item)) {
                    continue;
                }

                $this->_options[] = [
                    'label' => $item->getData('legal_name'),
                    'value' => $item->getData('subseller_id')
                ];
            }
        }

        return $this->_options;
    }

    protected function checkSellerIsApproved($seller)
    {
        $seller = $seller->getData();
        $result = false;

        if ($seller['type'] == 'PF') {
            $pfCallback = $this->clientPf->pfCallback($seller['merchant_id'], $seller['legal_document_number']);

            if (
                $pfCallback
                && (
                    $pfCallback['status'] == self::STATUS_APPROVED
                    || $pfCallback['status'] == self::STATUS_APPROVED_TRANSACT
                    || $pfCallback['status'] == self::STATUS_APPROVED_TRANSACT_TO_ANTICIPATE
                )
            ) {
                $result = true;
            }
        }

        if ($seller['type'] == 'PJ') {
            $pjCallback = $this->clientPj->pjCallback($seller['merchant_id'], $seller['legal_document_number']);

            if (
                $pjCallback
                && (
                    $pjCallback['status'] == self::STATUS_APPROVED
                    || $pjCallback['status'] == self::STATUS_APPROVED_TRANSACT
                    || $pjCallback['status'] == self::STATUS_APPROVED_TRANSACT_TO_ANTICIPATE
                )
            ) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();

        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }

        return false;
    }
}
