<?php


namespace Company\CustomerDataMigration\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class MigratedOrders extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'merrell_migrated_archive_orders';

    /**
     * @var string
     */
    protected $_cacheTag = 'merrell_migrated_archive_orders';

    /**
     * @var string
     */
    protected $_eventPrefix = 'merrell_migrated_archive_orders';


    protected function _construct()
    {
        $this->_init('Company\CustomerDataMigration\Model\ResourceModel\MigratedOrders');
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
