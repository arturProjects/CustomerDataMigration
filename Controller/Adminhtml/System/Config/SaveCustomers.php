<?php


namespace Company\CustomerDataMigration\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Company\CustomerDataMigration\Helper\CsvCustomerProcess;
use Company\CustomerDataMigration\Helper\Config;


class SaveCustomers extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CsvCustomerProcess
     */
    protected $CompanyCsvCustomerProcessHelper;

    /**
     * @var Config
     */
    protected $CompanyConfigHelper;

    /**
     * Save constructor.
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CsvCustomerProcess $CompanyCsvCustomerProcessHelper
     * @param Config $CompanyConfigHelper
     */
    public function __construct(Context $context, JsonFactory $resultJsonFactory, CsvCustomerProcess $CompanyCsvCustomerProcessHelper, Config $CompanyConfigHelper)
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->CompanyCsvCustomerProcessHelper = $CompanyCsvCustomerProcessHelper;
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
        $filePath = $this->CompanyConfigHelper->getUploadFilePathCustomers();
        if((!empty($filePath)) && (!is_null($filePath)))
        {
            $this->CompanyCsvCustomerProcessHelper->getUploadedCsvFile($filePath);
        }
    }
}
