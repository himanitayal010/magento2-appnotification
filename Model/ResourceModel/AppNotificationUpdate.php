<?php

namespace Magneto\AppNotification\Model\ResourceModel;

class AppNotificationUpdate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('magneto_appnotification_update', 'id');   //here "magneto_appnotification" is table name and "id" is the primary key of custom table
    }
}