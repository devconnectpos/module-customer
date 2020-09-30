<?php
/**
 * Created by mr.vjcspy@gmail.com - khoild@smartosc.com.
 * Date: 24/10/2016
 * Time: 15:22
 */

namespace SM\Customer\Repositories;

use Exception;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\GroupFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Sales\Model\Order;
use SM\Core\Api\Data\CountryRegion;
use SM\Core\Api\Data\CustomerAddress;
use SM\Core\Api\Data\CustomerGroup;
use SM\Core\Api\Data\CustomerOccupation;
use SM\Core\Api\Data\ScgCustomerGroup;
use SM\Core\Api\Data\XCustomer;
use SM\Core\Model\DataObject;
use SM\Performance\Helper\RealtimeManager;
use SM\XRetail\Helper\DataConfig;
use SM\XRetail\Repositories\Contract\ServiceAbstract;

/**
 * Class CustomerManagement
 *
 * @package SM\Customer\Repositories
 */
class CustomerManagement extends ServiceAbstract
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;
    /**
     * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    protected $countryCollection;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
     */
    protected $customerGroupCollectionFactory;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $configShare;
    /**
     * @var \SM\Customer\Helper\Data
     */
    protected $customerHelper;
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;
    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;
    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    private $customerConfigShare;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Sale\CollectionFactory
     */
    protected $salesCollectionFactory;
    /**
     * @var \SM\Integrate\Helper\Data
     */
    private $integrateHelper;
    /**
     * @var \SM\Wishlist\Repositories\WishlistManagement
     */
    private $wishlistManagement;

    /**
     * @var \SM\Customer\Model\ResourceModel\Grid\CollectionFactory
     */
    private $customerGridCollectionFactory;

    /**
     *
     * @var \Magento\Newsletter\Model\Subscriber
     */
    private $subscriberFactory;

    /**
     * @var \Magento\Customer\Api\GroupManagementInterface
     */
    protected $customerGroupManagement;

    protected $quoteFactory;

    protected $quoteModel;

    /**
     * @var \SM\Sales\Repositories\OrderHistoryManagement
     */
    private $orderHistoryManagement;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $groupFactory;

    protected $realtimeManager;

    public function __construct(
        \Magento\Framework\App\RequestInterface $requestInterface,
        \SM\XRetail\Helper\DataConfig $dataConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Model\Config\Share $customerConfigShare,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \SM\Customer\Helper\Data $customerHelper,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Sales\Model\ResourceModel\Sale\CollectionFactory $salesCollectionFactory,
        ProductFactory $productFactory,
        \SM\Integrate\Helper\Data $integrateHelperData,
        \SM\Wishlist\Repositories\WishlistManagement $wishlistManagement,
        \SM\Customer\Model\ResourceModel\Grid\CollectionFactory $customerGridCollection,
        \Magento\Customer\Api\GroupManagementInterface $customerGroupManagement,
        SubscriberFactory $subscriberFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\Registry $registry,
        Config $eavConfig,
        \SM\Sales\Repositories\OrderHistoryManagement $orderHistoryManagement,
        EavSetupFactory $eavSetupFactory,
        Attribute $attributeFactory,
        GroupFactory $groupFactory,
        RealtimeManager $realtimeManager
    ) {
        $this->customerConfigShare            = $customerConfigShare;
        $this->customerCollectionFactory      = $customerCollectionFactory;
        $this->countryCollection              = $countryCollectionFactory;
        $this->resource                       = $resource;
        $this->customerGroupCollectionFactory = $groupCollectionFactory;
        $this->customerFactory                = $customerFactory;
        $this->customerRepository             = $customerRepository;
        $this->customerHelper                 = $customerHelper;
        $this->addressRepository              = $addressRepository;
        $this->addressFactory                 = $addressFactory;
        $this->productFactory                 = $productFactory;
        $this->salesCollectionFactory         = $salesCollectionFactory;
        $this->integrateHelper                = $integrateHelperData;
        $this->wishlistManagement             = $wishlistManagement;
        $this->customerGridCollectionFactory  = $customerGridCollection;
        $this->subscriberFactory              = $subscriberFactory;
        $this->customerGroupManagement        = $customerGroupManagement;
        $this->quoteFactory                   = $quoteFactory;
        $this->orderHistoryManagement         = $orderHistoryManagement;
        $this->registry                       = $registry;
        $this->eavConfig                      = $eavConfig;
        $this->eavSetupFactory                = $eavSetupFactory;
        $this->attributeFactory               = $attributeFactory;
        $this->groupFactory                   = $groupFactory;
        $this->realtimeManager                = $realtimeManager;
        parent::__construct($requestInterface, $dataConfig, $storeManager);
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getCustomerData()
    {
        return $this->loadCustomers($this->getSearchCriteria())->getOutput();
    }

    /**
     * @param null $searchCriteria
     *
     * @return \SM\Core\Api\SearchResult
     * @throws \Exception
     */
    public function loadCustomers($searchCriteria = null)
    {
        if (is_null($searchCriteria) || !$searchCriteria) {
            $searchCriteria = $this->getSearchCriteria();
        }

        $this->getSearchResult()->setSearchCriteria($searchCriteria);
        $collection = $this->getCustomerCollection($searchCriteria);

        $customers = [];
        if ($collection->getLastPageNumber() < $searchCriteria->getData('currentPage')) {
        } else {
            foreach ($collection as $customerModel) {
                $customerModel->load($customerModel->getId());
                /** @var $customerModel \Magento\Customer\Model\Customer */
                $customer = new XCustomer();
                $customer->addData($customerModel->getData());
                $customer->setData('tax_class_id', $customerModel->getTaxClassId());

                $customer->setData('address', $this->getCustomerAddress($customerModel));

                $checkSubscriber = $this->subscriberFactory->create()->loadByCustomerId($customerModel->getId());
                if ($checkSubscriber->isSubscribed()) {
                    $customer->setData('subscription', true);
                } else {
                    $customer->setData('subscription', false);
                }

                if ($this->integrateHelper->isIntegrateRP() && $this->integrateHelper->isAHWRewardPoints()) {
                    $customer->setData('reward_point', $this->integrateHelper->getRpIntegrateManagement()
                                                                              ->getCurrentIntegrateModel()
                                                                              ->getCurrentPointBalance(
                                                                                  $customerModel->getEntityId(),
                                                                                  $this->storeManager->getStore($searchCriteria['storeId'])->getWebsiteId()
                                                                              ));
                }
                $customer->setData('scg_customer_group_name', $this->getScgCustomerGroup($customer->getData('scg_customer_group')));

                $customers[] = $customer;
            }
        }
        return $this->getSearchResult()
                    ->setItems($customers)
                    ->setLastPageNumber($collection->getLastPageNumber())
                    ->setTotalCount($collection->getSize());
    }

    /**
     * @param null $groupId
     * @return array|false
     * @throws LocalizedException
     */
    protected function getScgCustomerGroup($groupId = null)
    {
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'scg_customer_group');
        if (!$attribute) {
            return false;
        }
        if ($groupId) {
            return $attribute->getSource()->getOptionText($groupId);
        }

        return $attribute->getSource()->toOptionArray();
    }

    /**
     *
     * @param \Magento\Framework\DataObject $searchCriteria
     *
     * @return \SM\Customer\Model\ResourceModel\Grid\Collection
     * @throws \Exception
     */
    protected function getCustomerCollection($searchCriteria)
    {
        $storeId = $searchCriteria->getData('storeId');
        if (is_null($storeId)) {
            throw new Exception(__('Must have param storeId'));
        } else {
            $this->getStoreManager()->setCurrentStore($storeId);
        }
        /** @var \SM\Customer\Model\ResourceModel\Grid\Collection $collection */
        $collection = $this->customerGridCollectionFactory->create();
        if (is_nan($searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }
        if ($searchCriteria->getData('ids')) {
            $collection->addFieldToFilter('entity_id', ['in' => $searchCriteria->getData('ids')]);
        }
        if ($searchCriteria->getData('entity_id') || $searchCriteria->getData('entityId')) {
            if (is_null($searchCriteria->getData('entity_id'))) {
                $ids = $searchCriteria->getData('entityId');
            } else {
                $ids = $searchCriteria->getData('entity_id');
            }
            $collection->addFieldToFilter('entity_id', ['in' => explode(",", $ids)]);
        }
        if ($searchCriteria->getData('searchOnline') == 1) {
            $searchValue = $searchCriteria->getData('searchValue');
            $searchField = $searchCriteria->getData('searchFields');

            $_fieldFilters = [];
            $_valueFilters = [];
            foreach (explode(",", $searchField) as $field) {
                if ($field === 'first_name' || $field === 'last_name') {
                    $_fieldFilters[] = "name";
                    $_valueFilters[] = ['like' => '%' . $searchValue . '%'];
                } elseif ($field === 'telephone') {
                    $_fieldFilters[] = 'billing_telephone';
                    $_fieldFilters[] = 'shipping_full';
                    $_valueFilters[] = ['like' => '%' . $searchValue . '%'];
                    $_valueFilters[] = ['like' => '%' . $searchValue . '%'];
                } elseif ($field === 'id') {
                    $_fieldFilters  [] = 'entity_id';
                    $_valueFilters[]   = ['like' => '%' . $searchValue . '%'];
                } elseif ($field === 'postcode') {
                    $_fieldFilters[] = 'billing_postcode';
                    $_fieldFilters[] = 'shipping_full';
                    $_valueFilters[] = ['like' => '%' . $searchValue . '%'];
                    $_valueFilters[] = ['like' => '%' . $searchValue . '%'];
                } elseif ($field === 'email') {
                    $_fieldFilters  [] = 'email';
                    $_valueFilters[]   = ['like' => '%' . $searchValue . '%'];
                }
            }
            $_fieldFilters = array_unique($_fieldFilters);
            $collection->addFieldToFilter($_fieldFilters, $_valueFilters);
        }
        if (is_nan($searchCriteria->getData('pageSize'))) {
            $collection->setPageSize(
                DataConfig::PAGE_SIZE_LOAD_CUSTOMER
            );
        } else {
            $collection->setPageSize(
                $searchCriteria->getData('pageSize')
            );
        }
        if ($this->customerConfigShare->isWebsiteScope()) {
            $collection->addFieldToFilter('website_id', $this->getStoreManager()->getStore($storeId)->getWebsiteId());
        }

        return $collection;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     *
     * @return array
     */
    protected function getCustomerAddress(\Magento\Customer\Model\Customer $customer)
    {
        $customerAdd = [];

        foreach ($customer->getAddresses() as $address) {
            /** @var \Magento\Customer\Model\Address $address */
            $customerAdd[] = $this->getAddressData($address);
        }

        return $customerAdd;
    }

    /**
     * Get customer address base on api
     *
     * @param \Magento\Customer\Model\Address $address
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function getAddressData(\Magento\Customer\Model\Address $address)
    {
        $addData           = $address->getData();
        $addData['first_name'] = $addData['firstname'];
        $addData['last_name'] = $addData['lastname'];
        $addData['street'] = $address->getStreet();
        $addData['company'] = is_null($address->getCompany()) ? '' : $address->getCompany();
        $_customerAdd      = new CustomerAddress($addData);

        return $_customerAdd->getOutput();
    }

    /**
     * @return array
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getCountryRegionData()
    {
        $items      = [];
        $collection = $this->getCountryCollection($this->getSearchCriteria());
        if ($collection->getLastPageNumber() < $this->getSearchCriteria()->getData('currentPage')) {
        } else {
            foreach ($collection as $country) {
                /** @var \Magento\Directory\Model\Country $country */
                $regionCollection = $country->getRegionCollection();
                $regions          = [];
                foreach ($regionCollection as $region) {
                    $regions[] = $region->getData();
                }
                $countryRegion = new CountryRegion();
                $countryRegion->addData(
                    [
                        'country_id' => $country->getCountryId(),
                        'name'       => $country->getName(),
                        'regions'    => $regions
                    ]
                );
                $items[] = $countryRegion;
            }
        }

        return $this->getSearchResult()
                    ->setItems($items)
                    ->setTotalCount($collection->getSize())
                    ->getOutput();
    }

    /**
     * @param $searchCriteria
     *
     * @return \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected function getCountryCollection($searchCriteria)
    {
        /** @var   \Magento\Directory\Model\ResourceModel\Country\Collection $collection */
        $collection = $this->countryCollection->create();
        if (is_nan($searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }
        if (is_nan($searchCriteria->getData('pageSize'))) {
            $collection->setPageSize(
                DataConfig::PAGE_SIZE_LOAD_CUSTOMER
            );
        } else {
            $collection->setPageSize(
                $searchCriteria->getData('pageSize')
            );
        }

        return $collection;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getCustomerGroupData()
    {
        $items      = [];
        $collection = $this->getCustomerGroupCollection($this->getSearchCriteria());
        if ($collection->getLastPageNumber() < $this->getSearchCriteria()->getData('currentPage')) {
        } else {
            foreach ($collection as $group) {
                $g = new CustomerGroup();
                /** @var \Magento\Customer\Model\Group $group */
                $g->addData(
                    [
                        'customer_group_id'   => $group->getId(),
                        'customer_group_code' => $group->getCode(),
                        'tax_class_id'        => $group->getData('tax_class_id'),
                        'tax_class_name'      => $group->getTaxClassName()
                    ]
                );
                $items[] = $g;
            }
        }

        return $this->getSearchResult()
                    ->setSearchCriteria($this->getSearchCriteria())
                    ->setItems($items)
                    ->setTotalCount($collection->getSize())
                    ->getOutput();
    }

    /**
     * @param $searchCriteria
     *
     * @return \Magento\Customer\Model\ResourceModel\Group\Collection
     */
    protected function getCustomerGroupCollection($searchCriteria)
    {
        /** @var   \Magento\Customer\Model\ResourceModel\Group\Collection $collection */
        $collection = $this->customerGroupCollectionFactory->create();
        if (is_nan($searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }
        if (is_nan($searchCriteria->getData('pageSize'))) {
            $collection->setPageSize(
                DataConfig::PAGE_SIZE_LOAD_CUSTOMER
            );
        } else {
            $collection->setPageSize(
                $searchCriteria->getData('pageSize')
            );
        }
        if ($searchCriteria->getData('entity_id')) {
            $arr = explode(",", $searchCriteria->getData('entity_id'));
            $collection->addFieldToFilter('customer_group_id', ['in' => $arr]);
        }

        return $collection;
    }

    /**
     * @param $data
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function create($data)
    {
        $this->getRequest()->setParams($data);
        $customer = $this->save();
        if (isset($customer['items'][0]['id'])) {
            return $customer['items'][0]['id'];
        } else {
            throw new Exception("Can't create customer");
        }
    }

    /**
     * Save customer and address
     *
     * @return array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save()
    {
        $data = $this->getRequestData();

        $customerData = new DataObject($data->getData('customer'));
        $addressData  = $data->getData('address') ? new DataObject($data->getData('address')) : null;
        $addressType  = $data->getData('addressType');
        $storeId      = $data->getData('storeId');
        
        $this->validateTelephoneNumber($data);

        if (!!$customerData['customer_group_id'] &&
            $customerData['customer_group_id'] === 'other' &&
            !!$customerData['customer_group_other_name']) {
            $newCustomerGroupID = $this->createNewCustomerGroup($customerData['customer_group_other_name']);
            if (!!$newCustomerGroupID) {
                $customerData['customer_group_id'] = $newCustomerGroupID;
            }
        }

        if (!!$customerData['occupation'] &&
            $customerData['occupation'] !== '7') {
            $customerData['customer_occupation_other_name'] = '';
        }

        if (!!$customerData['birthday'] && isset($customerData['birthday']['data_date'])) {
            list($month, $date, $year) = explode('/', $customerData['birthday']['data_date']);

            $birthday = date('Y-m-d', strtotime("$year-$month-$date"));

            $customerData['dob'] = $birthday;
        }

        if (is_null($storeId)) {
            throw new Exception("Please define customer store id");
        }
        $this->customerHelper->transformCustomerData($customerData);

        // Check email already exists in website
        if (!$customerData->getId()) {
            try {
                $checkCustomer = $this->customerRepository->get($customerData->getEmail());
                $websiteId     = $checkCustomer->getWebsiteId();

                if ($this->customerHelper->isCustomerInStore($websiteId, $storeId)) {
                    throw new Exception(__('A customer with the same email already exists in an associated website.'));
                }
            } catch (Exception $e) {
                // CustomerRepository will throw exception if can't not find customer with email
            }
        }

        // Associate website_id with customer
        if (!$customerData->getWebsiteId()) {
            $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
            $customerData->setWebsiteId($websiteId);
        }

        // Update 'created_in' value with actual store name
        if ($customerData->getId() === null) {
            $storeName = $this->storeManager->getStore($storeId)->getName();
            $customerData->setCreatedIn($storeName);
        }

        try {
            $customer = $this->getCustomerModel();
            $customerValue = $customer->getDataModel();
            $customerValue->setCustomAttribute('retail_veriface', $customerData->getVeriface());
            $customer->updateData($customerValue);
            $customerData->setAddress(null);
            if ($customerData->getId() && $customerData->getId() < 1481282470403) {
                $customer = $customer->load($customerData->getId());

                if ($customer->getId()) {
                    if ($addressType === 'billing') {
                        $customer->addData($customerData->getData())
                                 ->save();
                    }
                } else {
                    throw new Exception("Can't find customer with id: " . $customerData->getId());
                }
            } elseif ($addressType === 'shipping') {
                throw new Exception("Please define customer when save shipping address");
            } else {
                $customer = $customer->addData($customerData->getData())
                                     ->save();
                try {
                    $customer->sendNewAccountEmail('confirmed', '', $storeId);
                } catch (Exception $e) {
                }
            }

            if ($addressData && $customer->getId()) {
                $this->customerHelper->transformCustomerData($addressData);
                $addressModel = $this->getAddressModel();
                if ($addressData->getId() && $addressData->getId() < 1481282470403) {
                    $addressModel->load($addressData->getId());
                    if (!$addressModel->getId()) {
                        throw new Exception(__("Can't get address id: " . $addressData->getId()));
                    }
                } else {
                    $addressData->setId(null);
                }
                $addressModel->addData($addressData->getData())
                             ->setData('parent_id', $customer->getId())
                             ->save();

                $customer = $this->getCustomerModel()->load($customer->getId());
                if ($addressType === 'billing') {
                    $customer->setDefaultBilling($addressModel->getId())->save();
                } else {
                    $customer->setDefaultShipping($addressModel->getId())->save();
                }
            }

            if (isset($customerData['subscription']) && $customer->getId()) {
                if ($customerData['subscription'] == 1) {
                    $this->subscriberFactory->create()->subscribeCustomerById($customer->getId());
                } else {
                    $this->subscriberFactory->create()->unsubscribeCustomerById($customer->getId());
                }
            }
        } catch (AlreadyExistsException $e) {
            throw new Exception(
                __('A customer with the same email already exists in an associated website.')
            );
        } catch (LocalizedException $e) {
            throw $e;
        }

        $searchCriteria = new \Magento\Framework\DataObject(
            [
                'storeId'   => $storeId,
                'entity_id' => $customer->getId()
            ]
        );

        return $this->loadCustomers($searchCriteria)->getOutput();
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function createAddress()
    {
        $address = $this->getRequestData();
        $this->customerHelper->transformCustomerData($address);

        $addressModel = $this->getAddressModel();
        if ($address->getId()) {
            $addressModel->load($address->getId());
            if (!$addressModel->getId()) {
                throw new Exception(__("Can't get address id: " . $address->getId()));
            }
        }
        $addressModel->addData($address->getData());
        $addressModel->setData('parent_id', $address->getData('customer_id'));

        return $this->getAddressData($addressModel->save());
    }

    /**
     * @return \Magento\Customer\Model\Address
     */
    protected function getAddressModel()
    {
        return $this->addressFactory->create();
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomerModel()
    {
        return $this->customerFactory->create();
    }

    /**
     * @param null $searchCriteria
     *
     * @return array
     * @throws \Exception
     */
    public function loadCustomerDetail($searchCriteria = null)
    {
        if (is_null($searchCriteria) || !$searchCriteria) {
            $searchCriteria = $this->getSearchCriteria();
        }
        $this->getSearchResult()->setSearchCriteria($searchCriteria);
        $customerId         = $searchCriteria->getData('customerId');
        $storeId            = $searchCriteria->getData('storeId');
        $usingProductOnline = $searchCriteria->getData('usingProductOnline');
        if (is_null($customerId) || is_null($storeId)) {
            throw new Exception(__("Something wrong! Missing require value"));
        }
        $this->registry->unregister('is_connectpos');
        $this->registry->register('is_connectpos', true);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $quote = $objectManager->create('Magento\Quote\Model\Quote')->loadByCustomer($customerId);
        if ($quote->getData('is_active') === '1') {
            //$quoteItems=$quote->getAllVisibleItems();
            $items = $this->orderHistoryManagement->getOrderItemData($quote->getAllItems(), $storeId);
        } else {
            $items = [];
        }

        $data = [
            'life_time_sales' => $this->salesCollectionFactory->create()
                                                              ->setOrderStateFilter(Order::STATE_COMPLETE, false)
                                                              ->setCustomerIdFilter($customerId)
                                                              ->load()
                                                              ->getTotals()
                                                              ->getLifetime(),
            'wishlist'        => $this->wishlistManagement->getWishlistData($customerId, $storeId, $usingProductOnline),
            'items'           => $items
        ];

        if ($this->integrateHelper->isIntegrateRP()
            && $this->integrateHelper->isAHWRewardPoints()) {
            $data['rp_point_balance'] = $this->integrateHelper
                ->getRpIntegrateManagement()
                ->getCurrentIntegrateModel()
                ->getCurrentPointBalance(
                    $customerId,
                    $this->storeManager->getStore($storeId)->getWebsiteId()
                );
        }

        if ($this->integrateHelper->isIntegrateRP()
            && $this->integrateHelper->isRewardPointMagento2EE()) {
            $data['rp_point_balance'] = $this->integrateHelper
                ->getRpIntegrateManagement()
                ->getCurrentIntegrateModel()
                ->getCurrentPointBalance(
                    $this->getCustomerModel()->load($customerId),
                    $this->storeManager->getStore($storeId)->getWebsiteId()
                );
        }

        if ($this->integrateHelper->isIntegrateStoreCredit()
            && $this->integrateHelper->isExistStoreCreditMagento2EE()) {
            $data['store_credit_balance'] = $this->integrateHelper
                ->getStoreCreditIntegrateManagement()
                ->getCurrentIntegrateModel()
                ->getStoreCreditCollection(
                    $this->getCustomerModel()->load($customerId),
                    $this->storeManager->getStore($storeId)->getWebsiteId()
                );
        }

        return $data;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductModel()
    {
        return $this->productFactory->create();
    }

    /**
     * @throws LocalizedException
     * @throws Exception
     */
    public function saveSub()
    {
        $data = $this->getRequestData();

        $email = $data->getData('email');
        $isSubscribe  = $data->getData('isSubscribe');
        $checkSubscriber = $this->subscriberFactory->create()->loadByEmail($email);
        if ($isSubscribe) {
            $checkSubscriber->subscribe($email);
        } else {
            if ($checkSubscriber->getSubscriberId()) {
                $checkSubscriber->unsubscribe($email);
            }
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getCustomerOccupationData()
    {
        $items      = [];
        $collection = $this->getCustomerOccupationCollection();

        if ($this->getSearchCriteria()->getData('currentPage') == 1) {
            foreach ($collection as $occupation) {
                $oc = new CustomerOccupation();

                $oc->addData(
                    [
                        'customer_occupation_id'    => $occupation['value'],
                        'customer_occupation_label' => $occupation['label']
                    ]
                );
                $items[] = $oc;
            }
        }

        return $this->getSearchResult()
                    ->setSearchCriteria($this->getSearchCriteria())
                    ->setItems($items)
                    ->getOutput();
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getScgCustomerGroupData()
    {
        $items      = [];
        $groups = $this->getScgCustomerGroup();

        if ((int) $this->getSearchCriteria()->getData('currentPage') === 1) {
            foreach ($groups as $group) {
                $scgGroup = new ScgCustomerGroup();
                $scgGroup->addData([
                    'scg_customer_group_id'    => $group['value'],
                    'scg_customer_group_label' => $group['label']
                ]);
                $items[] = $scgGroup;
            }
        }

        return $this->getSearchResult()
            ->setSearchCriteria($this->getSearchCriteria())
            ->setItems($items)
            ->getOutput();
    }

    /**
     * @return array
     */
    protected function getCustomerOccupationCollection()
    {
        $options = [];

        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, 'occupation');
        if (!!$attribute) {
            $options = $attribute->getSource()->getAllOptions();
        }

        return $options;
    }

    /**
     * @param $attributeCode
     *
     * @return Magento\Catalog\Model\ResourceModel\Eav\Attribute | null
     */
    protected function loadAttributeByAttributeCode($attributeCode)
    {
        $attributeInfo = $this->attributeFactory->getCollection()
                                                 ->addFieldToFilter('attribute_code', ['eq' => $attributeCode])
                                                 ->getFirstItem();
        if (!$attributeInfo) {
            return null;
        }
        return $attributeInfo;
    }

    /**
     * @param $attributeInfo
     * @param $optionLabel
     *
     * @return array
     */
    protected function createAttributeOptions($attributeInfo, $optionLabel)
    {
        $option                 = [];
        $option['attribute_id'] = $attributeInfo->getAttributeId();
        $valueId = strtotime(date('Y-m-d H:i:s'));
        $option['values'][$valueId][0] = $optionLabel;

        return [$option, $valueId];
    }

    /**
     * @param $option
     */
    protected function addOptionToAttribute($option)
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttributeOption($option);
    }

    /**
     * @param $customerGroup
     *
     * @return mixed
     * @throws \Exception
     */
    protected function createNewCustomerGroup($customerGroup)
    {
        $group = $this->groupFactory->create();
        $group
            ->setCode($customerGroup)
            ->setTaxClassId(3)
            ->save();

        $this->realtimeManager->trigger(
            RealtimeManager::CUSTOMER_GROUP,
            $group->getData('customer_group_id'),
            RealtimeManager::TYPE_CHANGE_NEW
        );

        return $group->getData('customer_group_id');
    }

    /**
     * @param DataObject $data
     * @throws AlreadyExistsException
     */
    protected function validateTelephoneNumber(DataObject $data)
    {
        $telephone = $data->getData('customer')['telephone'];
        $collection = $this->customerCollectionFactory->create()->addFieldToFilter('retail_telephone', ['eq' => $telephone]);

        if (isset($data->getData('customer')['id']) && $data->getData('customer')['id']) {
            $collection->addFieldToFilter('entity_id', ['neq' => $data->getData('customer')['id']]);
        }

        if ($collection->getSize() > 0) {
            throw new AlreadyExistsException(__('A customer with the same telephone already exists.'));
        }
    }
}
