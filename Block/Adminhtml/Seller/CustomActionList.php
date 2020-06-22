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

namespace FCamara\Getnet\Block\Adminhtml\Seller;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;

class CustomActionList extends Container
{
    /**
     * CustomActionList constructor.
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @return Container
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'add_new_seller',
            'label' => __('Add New'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => \Magento\Backend\Block\Widget\Button\SplitButton::class,
            'options' => $this->_getCustomActionListOptions(),
        ];

        $this->buttonList->add('add_new', $addButtonProps);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'CustomActionList' split button
     *
     * @return array
     */
    protected function _getCustomActionListOptions()
    {
        $splitButtonOptions = [
            'pf' => [
                'label' => __('Pessoa Física'),
                'onclick' => "setLocation('" . $this->getUrl('fcamara_getnet/seller/newaction') . "')",
                'default' => true
            ],
            'pj' => [
                'label' => __('Pessoa Jurídica'),
                'onclick' => "setLocation('" . $this->getUrl('fcamara_getnet/seller/newactionpj') . "')",
                'default' => false
            ]
        ];

        return $splitButtonOptions;
    }
}
