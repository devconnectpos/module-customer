<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace SM\Customer\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;
    /**
     * @var AttributeSetFactory
     */
    protected $attributeSetFactory;
    /**
     * @var State
     */
    private $state;

    /**
     * UpgradeSchema constructor.
     *
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory  $attributeSetFactory
     * @param State                $state
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        State $state
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        try {
            $this->state->emulateAreaCode(
                Area::AREA_ADMINHTML, function (SchemaSetupInterface $setup, ModuleContextInterface $context) {
                if (version_compare($context->getVersion(), '0.0.2', '<=')) {
                    $this->addPhoneAttribute($setup);
                }
                if (version_compare($context->getVersion(), '0.0.4', '<=')) {
                    $this->addAvatarAttribute($setup);
                    $this->addVerifaceAttribute($setup);
                }
                if (version_compare($context->getVersion(), '0.0.5', '<=')) {
                    $this->addGuestId($setup);
                }
                if (version_compare($context->getVersion(), '0.0.6', '<=')) {
                    $this->add2ndTelephone($setup);
                }
            }, [$setup, $context]
            );
        } catch (\Throwable $e) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $logger = $objectManager->get('Psr\Log\LoggerInterface');
            $logger->critical("====> [CPOS] Failed to upgrade customer schema: {$e->getMessage()}");
            $logger->critical($e->getTraceAsString());
            echo "Failed to upgrade customer schema: ".$e->getMessage()."\n";
        }
    }

    /**
     * Execute all schema upgrade at once
     *
     * @param SchemaSetupInterface $setup
     * @param OutputInterface      $output
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function execute(SchemaSetupInterface $setup, OutputInterface $output)
    {
        $output->writeln('  |__ Add telephone attribute');
        $this->addPhoneAttribute($setup);
        $output->writeln('  |__ Add 2nd telephone attribute');
        $this->add2ndTelephone($setup);
        $output->writeln('  |__ Add avatar attribute');
        $this->addAvatarAttribute($setup);
        $output->writeln('  |__ Add veriface attribute');
        $this->addVerifaceAttribute($setup);
        $output->writeln('  |__ Add guest ID attribute');
        $this->addGuestId($setup);
        $output->writeln('  |__ Add note attribute');
        $this->addNote($setup);
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    protected function addPhoneAttribute(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();

        // Skip if the attribute exists
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'retail_telephone');

        if ($attribute) {
            $setup->endSetup();

            return;
        }

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'retail_telephone',
            [
                'type'               => 'varchar',
                'label'              => 'Telephone',
                'input'              => 'text',
                'required'           => false,
                'visible'            => true,
                'user_defined'       => true,
                'sort_order'         => 1000,
                'position'           => 1000,
                'system'             => 0,
                'attribute_set_id'   => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms'      => ['adminhtml_customer'],
            ]
        );

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    protected function addAvatarAttribute(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();

        // Skip if the attribute exists
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'retail_avatar');

        if ($attribute) {
            $setup->endSetup();

            return;
        }

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'retail_avatar',
            [
                'type'               => 'varchar',
                'label'              => 'Avatar',
                'input'              => 'text',
                'required'           => false,
                'visible'            => true,
                'user_defined'       => true,
                'sort_order'         => 1000,
                'position'           => 1000,
                'system'             => 0,
                'attribute_set_id'   => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms'      => ['adminhtml_customer'],
            ]
        );

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    protected function addVerifaceAttribute(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();

        // Skip if the attribute exists
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'retail_veriface');

        if ($attribute) {
            $setup->endSetup();

            return;
        }

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'retail_veriface',
            [
                'type'               => 'varchar',
                'label'              => 'Veriface',
                'input'              => 'text',
                'required'           => false,
                'visible'            => true,
                'user_defined'       => true,
                'sort_order'         => 1000,
                'position'           => 1000,
                'system'             => 0,
                'attribute_set_id'   => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms'      => ['adminhtml_customer'],
            ]
        );

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    protected function addGuestId(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();

        // Skip if the attribute exists
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'retail_guest_id');

        if ($attribute) {
            $setup->endSetup();

            return;
        }

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'retail_guest_id',
            [
                'type'               => 'varchar',
                'label'              => 'Retail Guest Id',
                'input'              => 'text',
                'required'           => false,
                'visible'            => true,
                'user_defined'       => true,
                'sort_order'         => 1000,
                'position'           => 1000,
                'system'             => 0,
                'attribute_set_id'   => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms'      => ['adminhtml_customer'],
            ]
        );

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Validate_Exception
     */
    protected function add2ndTelephone(SchemaSetupInterface $setup)
    {
        $setup->startSetup();
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create();

        // Skip if the attribute exists
        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'retail_telephone_2');

        if ($attribute) {
            $setup->endSetup();

            return;
        }

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var AttributeSet $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            'retail_telephone_2',
            [
                'type'                  => 'varchar',
                'label'                 => 'Secondary Telephone',
                'input'                 => 'text',
                'required'              => false,
                'visible'               => true,
                'user_defined'          => true,
                'sort_order'            => 1000,
                'position'              => 1000,
                'system'                => 0,
                'is_used_in_grid'       => true,
                'is_visible_in_grid'    => true,
                'is_filterable_in_grid' => true,
                'is_searchable_in_grid' => true,
                'attribute_set_id'      => $attributeSetId,
                'attribute_group_id'    => $attributeGroupId,
                'used_in_forms'         => ['adminhtml_customer'],
            ]
        );

        $setup->endSetup();
    }
}
