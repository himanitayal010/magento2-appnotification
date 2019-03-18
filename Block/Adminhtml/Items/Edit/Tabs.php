<?php

namespace Magneto\AppNotification\Block\Adminhtml\Items\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magneto_appnotification_items_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('App Notification'));
    }
}
