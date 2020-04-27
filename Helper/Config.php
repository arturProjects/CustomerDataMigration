<?php


namespace Company\CustomerDataMigration\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Config extends AbstractHelper
{
    const CONFIG_UPLOAD_FILE_PATH_CUSTOMERS = 'customer_data_migration/upload_customer_data/upload_file_path';
    const CONFIG_UPLOAD_FILE_PATH_ORDERS = 'customer_data_migration/upload_order_data/upload_file_path';
    const MODULE_ENABLED = 'customer_data_migration/configuration/enable';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Config constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(Context $context, ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Get value from configuration
     *
     * @param $configPath
     *
     * @return mixed
     */
    protected function getConfig($configPath)
    {
        return $this->scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isModuleEnabled(): bool
    {
        return ($this->getConfig(self::MODULE_ENABLED) == 1)? true: false;
    }

    /**
     * @return mixed
     */
    public function getUploadFilePathCustomers()
    {
        return $this->getConfig(self::CONFIG_UPLOAD_FILE_PATH_CUSTOMERS);
    }

    /**
     * @return mixed
     */
    public function getUploadFilePathOrders()
    {
        return $this->getConfig(self::CONFIG_UPLOAD_FILE_PATH_ORDERS);
    }
}
