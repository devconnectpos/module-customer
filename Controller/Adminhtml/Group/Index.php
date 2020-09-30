<?php
declare(strict_types=1);

namespace SM\Customer\Controller\Adminhtml\Group;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use SM\Customer\Controller\Adminhtml\Group;

/**
 * Class Index
 * @package SM\Customer\Controller\Adminhtml\Group
 */
class Index extends Group
{
    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $resultPage = $this->_pageFactory->create();
        $resultPage->setActiveMenu('Magento_Customer::customer_group');
        $resultPage->getConfig()->getTitle()->prepend(__('SCG Customer Groups'));

        return $resultPage;
    }
}
