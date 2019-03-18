<?php

namespace Magneto\AppNotification\Model\ResourceModel;

class AppNotification extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('magneto_appnotification', 'appnotification_id');   //here "magneto_appnotification" is table name and "appnotification_id" is the primary key of custom table
    }
}