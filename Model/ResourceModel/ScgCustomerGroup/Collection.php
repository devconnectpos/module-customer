<?php
declare(strict_types=1);

namespace SM\Customer\Model\ResourceModel\ScgCustomerGroup;

use Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection;
use SM\Customer\Model\ScgCustomerGroup as Model;
use SM\Customer\Model\ResourceModel\ScgCustomerGroup as ResourceModel;

/**
 * Class Collection
 * @package SM\Customer\Model\ResourceModel\ScgCustomerGroup
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
