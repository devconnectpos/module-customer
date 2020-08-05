<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace SM\Customer\Setup;

use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    protected $customerSetupFactory;
    protected $attributeSetFactory;
    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * UpgradeSchema constructor.
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Framework\App\State $state
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory  = $attributeSetFactory;
	    try {
		    $state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
	    } catch (LocalizedException $e) {
	    }
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
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
}
