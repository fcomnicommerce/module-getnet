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

use FCamara\Getnet\Model\ResourceModel\Seller\Collection;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use FCamara\Getnet\Model\ResourceModel\Seller\CollectionFactory;

class SellerId extends AbstractSource
{
    /**
     * @var FCamara\Getnet\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $sellerCollection;

    /**
     * SellerId constructor.
     * @param CollectionFactory $sellerCollection
     */
    public function __construct(
        CollectionFactory $sellerCollection
    ) {
        $this->sellerCollection = $sellerCollection->create();
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
                $this->_options[] = [
                    'label' => $item->getData('legal_name'),
                    'value' => $item->getData('subseller_id')
                ];
            }
        }

        return $this->_options;
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
