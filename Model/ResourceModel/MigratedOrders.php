<?php


namespace Company\CustomerDataMigration\Model\ResourceModel;


use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class MigratedOrders extends AbstractDb
{
    /**
     * MigratedOrders constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('merrell_migrated_archive_orders', 'migrated_orders_id');
    }
}
