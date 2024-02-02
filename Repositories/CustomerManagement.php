<?php

namespace SM\Customer\Repositories;

use Exception;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Sales\Model\Order;
use SM\Core\Api\Data\CountryRegion;
use SM\Core\Api\Data\CustomerAddress;
use SM\Core\Api\Data\CustomerGroup;
use SM\Core\Api\Data\XCustomer;
use SM\Core\Model\DataObject;
use SM\XRetail\Helper\DataConfig;
use SM\XRetail\Repositories\Contract\ServiceAbstract;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResource;

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
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;
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
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;
    /**
     * @var \SM\Sales\Repositories\OrderHistoryManagement
     */
    private $orderHistoryManagement;

    /**
     * @var State
     */
    private $state;

    /**
     * @var CustomerResource
     */
    protected $customerResource;

    /**
     * CustomerManagement constructor.
     *
     * @param \Magento\Framework\App\RequestInterface                          $requestInterface
     * @param \SM\XRetail\Helper\DataConfig                                    $dataConfig
     * @param \Magento\Store\Model\StoreManagerInterface                       $storeManager
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magento\Customer\Model\Config\Share                             $customerConfigShare
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection                        $resource
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory    $groupCollectionFactory
     * @param \Magento\Customer\Model\CustomerFactory                          $customerFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface                $customerRepository
     * @param \SM\Customer\Helper\Data                                         $customerHelper
     * @param \Magento\Customer\Api\AddressRepositoryInterface                 $addressRepository
     * @param \Magento\Customer\Model\AddressFactory                           $addressFactory
     * @param \Magento\Sales\Model\ResourceModel\Sale\CollectionFactory        $salesCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory                            $productFactory
     * @param \SM\Integrate\Helper\Data                                        $integrateHelperData
     * @param \SM\Wishlist\Repositories\WishlistManagement                     $wishlistManagement
     * @param \SM\Customer\Model\ResourceModel\Grid\CollectionFactory          $customerGridCollection
     * @param \Magento\Customer\Api\GroupManagementInterface                   $customerGroupManagement
     * @param SubscriberFactory                                                $subscriberFactory
     * @param \Magento\Quote\Model\QuoteFactory                                $quoteFactory
     * @param \Magento\Framework\Registry                                      $registry
     * @param \SM\Sales\Repositories\OrderHistoryManagement                    $orderHistoryManagement
     * @param \Magento\Customer\Api\AccountManagementInterface                 $accountManagement
     * @param State                                                            $state
     * @param CustomerResource                                                 $customerResource
     */
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
        \SM\Sales\Repositories\OrderHistoryManagement $orderHistoryManagement,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        State $state,
        CustomerResource $customerResource
    ) {
        $this->customerConfigShare = $customerConfigShare;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->countryCollection = $countryCollectionFactory;
        $this->resource = $resource;
        $this->customerGroupCollectionFactory = $groupCollectionFactory;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->customerHelper = $customerHelper;
        $this->addressRepository = $addressRepository;
        $this->addressFactory = $addressFactory;
        $this->productFactory = $productFactory;
        $this->salesCollectionFactory = $salesCollectionFactory;
        $this->integrateHelper = $integrateHelperData;
        $this->wishlistManagement = $wishlistManagement;
        $this->customerGridCollectionFactory = $customerGridCollection;
        $this->subscriberFactory = $subscriberFactory;
        $this->customerGroupManagement = $customerGroupManagement;
        $this->quoteFactory = $quoteFactory;
        $this->orderHistoryManagement = $orderHistoryManagement;
        $this->registry = $registry;
        $this->accountManagement = $accountManagement;
        $this->state = $state;
        $this->customerResource = $customerResource;
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
     * For updating module
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

                $thirdPartyRP = $this->integrateHelper->isAHWRewardPoints() || $this->integrateHelper->isAmastyRewardPoints() || $this->integrateHelper->isMirasvitRewardPoints();
                if ($this->integrateHelper->isIntegrateRP() && $thirdPartyRP) {
                    $rewardPoints = $this->integrateHelper->getRpIntegrateManagement()
                        ->getCurrentIntegrateModel()
                        ->getCurrentPointBalance(
                            $customerModel->getEntityId(),
                            $this->storeManager->getStore($searchCriteria['storeId'])->getWebsiteId()
                        );
                    $customer->setData('reward_point', $rewardPoints);
                }

                $customers[] = $customer;
            }
        }

        return $this->getSearchResult()
            ->setItems($customers)
            ->setLastPageNumber($collection->getLastPageNumber())
            ->setTotalCount($collection->getSize());
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
        if (is_nan((float)$searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }
        if ($searchCriteria->getData('ids')) {
            $collection->addFieldToFilter('entity_id', ['in' => $searchCriteria->getData('ids')]);
        }
        if ($searchCriteria->getData('entity_id') || $searchCriteria->getData('entityId')) {
            if ($searchCriteria->getData('entity_id') === null) {
                $ids = $searchCriteria->getData('entityId');
            } else {
                $ids = $searchCriteria->getData('entity_id');
            }
            $collection->addFieldToFilter('entity_id', ['in' => explode(",", (string)$ids)]);
        }
        if ($searchCriteria->getData('searchOnline') == 1) {
            $searchValue = $searchCriteria->getData('searchValue');
            $searchField = $searchCriteria->getData('searchFields');

            $_fieldFilters = [];
            $_valueFilters = [];
            foreach (explode(",", (string)$searchField) as $field) {
                if ($field === 'first_name' || $field === 'last_name') {
                    $_fieldFilters[] = "name";
                    $_valueFilters[] = ['like' => '%'.$searchValue.'%'];
                } elseif ($field === 'telephone') {
                    $_fieldFilters[] = 'billing_telephone';
                    $_fieldFilters[] = 'shipping_full';
                    $_valueFilters[] = ['like' => '%'.$searchValue.'%'];
                    $_valueFilters[] = ['like' => '%'.$searchValue.'%'];
                } elseif ($field === 'id') {
                    $_fieldFilters  [] = 'entity_id';
                    $_valueFilters[] = ['like' => '%'.$searchValue.'%'];
                } elseif ($field === 'postcode') {
                    $_fieldFilters[] = 'billing_postcode';
                    $_fieldFilters[] = 'shipping_full';
                    $_valueFilters[] = ['like' => '%'.$searchValue.'%'];
                    $_valueFilters[] = ['like' => '%'.$searchValue.'%'];
                } elseif ($field === 'email') {
                    $_fieldFilters  [] = 'email';
                    $_valueFilters[] = ['like' => '%'.$searchValue.'%'];
                } else {
                    $_fieldFilters  [] = $field;
                    $_valueFilters[] = ['like' => '%'.$searchValue.'%'];
                }
            }
            $_fieldFilters = array_unique($_fieldFilters);
            $collection->addFieldToFilter($_fieldFilters, $_valueFilters);
        }
        if (is_nan((float)$searchCriteria->getData('pageSize'))) {
            $collection->setPageSize(
                DataConfig::PAGE_SIZE_LOAD_CUSTOMER
            );
        } else {
            $collection->setPageSize(
                $searchCriteria->getData('pageSize')
            );
        }
        if ($this->customerConfigShare->isWebsiteScope()) {
            $collection->addFieldToFilter('website_id', ['in' => [$this->getStoreManager()->getStore($storeId)->getWebsiteId(), 0]]);
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
        $addData = $address->getData();
        $addData['first_name'] = $addData['firstname'];
        $addData['last_name'] = $addData['lastname'];
        $addData['street'] = $address->getStreet();
        $addData['company'] = is_null($address->getCompany()) ? '' : $address->getCompany();
        $_customerAdd = new CustomerAddress($addData);

        return $_customerAdd->getOutput();
    }

    /**
     * @return array
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getCountryRegionData()
    {
        $items = [];
        $collection = $this->getCountryCollection($this->getSearchCriteria());
        if ($collection->getLastPageNumber() < $this->getSearchCriteria()->getData('currentPage')) {
        } else {
            foreach ($collection as $country) {
                /** @var \Magento\Directory\Model\Country $country */
                $regionCollection = $country->getRegionCollection();
                $regions = [];
                foreach ($regionCollection as $region) {
                    $regions[] = $region->getData();
                }
                $countryRegion = new CountryRegion();
                $countryRegion->addData(
                    [
                        'country_id' => $country->getCountryId(),
                        'name'       => $country->getName(),
                        'regions'    => $regions,
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
        if (is_nan((float)$searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }

        $collection->setPageSize(300);

        return $collection;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getCustomerGroupData()
    {
        $items = [];
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
                        'tax_class_name'      => $group->getTaxClassName(),
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
        if (is_nan((float)$searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }
        if (is_nan((float)$searchCriteria->getData('pageSize'))) {
            $collection->setPageSize(
                DataConfig::PAGE_SIZE_LOAD_CUSTOMER
            );
        } else {
            $collection->setPageSize(
                $searchCriteria->getData('pageSize')
            );
        }
        if ($searchCriteria->getData('entity_id')) {
            $arr = explode(",", (string)$searchCriteria->getData('entity_id'));
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
        $addressData = $data->getData('address') ? new DataObject($data->getData('address')) : null;
        $addressType = $data->getData('addressType');
        $storeId = $data->getData('storeId');

        if (is_null($storeId)) {
            throw new Exception("Please define customer store id");
        }

        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
        $this->customerHelper->transformCustomerData($customerData);

        // Check email already exists in website
        if (!$customerData->getId()) {
            try {
                $checkCustomer = $this->customerRepository->get($customerData->getEmail());
                $websiteId = $checkCustomer->getWebsiteId();

                if ($this->customerHelper->isCustomerInStore($websiteId, $storeId)) {
                    throw new Exception(__('A customer with the same email already exists in an associated website.'));
                }
            } catch (Exception $e) {
                // CustomerRepository will throw exception if it cannot find customer with email
            }
        }

        // Associate website_id with customer
        if (!$customerData->getWebsiteId()) {
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
                    throw new Exception("Can't find customer with id: ".$customerData->getId());
                }
            } elseif ($addressType === 'shipping') {
                throw new Exception("Please define customer when saving shipping address");
            } else {
                $customer = $customer->addData($customerData->getData());
                $customer = $this->accountManagement->createAccount($customer->getDataModel());
            }

            $oldAddressData = [];
            if (empty($addressData) && $customer->getId()) {
                $cust = $this->customerRepository->getById($customer->getId());

                if ($cust->getAddresses()) {
                    $oldAddressData = $cust->getAddresses();
                }
            }

            if ($addressData && $customer->getId()) {
                $this->customerHelper->transformCustomerData($addressData);
                $addressModel = $this->getAddressModel();
                if ($addressData->getId() && $addressData->getId() < 1481282470403) {
                    $addressModel->load($addressData->getId());
                    if (!$addressModel->getId()) {
                        throw new Exception(__("Can't get address id: ".$addressData->getId()));
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
                    $this->subscriberFactory->create()->subscribe($customer->getEmail());
                } elseif (!$this->dataConfig->isBlockingCustomerFromUnsubscribe()) {
                    $this->subscriberFactory->create()->unsubscribeCustomerById($customer->getId());
                } else {
                    $this->subscriberFactory->create()->unsubscribeCustomerById($customer->getId());
                }
            }

            if ($this->integrateHelper->isIntegrateStoreCredit()
                && ($this->integrateHelper->isExistStoreCreditMagento2EE() || $this->integrateHelper->isExistStoreCreditAheadworks())
                && isset($customerData['store_credit_adjust'])
                && $customer->getId()
            ) {
                $this->integrateHelper
                    ->getStoreCreditIntegrateManagement()
                    ->getCurrentIntegrateModel()
                    ->updateCustomerStoreCreditBalance($customer->getDataModel(), $websiteId, $storeId, $customerData['store_credit_adjust']);
            }

            if ($this->integrateHelper->isIntegrateRP()
                && (($this->integrateHelper->isAHWRewardPoints() || $this->integrateHelper->isAmastyRewardPoints()) || $this->integrateHelper->isRewardPointMagento2EE()
                    || $this->integrateHelper->isMirasvitRewardPoints())
                && isset($customerData['rp_point_adjust'])
                && $customer->getId()
            ) {
                $this->integrateHelper
                    ->getRpIntegrateManagement()
                    ->getCurrentIntegrateModel()
                    ->updateCustomerCurrentPointBalance(
                        $customer->getDataModel(),
                        $websiteId,
                        $storeId,
                        $customerData['rp_point_adjust']
                    );
            }
            // Magento restricts updating customer group for some unknown reason, thus we need to update the customer after creating new
            /** @see \Magento\Customer\Model\AccountManagement::createAccount */
            $customer->setGroupId($customerData->getData('group_id'));
            if ($customer instanceof \Magento\Customer\Model\Data\Customer) {
                $customer = $this->getCustomerModel()->load($customer->getId());
                if (!empty($customerData->getData('retail_note'))) {
                    $customer->setData('retail_note', $customerData->getData('retail_note'));
                    $this->customerResource->saveAttribute($customer, 'retail_note');
                }
                if (!empty($customerData->getData('retail_telephone_2'))) {
                    $customer->setData('retail_telephone_2', $customerData->getData('retail_telephone_2'));
                    $this->customerResource->saveAttribute($customer, 'retail_telephone_2');
                }
                if (!empty($customerData->getData('retail_guest_id'))) {
                    $customer->setData('retail_guest_id', $customerData->getData('retail_guest_id'));
                    $this->customerResource->saveAttribute($customer, 'retail_guest_id');
                }
                $saveModel = $customer->getDataModel();
                if (!empty($oldAddressData)) {
                    $saveModel->setAddresses($oldAddressData);
                }
                $this->customerRepository->save($saveModel);
            } else {
                if (!empty($customerData->getData('retail_note'))) {
                    $this->customerResource->saveAttribute($customer, 'retail_note');
                }
                if (!empty($customerData->getData('retail_telephone_2'))) {
                    $this->customerResource->saveAttribute($customer, 'retail_telephone_2');
                }
                if (!empty($customerData->getData('retail_guest_id'))) {
                    $this->customerResource->saveAttribute($customer, 'retail_guest_id');
                }
                $saveModel = $customer->getDataModel();
                if (!empty($oldAddressData)) {
                    $saveModel->setAddresses($oldAddressData);
                }
                $this->customerRepository->save($saveModel);
            }
        } catch (AlreadyExistsException $e) {
            throw new Exception(
                __('A customer with the same email already exists in an associated website.')
            );
        } catch (\Throwable $e) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $logger = $objectManager->get('Psr\Log\LoggerInterface');
            $logger->info("====> [CPOS] Failed to save customer: {$e->getMessage()}");
            $logger->info($e->getTraceAsString());
            throw $e;
        }

        $searchCriteria = new \Magento\Framework\DataObject(
            [
                'storeId'   => $storeId,
                'entity_id' => $customer->getId(),
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
                throw new Exception(__("Can't get address id: ".$address->getId()));
            }
        }
        $addressModel->addData($address->getData());
        $addressModel->setData('parent_id', $address->getData('customer_id'));

        return $this->getAddressData($addressModel->save());
    }

    /**
     * @return array
     * @throws Exception
     */
    public function bulkAddressSave()
    {
        $addressData = $this->request->getParams();

        if (!is_array($addressData)) {
            throw new Exception(__("Invalid address request data"));
        }

        if (!isset($addressData["addresses"])) {
            throw new Exception(__("Address data is required"));
        }

        foreach ($addressData["addresses"] as $data) {
            $address = $this->addressFactory->create();
            $address->addData($data);
            try {
                $this->addressRepository->save($address->getDataModel());
            } catch (\Throwable $e) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $logger = $objectManager->get('Psr\Log\LoggerInterface');
                $logger->critical("====> [CPOS] Failed to update address: {$e->getMessage()}");
                $logger->critical($e->getTraceAsString());
            }
        }

        return [];
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
        $customerId = $searchCriteria->getData('customerId');
        $storeId = $searchCriteria->getData('storeId');
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
            'items'           => $items,
        ];

        if ($this->integrateHelper->isIntegrateRP()
            && (
                $this->integrateHelper->isAHWRewardPoints()
                || $this->integrateHelper->isAmastyRewardPoints()
                || $this->integrateHelper->isMirasvitRewardPoints()
            )
        ) {
            $data['rp_point_balance'] = $this->integrateHelper
                ->getRpIntegrateManagement()
                ->getCurrentIntegrateModel()
                ->getCurrentPointBalance(
                    $customerId,
                    $this->storeManager->getStore($storeId)->getWebsiteId()
                );
        }

        if ($this->integrateHelper->isIntegrateRP()
            && $this->integrateHelper->isRewardPointMagento2EE()
        ) {
            $data['rp_point_balance'] = $this->integrateHelper
                ->getRpIntegrateManagement()
                ->getCurrentIntegrateModel()
                ->getCurrentPointBalance(
                    $this->getCustomerModel()->load($customerId),
                    $this->storeManager->getStore($storeId)->getWebsiteId()
                );
        }

        if ($this->integrateHelper->isIntegrateStoreCredit()
            && ($this->integrateHelper->isExistStoreCreditMagento2EE() || $this->integrateHelper->isExistStoreCreditAheadworks())
        ) {
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

    public function saveSub()
    {
        $data = $this->getRequestData();

        $email = $data->getData('email');
        $isSubscribe = $data->getData('isSubscribe');
        $checkSubscriber = $this->subscriberFactory->create()->loadByEmail($email);
        if ($isSubscribe) {
            $checkSubscriber->subscribe($email);
        } else {
            if ($checkSubscriber->getSubscriberId() && !$this->dataConfig->isBlockingCustomerFromUnsubscribe()) {
                $checkSubscriber->unsubscribe($email);
            }
        }
    }
}
