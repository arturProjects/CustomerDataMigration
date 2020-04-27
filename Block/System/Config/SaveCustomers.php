<?php


namespace Company\CustomerDataMigration\Block\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Company\CustomerDataMigration\Helper\Config;


class SaveCustomers extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Company_CustomerDataMigration::system/config/save_customers.phtml';

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * Collect constructor.
     *
     * @param Context $context
     * @param Config $configHelper
     * @param array $data
     */
    public function __construct(Context $context, Config $configHelper, array $data = [])
    {
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * Remove scope label
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('Company_customerdatamigration/system_config/savecustomers');
    }

    /**
     * @return mixed
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'id' => 'save_button',
                'label' => __('Save migrating customers'),
            ]
        );

        return $button->toHtml();
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        $filePath = $this->configHelper->getUploadFilePathCustomers();

        if((!empty($filePath)) && (!is_null($filePath)))
        {
            $fileSplit = explode('/', $filePath);
            $filename = $fileSplit[count($fileSplit) - 1];

            return '/pub/media/migrated/default/'. $filename;
        }

        return '';
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        $filePath = $this->configHelper->getUploadFilePathCustomers();

        if((!empty($filePath)) && (!is_null($filePath)))
        {
            $fileSplit = explode('/', $filePath);
            $filename = $fileSplit[count($fileSplit) - 1];

            return $filename;
        }

        return '';
    }
}
