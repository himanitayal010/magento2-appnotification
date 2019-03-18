<?php

namespace Magneto\AppNotification\Model\ResourceModel\AppNotification;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'appnotification_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Magneto\AppNotification\Model\AppNotification',
            'Magneto\AppNotification\Model\ResourceModel\AppNotification'
        );
    }
}