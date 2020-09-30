<?php
/**
 * Created by mr.vjcspy@gmail.com - khoild@smartosc.com.
 * Date: 09/12/2016
 * Time: 10:05
 */

namespace SM\Customer\Helper;

use Magento\Framework\DataObject;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Data
 *
 * @package SM\Customer\Helper
 */
class Data
{

    const DEFAULT_CUSTOMER_RETAIL_EMAIL = 'guest@sales.connectpos.com';
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Customer\Model\Config\Share
     */
    protected $configShare;

    /**
     * Data constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Config\Share       $configShare
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Config\Share $configShare
    ) {
        $this->storeManager = $storeManager;
        $this->configShare  = $configShare;
    }

    /**
     * @param $customerWebsiteId
     * @param $storeId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isCustomerInStore($customerWebsiteId, $storeId)
    {
        $ids = [];
        if ((bool)$this->configShare->isWebsiteScope()) {
            $ids = $this->storeManager->getWebsite($customerWebsiteId)->getStoreIds();
        } else {
            foreach ($this->storeManager->getStores() as $store) {
                $ids[] = $store->getId();
            }
        }

        return in_array($storeId, $ids);
    }

    public function transformCustomerData(&$customer)
    {
        if ($customer->getData('entity_id')) {
            $customer->setData('id', $customer->getData('entity_id'));
        }
        if ($customer->getData('first_name')) {
            $customer->setData('firstname', mb_convert_case($customer->getData('first_name'), MB_CASE_TITLE, "UTF-8"));
        }
        if ($customer->getData('last_name')) {
            $customer->setData('lastname', mb_convert_case($customer->getData('last_name'), MB_CASE_TITLE, "UTF-8"));
        }
        if ($customer->getData('middle_name')) {
            $customer->setData('middlename', mb_convert_case($customer->getData('middle_name'), MB_CASE_TITLE, "UTF-8"));
        }
        if ($customer->getData('company')) {
            $customer->setData('company', mb_convert_case($customer->getData('company'), MB_CASE_TITLE, "UTF-8"));
        }
        if ($customer->getData('customer_occupation_other_name')) {
            $customer->setData('customer_occupation_other_name', mb_convert_case($customer->getData('customer_occupation_other_name'), MB_CASE_TITLE, "UTF-8"));
        }
        if ($customer->getData('street') && is_array($customer->getData('street'))) {
            $street = [];
            foreach ($customer->getData('street') as $key => $value) {
                $street[$key] = mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
            }
            $customer->setData('street', $street);
        }
        if ($customer->getData('customer_group_id')) {
            $customer->setData('group_id', $customer->getData('customer_group_id'));
        }
        if ($customer->getData('telephone')) {
            $customer->setData('retail_telephone', $customer->getData('telephone'));
        } else {
            $customer->setData('retail_telephone', '');
        }
        if ($customer->getData('avatar')) {
            if (false !== strpos($customer->getData('avatar'), ';base64,')) {
                $file = $this->generateImage($customer->getData('avatar'));
                $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::DEFAULT_URL_TYPE);
                $customer->setData('retail_avatar', $baseUrl . $file);
            } else {
                $customer->setData('retail_avatar', $customer->getData('avatar'));
            }
        }
        if ($customer->getData('veriface')) {
            $customer->setData('retail_veriface', json_encode($customer->getData('veriface')));
        }
        if ($customer->getData('retail_store_id')) {
            $customer->setData('store_id', $customer->getData('retail_store_id'));
        }
        if (is_bool($customer->getData('subscription'))) {
            $customer->setData('subscription', $customer->getData('subscription'));
        }
        if ($customer->getData('guest_id')) {
            $customer->setData('retail_guest_id', $customer->getData('guest_id'));
        }
        if ($customer->getData('scg_customer_group')) {
            $customer->setData('scg_customer_group', (int) $customer->getData('scg_customer_group'));
        }

        return $customer;
    }

    public function generateImage($img)
    {
        $folderPath = "pub/media/retail/pos/facial/";

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $image_parts = explode(";base64,", $img);
        $image_base64 = base64_decode($image_parts[1]);
        $file = $folderPath . uniqid() . '.png';

        file_put_contents($file, $image_base64);
        return $file;
    }
}
