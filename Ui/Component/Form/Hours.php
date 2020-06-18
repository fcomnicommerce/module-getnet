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

class Hours extends DataObject implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];

        $options[] = ['value' => '00:00:00', 'label' => '00:00'];
        $options[] = ['value' => '01:00:00', 'label' => '01:00'];
        $options[] = ['value' => '02:00:00', 'label' => '02:00'];
        $options[] = ['value' => '03:00:00', 'label' => '02:00'];
        $options[] = ['value' => '04:00:00', 'label' => '04:00'];
        $options[] = ['value' => '05:00:00', 'label' => '05:00'];
        $options[] = ['value' => '06:00:00', 'label' => '06:00'];
        $options[] = ['value' => '07:00:00', 'label' => '07:00'];
        $options[] = ['value' => '08:00:00', 'label' => '08:00'];
        $options[] = ['value' => '09:00:00', 'label' => '09:00'];
        $options[] = ['value' => '10:00:00', 'label' => '10:00'];
        $options[] = ['value' => '11:00:00', 'label' => '11:00'];
        $options[] = ['value' => '12:00:00', 'label' => '12:00'];
        $options[] = ['value' => '13:00:00', 'label' => '13:00'];
        $options[] = ['value' => '14:00:00', 'label' => '14:00'];
        $options[] = ['value' => '15:00:00', 'label' => '15:00'];
        $options[] = ['value' => '16:00:00', 'label' => '16:00'];
        $options[] = ['value' => '17:00:00', 'label' => '17:00'];
        $options[] = ['value' => '18:00:00', 'label' => '18:00'];
        $options[] = ['value' => '19:00:00', 'label' => '19:00'];
        $options[] = ['value' => '20:00:00', 'label' => '20:00'];
        $options[] = ['value' => '21:00:00', 'label' => '21:00'];
        $options[] = ['value' => '22:00:00', 'label' => '22:00'];
        $options[] = ['value' => '23:00:00', 'label' => '23:00'];
        $options[] = ['value' => '24:00:00', 'label' => '24:00'];

        return $options;
    }
}