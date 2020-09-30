<?php
declare(strict_types=1);

namespace SM\Customer\Controller\Adminhtml\Group;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use SM\Customer\Model\ResourceModel\ScgCustomerGroup\CollectionFactory as ScgCustomerGroupCollection;

/**
 * Class MassDelete
 * @package SM\Customer\Controller\Adminhtml\Group
 */
class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var ScgCustomerGroupCollection
     */
    protected $_scgCustomerGroupCollection;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param ScgCustomerGroupCollection $scgCustomerGroupCollection
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ScgCustomerGroupCollection $scgCustomerGroupCollection
    ) {
        $this->_filter = $filter;
        $this->_scgCustomerGroupCollection = $scgCustomerGroupCollection;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $groups = $this->_filter->getCollection($this->_scgCustomerGroupCollection->create());
        $totalItems = $groups->getSize();
        try {
            foreach ($groups as $group) {
                $group->delete();
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $redirect->setPath('*/*/');
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $totalItems));

        return $redirect;
    }
}
