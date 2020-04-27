<?php


namespace Company\CustomerDataMigration\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;
use Company\CustomerDataMigration\Model\MigratedOrdersFactory;


class CsvOrdersProcess extends AbstractHelper
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
     * @var MigratedOrdersFactory
     */
    protected $modelMigratedOrdersFactory;


    public function __construct(
        Csv $csvProcessor,
        DirectoryList $directoryList,
        Context $context,
        StoreManagerInterface $storeManager,
        State $state,
        MigratedOrdersFactory $modelMigratedOrdersFactory
     )
    {
        $this->csvProcessor = $csvProcessor;
        $this->directoryList = $directoryList;
        $this->storeManager = $storeManager;
        $this->state = $state;
        $this->modelMigratedOrdersFactory = $modelMigratedOrdersFactory;
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
        $fileIn = $directory . '/orders/' . $file;

        if (!isset($fileIn)) {
            throw new LocalizedException(__('File not exist. Check if file is directory var and filename is correctly.'));
        }
        $ordersToSave = $this->prepareDataFromCsv($this->csvProcessor->getData($fileIn));
        $this->saveArchiveOrders($ordersToSave);
        return '';

    }

    /**
     * @param $importOrdersRawData
     * @return array
     */
    protected function prepareDataFromCsv($importOrdersRawData)
    {
        array_shift($importOrdersRawData);
        $migratingOrders = [];
        foreach ($importOrdersRawData as $order)
        {
            $orderData = [];
            $orderData['original_id'] = $order[0];
            $orderData['created_at'] = trim($order[1], '"');
            $orderData['updated_at'] = trim($order[2], '"');
            $orderData['customer'] = trim($order[3], '"');
            $orderData['price'] = $order[4];
            $orderData['paid'] = $this->isPaid($order[5]);
            $orderData['date_of_payment'] = trim($order[6], '"');
            $orderData['status'] = $order[8];
            $orderData['original_number_of_payment'] = $order[7];
            $orderData['email'] = $order[9];
            $orderData['invoice'] = $this->isInvoice($order[10]);
            $orderData['archive_order'] = $this->collectionOfProducts([$order[12], $order[16], $order[20], $order[24], $order[28], $order[32], $order[36], $order[40], $order[44], $order[48]]);

            $migratingOrders[] = $orderData;
            unset($orderData);

        }
        return $migratingOrders;
    }


    /**
     * @param $orders
     * @throws \Exception
     */
    public function saveArchiveOrders($orders)
    {
        foreach ($orders as $raw)
        {
            $migratedOrder = $this->modelMigratedOrdersFactory->create();
            if($this->isOriginalIdSaved($raw['original_id']))
            {
                continue;
            }
            $migratedOrder->setData(
                [
                    'original_id' => $raw['original_id'],
                    'created_at' => $raw['created_at'],
                    'updated_at' => $raw['updated_at'],
                    'customer' => $raw['customer'],
                    'price' => $raw['price'],
                    'paid' => $raw['paid'],
                    'date_of_payment' => $raw['date_of_payment'],
                    'status' => $raw['status'],
                    'original_number_of_payment' => $raw['original_number_of_payment'],
                    'email' => $raw['email'],
                    'invoice' => $raw['invoice'],
                    'archive_order' => $raw['archive_order']
                ]);
            $migratedOrder->save();
        }
    }

    /**
     * @param array $products
     * @return string
     */
    public function collectionOfProducts(array $products)
    {
        $q = count($products);
        $stringOfProducts = [];
        for($i = 0; $i < $q; $i++)
        {
           if(!empty($products[$i]))
           {
               $stringOfProducts[] = $products[$i];
           }
        }
        return implode(',', $stringOfProducts);
    }

    /**
     * @param $paid
     * @return bool
     */
    public function isPaid($paid)
    {
        return ($paid == 'Tak') ? true : false;
    }

    /**
     * @param $invoice
     * @return bool
     */
    public function isInvoice($invoice)
    {
        return ($invoice == 'Tak') ? true : false;
    }

    /**
     * @param $originalId
     * @return bool
     */
    public function isOriginalIdSaved($originalId)
    {
        $orders = $this->modelMigratedOrdersFactory->create()->getCollection();
        foreach($orders as $order)
        {
            if($order->getData('original_id') == $originalId)
            {
                return true;
            }
        }

        return false;
    }
}
