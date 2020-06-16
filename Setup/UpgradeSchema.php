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
 * @author    Danilo Cavalcanti de Moura <danilo.moura@fcamara.com.br>
 */

namespace FCamara\Getnet\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package FCamara\Getnet\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.3.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_order'),
                'subscription_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Recurrence Subscription Id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.4.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('quote'),
                'subscription_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'default' => '',
                    'comment' => 'Recurrence Subscription Id'
                ]
            );
        }

        if (version_compare($context->getVersion(), '0.5.0', '<')) {
            if (!$installer->tableExists('getnet_reports')) {
                $table = $installer->getConnection()->newTable($installer->getTable('getnet_reports'))
                    ->addColumn(
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true,
                        ],
                        'Entity ID'
                    )
                    ->addColumn(
                        'customer_name',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Customer Name'
                    )
                    ->addColumn(
                        'customer_email',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Customer Email'
                    )
                    ->addColumn(
                        'status',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Status'
                    )
                    ->addColumn(
                        'status_message',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Status Message'
                    )
                    ->addColumn(
                        'payment_type',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Payment Type'
                    )
                    ->addColumn(
                        'payment_type',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Payment Type'
                    )
                    ->addColumn(
                        'request_body',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Request Body'
                    )
                    ->addColumn(
                        'response_body',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Response Body'
                    )
                    ->addColumn(
                        'created_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                        'Created At'
                    )
                    ->setComment('Getnet Reports Table');

                $installer->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '0.6.0', '<')) {
            $installer = $setup;
            if (!$installer->tableExists('fcamara_getnet_seller')) {
                $table = $installer->getConnection()->newTable($installer->getTable('fcamara_getnet_seller'))
                    ->addColumn(
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary' => true,
                            'unsigned' => true,
                        ],
                        'Entity ID'
                    )
                    ->addColumn(
                        'merchant_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Merchant ID'
                    )
                    ->addColumn(
                        'type',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'Type'
                    )
                    ->addColumn(
                        'legal_document_number',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null
                    )
                    ->addColumn(
                        'legal_name',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'birth_date',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
                        null
                    )
                    ->addColumn(
                        'mothers_name',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'occupation',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'business_address',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'mailing_address',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'working_hours',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'phone',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'cellphone',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'email',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'acquirer_merchant_category_code',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null
                    )
                    ->addColumn(
                        'bank_accounts',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'list_commissions',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'accepted_contract',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'liability_chargeback',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'marketplace_store',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'enabled',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'status',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'capture_payments_enabled',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'payment_plan',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null
                    )
                    ->addColumn(
                        'anticipation_enabled',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null
                    )
                    ->addColumn(
                        'trade_name',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'state_fiscal_document_number',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255
                    )
                    ->addColumn(
                        'created_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                        'Created At'
                    )
                    ->setComment('Getnet Seller Table');
                $installer->getConnection()->createTable($table);
            }
        }

        $setup->endSetup();
    }
}
