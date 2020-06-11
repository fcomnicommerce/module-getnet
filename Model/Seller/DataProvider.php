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
namespace FCamara\Getnet\Model\Seller;

use FCamara\Getnet\Model\ResourceModel\Seller\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * DataProvider constructor.
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param array $meta
     * @param array $data
     * @param CollectionFactory $sellerCollectionFactory
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $sellerCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $sellerCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        $this->loadedData = array();

        foreach ($items as $seller) {
            $this->loadedData[$seller->getId()]['main_fieldset'] = [
                'seller_information' => $seller->getData(),
                'seller_address' => json_decode($seller->getData('business_address'), true),
                'seller_working_hours' => json_decode($seller->getData('working_hours'), true),
                'seller_bank_account' => json_decode($seller->getData('bank_accounts'), true),
            ];
        }

        return $this->loadedData;
    }
}
