<?php
declare(strict_types=1);

namespace SM\Customer\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ScgCustomerGroup
 * @package SM\Customer\Model\ResourceModel
 */
class ScgCustomerGroup extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cpos_scg_customer_group', 'entity_id');
    }
}
