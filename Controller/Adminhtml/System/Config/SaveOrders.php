<?php


namespace Company\CustomerDataMigration\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Company\CustomerDataMigration\Helper\CsvOrdersProcess;
use Company\CustomerDataMigration\Helper\Config;



class SaveOrders extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CsvOrdersProcess
     */
    protected $CompanyCsvOrdersProcessHelper;

    /**
     * @var Config
     */
    protected $CompanyConfigHelper;

    /**
     * Save constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CsvOrdersProcess $CompanyCsvOrdersProcessHelper
     * @param Config $CompanyConfigHelper
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory, CsvOrdersProcess $CompanyCsvOrdersProcessHelper, Config $CompanyConfigHelper)
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->CompanyCsvOrdersProcessHelper = $CompanyCsvOrdersProcessHelper;
        $this->CompanyConfigHelper = $CompanyConfigHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $filePath = $this->CompanyConfigHelper->getUploadFilePathOrders();
        if((!empty($filePath)) && (!is_null($filePath)))
        {
            $this->CompanyCsvOrdersProcessHelper->getUploadedCsvFile($filePath);
        }
    }
}
