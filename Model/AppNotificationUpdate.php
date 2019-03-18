<?php

namespace Magneto\AppNotification\Model;

use Magento\Framework\Model\AbstractModel;

class AppNotificationUpdate extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Magneto\AppNotification\Model\ResourceModel\AppNotificationUpdate');
    }
}