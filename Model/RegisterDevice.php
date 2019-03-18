<?php

namespace Magneto\AppNotification\Model;

use Magento\Framework\Model\AbstractModel;

class RegisterDevice extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Magneto\AppNotification\Model\ResourceModel\RegisterDevice');
    }
}