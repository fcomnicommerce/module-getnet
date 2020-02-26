<?php
/**
 * Copyright © FCamara - Formação e Consultoria. All rights reserved.
 * @author Guilherme Miguelete <guilherme.miguelete@fcamara.com.br>
 * https://www.fcamara.com.br
 */

namespace FCamara\Getnet\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'is_recurrence');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'is_recurrence',
            [
                'attribute_set_id' => 4,
                'type' => 'int',
                'label' => 'Is Recurrence?',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'Recurrence Information',
                'backend' => '',
                'frontend' => '',
                'note' => '',
                'class' => '',
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'recurrence_name');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'recurrence_name',
            [
                'attribute_set_id' => 4,
                'type' => 'varchar',
                'label' => 'Name',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Recurrence Information',
                'backend' => '',
                'frontend' => '',
                'note' => __('Nome do plano.'),
                'class' => '',
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'recurrence_description');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'recurrence_description',
            [
                'attribute_set_id' => 4,
                'type' => 'varchar',
                'label' => 'Description',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Recurrence Information',
                'backend' => '',
                'frontend' => '',
                'note' => __('Descrição do plano.'),
                'class' => '',
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'recurrence_amount');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'recurrence_amount',
            [
                'attribute_set_id' => 4,
                'type' => 'decimal',
                'label' => 'Amount',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Recurrence Information',
                'backend' => '',
                'frontend' => '',
                'note' => __('Valor do plano.'),
                'class' => '',
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'recurrence_sales_tax');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'recurrence_sales_tax',
            [
                'attribute_set_id' => 4,
                'type' => 'int',
                'label' => 'Sales Tax',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Recurrence Information',
                'backend' => '',
                'frontend' => '',
                'note' => __('Valor de impostos. Default: 0'),
                'class' => '',
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'recurrence_product_type');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'recurrence_product_type',
            [
                'attribute_set_id' => 4,
                'type' => 'varchar',
                'label' => 'Product Type',
                'input' => 'select',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'source' => 'FCamara\Getnet\Model\Eav\Entity\Attribute\Source\ProductType',
                'group' => 'Recurrence Information',
                'backend' => '',
                'frontend' => '',
                'note' => __('Identificador do tipo de produto vendido dentre as opções.'),
                'class' => '',
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'recurrence_period_type');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'recurrence_period_type',
            [
                'attribute_set_id' => 4,
                'type' => 'varchar',
                'label' => 'Period Type',
                'input' => 'select',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'source' => 'FCamara\Getnet\Model\Eav\Entity\Attribute\Source\PeriodType',
                'group' => 'Recurrence Information',
                'backend' => '',
                'frontend' => '',
                'note' => __('Tipo de periodicidade.'),
                'class' => '',
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'recurrence_specific_cycle_in_days');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'recurrence_specific_cycle_in_days',
            [
                'attribute_set_id' => 4,
                'type' => 'int',
                'label' => 'Specific Cycle in Days',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Recurrence Information',
                'backend' => '',
                'frontend' => '',
                'note' => __('Se a duração do plano for 12 e o ciclo específico for 10, significa que a cobrança ocorrerá de 10 em 10 dias num todal de 12 cobranças.'),
                'class' => '',
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'recurrence_billing_cycle');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'recurrence_billing_cycle',
            [
                'attribute_set_id' => 4,
                'type' => 'int',
                'label' => 'Billing Cycle',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Recurrence Information',
                'backend' => '',
                'frontend' => '',
                'note' => __('Duração do plano com base no tipo de periodicidade. Por exemplo, para um plano de 12 meses, onde a cobrança será realizada mensalmente (type = monthly) o valor a ser atribuído é 12.'),
                'class' => '',
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'recurrence_plan_id');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'recurrence_plan_id',
            [
                'attribute_set_id' => 4,
                'type' => 'varchar',
                'label' => 'Plan Id',
                'input' => 'text',
                'required' => false,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Recurrence Information',
                'backend' => '',
                'frontend' => '',
                'note' => '',
                'class' => '',
                'visible' => true,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => '',
                'readonly' => true
            ]
        );
    }
}
