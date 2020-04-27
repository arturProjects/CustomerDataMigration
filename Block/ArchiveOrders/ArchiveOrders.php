<?php


namespace Company\CustomerDataMigration\Block\ArchiveOrders;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Company\CustomerDataMigration\Model\MigratedOrdersFactory;
use Magento\Customer\Model\Session;

class ArchiveOrders extends Template
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var MigratedOrdersFactory
     */
    protected $modelMigratedOrdersFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    /**
     * ArchiveOrders constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param MigratedOrdersFactory $modelMigratedOrdersFactory
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct
    (
        Context $context,
        Session $customerSession,
        MigratedOrdersFactory $modelMigratedOrdersFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        RequestInterface $request,
        array $data = []
    )
    {
        $this->modelMigratedOrdersFactory = $modelMigratedOrdersFactory;
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $data);
    }

    /**
     * @return $this|Template
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Archive Orders'));
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->customerSession->getCustomer()->getId();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerEmail()
    {
        $customer = $this->customerRepositoryInterface->getById($this->getCustomerId());
        $email = $customer->getEmail();
        return $email;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getArchiveOrders():array
    {
        $orders = $this->modelMigratedOrdersFactory->create()->getCollection();
        $email = $this->getCustomerEmail();
        $ordersByCustomerEmail = [];
        foreach($orders as $order)
        {
            if($order->getData('email') == $email)
            {
                $ordersByCustomerEmail[] = $order;
            }
        }
        return $ordersByCustomerEmail;
    }
}
