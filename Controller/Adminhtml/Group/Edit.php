<?php
declare(strict_types=1);

namespace SM\Customer\Controller\Adminhtml\Group;

use Magento\Backend\Model\View\Result\Forward;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use SM\Customer\Controller\Adminhtml\Group;

/**
 * Class Edit
 * @package SM\Customer\Controller\Adminhtml\Group
 */
class Edit extends Group
{
    /**
     * @return Forward|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        return $this->_forwardFactory->create()->forward('create');
    }
}
