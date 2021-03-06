<?php


namespace Company\CustomerDataMigration\Model\Config\Backend;

class CustomFileType extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * @return string[]
     */
    public function getAllowedExtensions() {
        return ['csv', 'xls'];
    }
}
