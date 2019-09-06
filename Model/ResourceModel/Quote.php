<?php

namespace SM\Customer\Model\ResourceModel;


/**
 * Quote resource model
 */
class Quote extends \Magento\Quote\Model\ResourceModel\Quote {

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
    public function loadByCustomerId($quote, $customerId) {
        $isConnectPOs  = $this->registry->registry('is_connectpos');
        $connection    = $this->getConnection();
        if ($isConnectPOs) {
            $select = $this->_getLoadSelect(
                'customer_id',
                $customerId,
                $quote
            )->order(
                'created_at ' . \Magento\Framework\DB\Select::SQL_DESC
            )->limit(
                1
            );
        }else {
            $select = $this->_getLoadSelect(
                'customer_id',
                $customerId,
                $quote
            )->where(
                'is_active = ?',
                1
            )->order(
                'updated_at ' . \Magento\Framework\DB\Select::SQL_DESC
            )->limit(
                1
            );
        }
        $data = $connection->fetchRow($select);
        if ($data) {
            $quote->setData($data);
        }

        $this->_afterLoad($quote);

        return $this;
    }
}
