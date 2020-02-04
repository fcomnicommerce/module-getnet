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

namespace FCamara\Getnet\Model\Adminhtml\Source;


class StreetLine implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [ 'label' => __('Line 1'), 'value' => 0 ],
            [ 'label' => __('Line 2'), 'value' => 1 ],
            [ 'label' => __('Line 3'), 'value' => 2 ],
            [ 'label' => __('Line 4'), 'value' => 3 ],
        ];
    }
}
