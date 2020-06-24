<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SM\Customer\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use SM\Customer\Model\Config\Source\Options;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    protected $customerSetupFactory;
    protected $attributeSetFactory;

    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory  = $attributeSetFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $this->addPhoneAttribute();
        }
        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $this->addAvatarAttribute();
            $this->addVerifaceAttribute();
        }
        if (version_compare($context->getVersion(), '0.0.5', '<')) {
            $this->addGuestId();
        }
        if (version_compare($context->getVersion(), '0.0.6', '<')) {
            $this->addOccupationAttribute();
        }
    }

    protected function addPhoneAttribute()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet     = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'retail_telephone',
            [
                'type'         => 'varchar',
                'label'        => 'Telephone',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'sort_order'   => 1000,
                'position'     => 1000,
                'system'       => 0,
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'retail_telephone')
                                   ->addData(
                                       [
                                           'attribute_set_id'   => $attributeSetId,
                                           'attribute_group_id' => $attributeGroupId,
                                           'used_in_forms'      => ['adminhtml_customer'],
                                       ]
                                   );

        $attribute->save();
    }

    protected function addAvatarAttribute()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet     = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'retail_avatar',
            [
                'type'         => 'varchar',
                'label'        => 'Avatar',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'sort_order'   => 1000,
                'position'     => 1000,
                'system'       => 0,
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'retail_avatar')
                                   ->addData(
                                       [
                                           'attribute_set_id'   => $attributeSetId,
                                           'attribute_group_id' => $attributeGroupId,
                                           'used_in_forms'      => ['adminhtml_customer'],
                                       ]
                                   );

        $attribute->save();
    }

    protected function addVerifaceAttribute()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet     = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'retail_veriface',
            [
                'type'         => 'varchar',
                'label'        => 'Veriface',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'sort_order'   => 1000,
                'position'     => 1000,
                'system'       => 0,
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'retail_veriface')
                                   ->addData(
                                       [
                                           'attribute_set_id'   => $attributeSetId,
                                           'attribute_group_id' => $attributeGroupId,
                                           'used_in_forms'      => ['adminhtml_customer'],
                                       ]
                                   );

        $attribute->save();
    }

    protected function addGuestId()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet     = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'retail_guest_id',
            [
                'type'         => 'varchar',
                'label'        => 'Retail Guest Id',
                'input'        => 'text',
                'required'     => false,
                'visible'      => true,
                'user_defined' => true,
                'sort_order'   => 1000,
                'position'     => 1000,
                'system'       => 0,
            ]
        );

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'retail_guest_id')
                                   ->addData(
                                       [
                                           'attribute_set_id'   => $attributeSetId,
                                           'attribute_group_id' => $attributeGroupId,
                                           'used_in_forms'      => ['adminhtml_customer'],
                                       ]
                                   );

        $attribute->save();
    }

    protected function addOccupationAttribute()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet     = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'occupation',
            [
                'input'                      => 'select',
                'type'                       => 'int',
                'label'                      => 'Occupation',
                'required'                   => false,
                'visible'                    => true,
                'user_defined'               => true,
                'sort_order'                 => 1000,
                'position'                   => 1000,
                'system'                     => 0,
                'searchable'                 => false,
                'filterable'                 => false,
                'comparable'                 => false,
                'visible_on_front'           => false,
                'visible_in_advanced_search' => false,
                'is_html_allowed_on_front'   => false,
                'used_for_promo_rules'       => true,
                'source'                     => Options::class,
                'frontend_class'             => '',
                'global'                     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'unique'                     => false,
            ]
        );

        $attributeOccupation = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'occupation')
                                   ->addData(
                                       [
                                           'attribute_set_id'   => $attributeSetId,
                                           'attribute_group_id' => $attributeGroupId,
                                           'used_in_forms'      => ['adminhtml_customer'],
                                       ]
                                   );

        $attributeOccupation->save();

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'customer_occupation_other_name',
            [
                'type'                       => Table::TYPE_TEXT,
                'label'                      => 'Occupation Name',
                'length'                     => 255,
                'required'                   => false,
                'visible'                    => true,
                'user_defined'               => true,
                'sort_order'                 => 1000,
                'position'                   => 1000,
                'system'                     => 0,
                'searchable'                 => false,
                'filterable'                 => false,
                'comparable'                 => false,
                'visible_on_front'           => false,
                'visible_in_advanced_search' => false,
                'is_html_allowed_on_front'   => false,
                'used_for_promo_rules'       => true,
                'frontend_class'             => '',
                'global'                     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'unique'                     => false,
            ]
        );

        $attributeOccupationName = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'customer_occupation_other_name')
                                   ->addData(
                                       [
                                           'attribute_set_id'   => $attributeSetId,
                                           'attribute_group_id' => $attributeGroupId,
                                           'used_in_forms'      => ['adminhtml_customer'],
                                       ]
                                   );

        $attributeOccupationName->save();
    }
}
