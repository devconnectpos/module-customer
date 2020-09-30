<?php
declare(strict_types=1);

namespace SM\Customer\Model;

use Magento\Framework\Model\AbstractModel;
use SM\Customer\Api\Data\ScgCustomerGroupInterface;
use SM\Customer\Model\ResourceModel\ScgCustomerGroup as ResourceModel;

/**
 * Class ScgCustomerGroup
 * @package SM\Customer\Model
 */
class ScgCustomerGroup extends AbstractModel implements ScgCustomerGroupInterface
{
    const NAME = 'name';
    const NOTE = 'note';

    /**
     * @var string
     */
    protected $_eventPrefix = 'cpos_scg_customer_group';

    /**
     * @var string
     */
    protected $_eventObject = 'cpos_scg_customer_group';

    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->getData(self::NOTE);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setNote($value)
    {
        return $this->setData(self::NOTE, $value);
    }
}
