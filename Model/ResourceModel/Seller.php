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

namespace FCamara\Getnet\Model\ResourceModel;

class Seller extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Seller constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('fcamara_getnet_seller', 'entity_id');
    }
}
