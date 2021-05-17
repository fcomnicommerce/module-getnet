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

namespace FCamara\Getnet\Ui\Component\Form;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\DataObject;

class ListCommissionsProductCredit extends DataObject implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];

        $options[] = ['value' => 'CREDITO A VISTA', 'label' => 'Crédito á Vista'];
        $options[] = ['value' => 'PARCELADO LOJISTA 3X', 'label' => 'Parcelado Lojista 3x'];
        $options[] = ['value' => 'PARCELADO LOJISTA 6X', 'label' => 'Parcelado Lojista 6x'];
        $options[] = ['value' => 'PARCELADO LOJISTA 9X', 'label' => 'Parcelado Lojista 9x'];
        $options[] = ['value' => 'PARCELADO LOJISTA 12X', 'label' => 'Parcelado Lojista 12x'];
        $options[] = ['value' => 'PARCELADO EMISSOR', 'label' => 'Parcelado Emissor'];

        return $options;
    }
}
