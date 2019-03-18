<?php

namespace Magneto\AppNotification\Block\Adminhtml;

class Items extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'items';
        $this->_headerText = __('App Notification');
        $this->_addButtonLabel = __('Add New App Notification');
        parent::_construct();
    }
}
