<?php

namespace Magneto\AppNotification\Controller\Adminhtml\Items;

class NewAction extends \Magneto\AppNotification\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
