<?php
declare(strict_types=1);

namespace SM\Customer\Block\Adminhtml\Group\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Exception\LocalizedException;
use SM\Customer\Model\ScgCustomerGroup;
use SM\Customer\Controller\Adminhtml\Group;

/**
 * Class Form
 * @package SM\Customer\Block\Adminhtml\Group\Edit
 */
class Form extends Generic
{
    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $form = $this->_formFactory->create();
        /** @var ScgCustomerGroup $group */
        $group = $this->_coreRegistry->registry(Group::CURRENT_GROUP);

        $fieldset = $form->addFieldset('sm_scg_group_info', ['legend' => __('Group Information')]);
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Group Name'),
                'title' => __('Group Name'),
                'class' => 'required-entry validate-length maximum-length-255',
                'required' => true
            ]
        );

        if ($group->getId() !== null) {
            $form->addField('id', 'hidden', ['name' => 'id', 'value' => $group->getId()]);
        }

        $form->addValues(['id' => $group->getId(), 'name' => $group->getName()]);
        if ($this->_backendSession->getScgCustomerGroupData()) {
            $form->addValues($this->_backendSession->getScgCustomerGroupData());
            $this->_backendSession->setScgCustomerGroupData(null);
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('smcustomer/*/save'));
        $form->setMethod('post');
        $this->setForm($form);
    }
}
