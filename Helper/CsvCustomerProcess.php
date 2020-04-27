<?php


namespace Company\CustomerDataMigration\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;
use Magento\Customer\Model\CustomerFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\Store;



class CsvCustomerProcess extends AbstractHelper
{
    /**
     * @var Csv
     */
    protected $csvProcessor;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * CsvCustomerProcess constructor.
     * @param Csv $csvProcessor
     * @param DirectoryList $directoryList
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param State $state
     * @param CustomerFactory $customerFactory
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        Csv $csvProcessor,
        DirectoryList $directoryList,
        Context $context,
        StoreManagerInterface $storeManager,
        State $state,
        CustomerFactory $customerFactory,
        SubscriberFactory $subscriberFactory
        )
        {
            $this->csvProcessor = $csvProcessor;
            $this->directoryList = $directoryList;
            $this->storeManager = $storeManager;
            $this->state = $state;
            $this->customerFactory = $customerFactory;
            $this->subscriberFactory = $subscriberFactory;
            parent::__construct($context);
        }

    /**
     * @param $file
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws LocalizedException
     */
    public function getUploadedCsvFile($file)
    {
        $directory = $this->directoryList->getPath(DirectoryList::MEDIA);
        $fileIn = $directory . '/migrated/' . $file;

        if (!isset($fileIn)) {
            throw new LocalizedException(__('File not exist. Check if file is directory var and filename is correctly.'));
        }

        $customersToSave = $this->prepareDataFromCsv($this->csvProcessor->getData($fileIn));
        $this->createAccountForMigratedCustomers($customersToSave);
        return '';

    }

    /**
     * @param $importCustomersRawData
     * @return array
     */
    protected function prepareDataFromCsv($importCustomersRawData)
    {
        array_shift($importCustomersRawData);
        $migratingCustomers = [];
        $counter = 0;

        foreach ($importCustomersRawData as $customer)
        {
            $customerData = [];
            $nameAndSurname = $this->getFirstNameAndLastName($customer[0]);

                $customerData['first_name'] = $nameAndSurname[1];
                $customerData['last_name'] = $nameAndSurname[0];
                $customerData['email'] = trim($customer[1]);
                $customerData['newsletter'] = $customer[2];
                $customerData['password'] = 'Abc123!@#';
                $customerData['amgdpr_agree'] = 1;
                $customerData['converse_regulation'] = 1;
                $customerData['process_personal_data'] = 1;

                $migratingCustomers[] = $customerData;
                unset($customerData);
                $counter++;
        }

        return $migratingCustomers;
    }

    /**
     * @param $nameAndSurname
     * @return array
     */
    public function getFirstNameAndLastName($nameAndSurname)
    {
        $array = preg_split('/[\s]+/', $nameAndSurname);
        $lastName = $array[0];
        $firstName = $array[1];
        return [$firstName, $lastName];
    }

    /**
     * @param $customers
     * @throws \Exception
     */
    public function createAccountForMigratedCustomers($customers)
    {
        foreach($customers as $raw)
        {
            $customer = $this->customerFactory->create();
            if($this->isEmailUniq($raw['email']))
            {
                continue;
            }
            $customer->setData(
                [
                    'firstname' => $raw['first_name'],
                    'lastname' => $raw['last_name'],
                    'email' => $raw['email'],
                    'password' => $raw['password'],
                    'amgdpr_agree' => $raw['amgdpr_agree'],
                    'converse_regulation' => $raw['converse_regulation'],
                    'process_personal_data' => $raw['process_personal_data']
               ]);
            $customer->save();
            if($this->isSubscriber($raw['newsletter']))
            {
                $this->subscriberFactory->create()
                     ->setStatus(Subscriber::STATUS_SUBSCRIBED)
                     ->setEmail($raw['email'])
                     ->setCustomerId($customer->getId())
                     ->setStoreId(Store::DEFAULT_STORE_ID)
                     ->save();
            }
        }
    }

    /**
     * @param $email
     * @return bool
     */
    public function isEmailUniq($email)
    {
        $customers = $this->customerFactory->create()->getCollection();
        foreach($customers as $customer)
        {
            if($customer->getData('email') == $email)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $newsletter
     * @return bool
     */
    public function isSubscriber($newsletter)
    {
        return ($newsletter == 'Tak') ? true : false;
    }
}
