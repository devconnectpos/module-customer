<?php
declare(strict_types=1);

namespace SM\Customer\Controller\Adminhtml\Group;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use SM\Customer\Controller\Adminhtml\Group;

/**
 * Class Delete
 * @package SM\Customer\Controller\Adminhtml\Group
 */
class Delete extends Group
{

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        if ($groupId = $this->getRequest()->getParam('id')) {
            $group = $this->_initGroup();
            $group->load($groupId);

            try {
                $group->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the customer group.'));
                return $redirect->setPath('smcustomer/group');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $redirect->setPath('smcustomer/group/edit', ['id' => $groupId]);
            }
        }

        $this->messageManager->addErrorMessage(__('This customer group no longer exists'));
        return $redirect->setPath('smcustomer/group');
    }
}
