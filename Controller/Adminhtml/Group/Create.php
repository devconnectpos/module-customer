<?php
declare(strict_types=1);

namespace SM\Customer\Controller\Adminhtml\Group;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use SM\Customer\Block\Adminhtml\Group\Edit as EditAlias;
use SM\Customer\Controller\Adminhtml\Group;

/**
 * Class Create
 * @package SM\Customer\Controller\Adminhtml\Group
 */
class Create extends Group
{
    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $group = $this->_initGroup();
        $groupId = $group->getId();

        $page = $this->_pageFactory->create();
        $page->setActiveMenu('Magento_Customer::customer_group');
        $page->getConfig()->getTitle()->prepend(__('New SCG Customer Group'));
        if ($groupId) {
            $page->getConfig()->getTitle()->prepend($group->getName());
        }

        $page->getLayout()
            ->addBlock(EditAlias::class, 'group', 'content')
            ->setEditMode((bool) $groupId);


        return $page;
    }
}
