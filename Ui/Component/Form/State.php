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

class State extends DataObject implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];

        $options[] = ['value' => 'AC', 'label' => 'Acre'];
        $options[] = ['value' => 'AL', 'label' => 'Alagoas'];
        $options[] = ['value' => 'AP', 'label' => 'Amapá'];
        $options[] = ['value' => 'AM', 'label' => 'Amazonas'];
        $options[] = ['value' => 'BA', 'label' => 'Bahia'];
        $options[] = ['value' => 'CE', 'label' => 'Ceará'];
        $options[] = ['value' => 'DF', 'label' => 'Distrito Federal'];
        $options[] = ['value' => 'ES', 'label' => 'Espírito Santo'];
        $options[] = ['value' => 'GO', 'label' => 'Goiás'];
        $options[] = ['value' => 'MA', 'label' => 'Maranhão'];
        $options[] = ['value' => 'MT', 'label' => 'Mato Grosso'];
        $options[] = ['value' => 'MS', 'label' => 'Mato Grosso do Sul'];
        $options[] = ['value' => 'MG', 'label' => 'Minas Gerais'];
        $options[] = ['value' => 'PA', 'label' => 'Pará'];
        $options[] = ['value' => 'PB', 'label' => 'Paraíba'];
        $options[] = ['value' => 'PR', 'label' => 'Paraná'];
        $options[] = ['value' => 'PE', 'label' => 'Pernambuco'];
        $options[] = ['value' => 'PI', 'label' => 'Piauí'];
        $options[] = ['value' => 'RJ', 'label' => 'Rio de Janeiro'];
        $options[] = ['value' => 'RN', 'label' => 'Rio Grande do Norte'];
        $options[] = ['value' => 'RS', 'label' => 'Rio Grande do Sul'];
        $options[] = ['value' => 'RO', 'label' => 'Rondônia'];
        $options[] = ['value' => 'RR', 'label' => 'Roraima'];
        $options[] = ['value' => 'SC', 'label' => 'Santa Catarina'];
        $options[] = ['value' => 'SP', 'label' => 'São Paulo'];
        $options[] = ['value' => 'SE', 'label' => 'Sergipe'];
        $options[] = ['value' => 'TO', 'label' => 'Tocantins'];

        return $options;
    }
}
