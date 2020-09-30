<?php
declare(strict_types=1);

namespace SM\Customer\Setup;

use Exception;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Customer\Model\Customer;
use SM\Customer\Model\Config\Source\ScgCustomerGroups;
use Zend_Validate_Exception;

/**
 * Class UpgradeData
 * @package SM\Customer\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    protected $_customerSetupFactory;

    /**
     * @var AttributeSetFactory
     */
    protected $_attributeSetFactory;

    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->_customerSetupFactory = $customerSetupFactory;
        $this->_attributeSetFactory = $attributeSetFactory;
    }

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws Exception
     * @throws Zend_Validate_Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.0.8', '<=')) {
            $this->addScgCustomerGroupAttribute($setup);
        }
    }

    /**
     * @param $setup
     * @throws Exception
     * @throws Zend_Validate_Exception
     */
    public function addScgCustomerGroupAttribute($setup)
    {
        $customerSetup = $this->_customerSetupFactory->create(['setup' => $setup]);
        $eavConfig = $customerSetup->getEavConfig();
        if (($attr = $eavConfig->getAttribute(Customer::ENTITY, ScgCustomerGroups::CODE)) && $attr->getId()) {
            return;
        }

        $customerEntity = $eavConfig->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        $attributeSet = $this->_attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(
            Customer::ENTITY,
            ScgCustomerGroups::CODE,
            [
                'input'                      => 'select',
                'type'                       => 'int',
                'label'                      => 'SCG Customer Group',
                'required'                   => false,
                'visible'                    => true,
                'user_defined'               => true,
                'sort_order'                 => 1500,
                'position'                   => 1500,
                'system'                     => 0,
                'searchable'                 => false,
                'filterable'                 => false,
                'comparable'                 => false,
                'visible_on_front'           => false,
                'visible_in_advanced_search' => false,
                'is_html_allowed_on_front'   => false,
                'used_for_promo_rules'       => true,
                'source'                     => ScgCustomerGroups::class,
                'frontend_class'             => '',
                'global'                     => ScopedAttributeInterface::SCOPE_GLOBAL,
                'unique'                     => false,
            ]
        );

        $newAttr = $customerSetup->getEavConfig()->clear()
            ->getAttribute(Customer::ENTITY, ScgCustomerGroups::CODE);

        if ($newAttr) {
            $newAttr->addData([
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms'      => ['adminhtml_customer']
            ]);

            $newAttr->save();
        }
    }
}
