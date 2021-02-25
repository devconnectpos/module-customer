<?php

namespace SM\Customer\Model\ResourceModel;


/**
 * Quote resource model
 */
class Quote extends \Magento\Quote\Model\ResourceModel\Quote
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite $entityRelationComposite,
        \Magento\SalesSequence\Model\Manager $sequenceManager,
        \Magento\Framework\Registry $registry,
        $connectionName = null
    ) {
        parent::__construct($context, $entitySnapshot, $entityRelationComposite, $sequenceManager, $connectionName);
        $this->registry = $registry;
    }

    /**
     * Load quote data by customer identifier
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param int                        $customerId
     *
     * @return $this
     */
    public function loadByCustomerId($quote, $customerId)
    {
        $isConnectPOS = $this->registry->registry('is_connectpos');
        $connection = $this->getConnection();
        if ($isConnectPOS) {
            $select = $this->_getLoadSelect(
                'customer_id',
                $customerId,
                $quote
            )->order(
                'created_at '.\Magento\Framework\DB\Select::SQL_DESC
            )->limit(
                1
            );
        } else {
            $select = $this->_getLoadSelect(
                'customer_id',
                $customerId,
                $quote
            )->where(
                'is_active = ?',
                1
            )->order(
                'updated_at '.\Magento\Framework\DB\Select::SQL_DESC
            )->limit(
                1
            );
        }

        $data = $connection->fetchRow($select);

        if ($data) {
            $quote->setData($data);

            // XRT-6183: Fix issue of Mr's Leather client in which address could not be saved to the address book in frontend
            // The $isConnectPOS flag is checked as a safe method to make sure original logic is kept when processing the quote through CPOS
            if (!$isConnectPOS) {
                $quote->setOrigData();
            }
        }

        $this->_afterLoad($quote);

        return $this;
    }
}
