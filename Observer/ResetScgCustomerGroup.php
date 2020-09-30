<?php
declare(strict_types=1);

namespace SM\Customer\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use SM\Customer\Model\ScgCustomerGroup;
use SM\Customer\Model\Config\Source\ScgCustomerGroups;

/**
 * Class ResetScgCustomerGroup
 * @package SM\Customer\Observer
 */
class ResetScgCustomerGroup implements ObserverInterface
{
    /**
     * @var CustomerCollection
     */
    protected $_customerCollection;

    /**
     * ResetScgCustomerGroup constructor.
     * @param CustomerCollection $customerCollection
     */
    public function __construct(
        CustomerCollection $customerCollection
    ) {
        $this->_customerCollection = $customerCollection;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        /** @var ScgCustomerGroup $group */
        $group = $observer->getData('cpos_scg_customer_group');

        $customers = $this->_customerCollection->create()
            ->addAttributeToSelect(ScgCustomerGroups::CODE)
            ->addAttributeToFilter(ScgCustomerGroups::CODE, $group->getId())
            ->load();

        if ($customers->getSize() <= 0) {
            return;
        }

        /** @var Customer $customer */
        foreach ($customers as $customer) {
            $customer->setData(ScgCustomerGroups::CODE, 0)->save();
        }
    }
}
