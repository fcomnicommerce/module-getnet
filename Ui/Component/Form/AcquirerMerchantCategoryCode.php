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

class AcquirerMerchantCategoryCode extends DataObject implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];

        $options[] = ['value' => 4001, 'label' => 'ACADEMIA DE GINASTICA - ARTES MARCIAIS - PERSONAL'];
        $options[] = ['value' => 5014, 'label' => 'CHAVEIROS'];
        $options[] = ['value' => 2044, 'label' => 'CARPINTARIA - SERVICOS'];
        $options[] = ['value' => 5042, 'label' => 'CONCRETO - SERVICOS'];
        $options[] = ['value' => 2086, 'label' => 'CONSERTO E MANUTENCAO DE COMPUTADORES'];
        $options[] = ['value' => 2075, 'label' => 'CORRETOR DE SEGUROS'];
        $options[] = ['value' => 6009, 'label' => 'DENTISTAS EM GERAL'];
        $options[] = ['value' => 5013, 'label' => 'ENGENHEIROS/ARQUITETOS'];
        $options[] = ['value' => 6018, 'label' => 'FARMACIAS E FARMACÊUTICOS (MANIPULACAO)'];
        $options[] = ['value' => 2045, 'label' => 'FORNECEDOR ESPECIALIZADO N CLASSIFICADO'];
        $options[] = ['value' => 7008, 'label' => 'HOTEL - FLAT'];
        $options[] = ['value' => 2042, 'label' => 'HORTICULTURA/SERVICO DE JARDINAGEM'];
        $options[] = ['value' => 5039, 'label' => 'LOJAS DE AQUECEDORES, REFRIGERADORES E SOLDAGENS'];
        $options[] = ['value' => 6008, 'label' => 'MEDICOS EM GERAL'];
        $options[] = ['value' => 5040, 'label' => 'MATERIAL ELETRICO/LUSTRES E LUMINARIAS'];
        $options[] = ['value' => 2119, 'label' => 'PROFISSIONAL AUTONOMO'];
        $options[] = ['value' => 6016, 'label' => 'PEDICURO'];
        $options[] = ['value' => 2094, 'label' => 'PRODUCAO E DISTRIBUICAO DE VIDEO TAPE'];
        $options[] = ['value' => 8009, 'label' => 'TAXI'];
        $options[] = ['value' => 2128, 'label' => 'VENDA DE TERCEIROS (MARKETPLACES)'];

        return $options;
    }
}
