<?php
declare(strict_types=1);

namespace SM\Customer\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use SM\Customer\Model\ScgCustomerGroup;
use SM\Customer\Model\ScgCustomerGroupFactory;

/**
 * Class Group
 * @package SM\Customer\Controller\Adminhtml
 */
abstract class Group extends Action
{
    const ADMIN_RESOURCE = 'SM_Customer::scg_customer_group';
    const CURRENT_GROUP = 'current_group';

    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var ScgCustomerGroupFactory
     */
    protected $_scgCustomerGroupFactory;

    /**
     * @var ForwardFactory
     */
    protected $_forwardFactory;

    /**
     * Group constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Registry $registry
     * @param ScgCustomerGroupFactory $scgCustomerGroupFactory
     * @param ForwardFactory $forwardFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Registry $registry,
        ScgCustomerGroupFactory $scgCustomerGroupFactory,
        ForwardFactory $forwardFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->_registry = $registry;
        $this->_scgCustomerGroupFactory = $scgCustomerGroupFactory;
        $this->_forwardFactory = $forwardFactory;

        parent::__construct($context);
    }

    /**
     * @return ScgCustomerGroup
     */
    protected function _initGroup()
    {
        $groupId = $this->getRequest()->getParam('id');
        $group = $this->_scgCustomerGroupFactory->create();
        if ($groupId) {
            $group->load($groupId);
        }

        $this->_registry->register(self::CURRENT_GROUP, $group);

        return $group;
    }
}
