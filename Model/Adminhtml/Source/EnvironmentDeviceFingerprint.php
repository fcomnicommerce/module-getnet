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
 * @copyright Copyright (c) 2020 FCamara Formação e Consultoria
 * @Agency    FCamara Formação e Consultoria, Inc. (http://www.fcamara.com.br)
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */

namespace FCamara\Getnet\Model\Adminhtml\Source;


class EnvironmentDeviceFingerprint implements \Magento\Framework\Data\OptionSourceInterface
{
    const SANDBOX_ENVIRONMENT = '1snn5n9w';
    const PRODUCTION_ENVIRONMENT = 'k8vif92e';
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SANDBOX_ENVIRONMENT,
                'label' => __('Sandbox')
            ],
            [
                'value' => self::PRODUCTION_ENVIRONMENT,
                'label' => __('Production')
            ]
        ];
    }
}