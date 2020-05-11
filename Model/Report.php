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

namespace FCamara\Getnet\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Report extends AbstractModel implements IdentityInterface
{
    /**
     * @const string
     */
    const CACHE_TAG = 'fcamara_getnet_report';

    /**
     * @var string
     */
    protected $_cacheTag = 'fcamara_getnet_report';

    /**
     * @var string
     */
    protected $_eventPrefix = 'fcamara_getnet_report';

    /**
     * Report Model Construct
     */
    protected function _construct()
    {
        $this->_init('FCamara\Getnet\Model\ResourceModel\Report');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
