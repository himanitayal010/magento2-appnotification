<?php

namespace Magneto\AppNotification\Controller\Adminhtml\Items;

class Index extends \Magneto\AppNotification\Controller\Adminhtml\Items
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magneto_AppNotification::test');
        $resultPage->getConfig()->getTitle()->prepend(__('App Notification'));
        $resultPage->addBreadcrumb(__('App'), __('App'));
        $resultPage->addBreadcrumb(__('Notification'), __('Notification'));
        return $resultPage;
    }
}