<?php
declare(strict_types=1);

namespace SM\Customer\Model\Config\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use SM\Customer\Model\ResourceModel\ScgCustomerGroup\Collectionfactory as ScgCustomerGroupCollection;
use SM\Customer\Model\ScgCustomerGroup;

/**
 * Class ScgCustomerGroups
 * @package SM\Customer\Model\Config\Source
 */
class ScgCustomerGroups extends AbstractSource
{
    const CODE = 'scg_customer_group';

    /**
     * @var ScgCustomerGroupCollection
     */
    protected $_scgCustomerGroupCollection;

    /**
     * ScgCustomerGroups constructor.
     * @param ScgCustomerGroupCollection $scgCustomerGroupCollection
     */
    public function __construct(
        ScgCustomerGroupCollection $scgCustomerGroupCollection
    ) {
        $this->_scgCustomerGroupCollection = $scgCustomerGroupCollection;
    }

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $groupOptions = [['label' => __('Not Specified'), 'value' => '0']];
        $groups = $this->_scgCustomerGroupCollection->create();
        /** @var ScgCustomerGroup $group */
        foreach ($groups as $group) {
            $groupOptions[] = ['label' => $group->getName(), 'value' => $group->getId()];
        }

        $this->_options = $groupOptions;
        return $this->_options;
    }
}
