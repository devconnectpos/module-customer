<?php
declare(strict_types=1);

namespace SM\Customer\Controller\Adminhtml\Group;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use SM\Customer\Controller\Adminhtml\Group;
use SM\Customer\Model\ScgCustomerGroup;

/**
 * Class Save
 * @package SM\Customer\Controller\Adminhtml\Group
 */
class Save extends Group
{
    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $group = $this->_initGroup();
        $groupId = $this->getRequest()->getParam('id');
        $redirect = $this->resultRedirectFactory->create();
        $data = [
            $group->getIdFieldName() => $groupId,
            ScgCustomerGroup::NAME => $this->getRequest()->getParam('name')
        ];

        $group->addData($data);

        try {
            $group->save();
            $redirect->setPath('smcustomer/group');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            $redirect->setPath('smcustomer/group/create');
            if ($groupId) {
                $data['id'] = $groupId;
                unset($data[$group->getIdFieldName()]);
                $redirect->setPath('smcustomer/group/edit', ['id' => $groupId]);
            }

            $this->_getSession()->setScgCustomerGroupData($data);

        }

        return $redirect;
    }

}
