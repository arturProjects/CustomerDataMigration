<?php


namespace Company\CustomerDataMigration\Block\Adminhtml\CustomerEdit\Tab;


use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Company\CustomerDataMigration\Model\MigratedOrdersFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

class View extends Template implements TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'tab/customer_view.phtml';

    /**
     * @var Registry
     */
    private $_coreRegistry;

    /**
     * @var $modelMigratedOrdersFactory
     */
    private $modelMigratedOrdersFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepositoryInterface;

    /**
     * View constructor.
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct
    (
        Context $context,
        Registry $registry,
        MigratedOrdersFactory $modelMigratedOrdersFactory,
        CustomerRepositoryInterface $customerRepositoryInterface,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        $this->modelMigratedOrdersFactory = $modelMigratedOrdersFactory;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        parent::__construct($context, $data);
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
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

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Archive Orders');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Archive Orders');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }

}
