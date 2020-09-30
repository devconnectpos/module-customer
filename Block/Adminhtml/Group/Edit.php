<?php
declare(strict_types=1);

namespace SM\Customer\Block\Adminhtml\Group;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use SM\Customer\Controller\Adminhtml\Group;
use SM\Customer\Model\ScgCustomerGroup;

/**
 * Class Edit
 * @package SM\Customer\Block\Adminhtml\Group
 */
class Edit extends Container
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * Edit constructor.
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_group';
        $this->_blockGroup = 'SM_Customer';

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('delete', 'label', __('Delete'));
    }

    /**
     * Retrieve the header text, either editing an existing group or creating a new one.
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        /** @var ScgCustomerGroup $group */
        $group = $this->_registry->registry(Group::CURRENT_GROUP);
        if ($group->getId()) {
            return __('Edit Customer Group "%1"', $this->escapeHtml($group->getName()));

        }

        return __('New Customer Group');
    }

    /**
     * Retrieve CSS classes added to the header.
     *
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'icon-head head-customer-groups';
    }
}
