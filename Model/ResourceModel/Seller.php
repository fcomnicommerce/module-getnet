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
     * DB connection
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * Seller constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Initialize resource model. Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('fcamara_getnet_seller', 'entity_id');
        $this->connection = $this->getConnection();
    }

    /**
     * @param $sellerId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadBySubSellerId($sellerId)
    {
        $select = $this->connection->select()->from($this->getMainTable())->where('subseller_id=:subseller_id');
        $result = $this->connection->fetchRow($select, ['subseller_id' => $sellerId]);

        if (!$result) {
            return [];
        }

        return $result;
    }
}
