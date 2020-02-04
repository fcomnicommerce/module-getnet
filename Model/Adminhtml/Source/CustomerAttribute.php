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
 * @author    Joao Bortolosso<joao.bortolosso@fcamara.com.br>
 */

namespace FCamara\Getnet\Model\Adminhtml\Source;

class CustomerAttribute implements \Magento\Framework\Data\OptionSourceInterface
{
    public $attributes;

    public function __construct(
        \Magento\Customer\Model\Customer $customer
    ) {
        $this->attributes = $customer->getAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $attributesArrays = [];

        foreach ($this->attributes as $cal=>$val) {
            $attributesArrays[] = [
                'label' => $val->getName(),
                'value' => $val->getAttributeCode()
            ];
        }

        return $attributesArrays;
    }
}
