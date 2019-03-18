<?php

namespace Magneto\AppNotification\Model\ResourceModel\AppNotificationUpdate;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Magneto\AppNotification\Model\AppNotificationUpdate',
            'Magneto\AppNotification\Model\ResourceModel\AppNotificationUpdate'
        );
    }
}