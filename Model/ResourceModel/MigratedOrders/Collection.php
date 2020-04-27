<?php


namespace Company\CustomerDataMigration\Model\ResourceModel\MigratedOrders;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'migrated_orders_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'Company_customerdatamigration_migratedorders_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'archive_orders_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Company\CustomerDataMigration\Model\MigratedOrders', 'Company\CustomerDataMigration\Model\ResourceModel\MigratedOrders');
    }

}
