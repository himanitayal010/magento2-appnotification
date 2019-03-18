<?php

namespace Magneto\AppNotification\Model\ResourceModel;

class RegisterDevice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('magneto_device_register', 'id');
    }
}