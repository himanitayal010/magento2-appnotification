<?php

namespace Magneto\AppNotification\Model\ResourceModel\RegisterDevice;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Magneto\AppNotification\Model\RegisterDevice',
            'Magneto\AppNotification\Model\ResourceModel\RegisterDevice'
        );
    }
}