<?php

namespace Magneto\AppNotification\Controller\Adminhtml\Items;

class Delete extends \Magneto\AppNotification\Controller\Adminhtml\Items
{

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->_objectManager->create('Magneto\AppNotification\Model\AppNotification');
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the item.'));
                $this->_redirect('magneto_appnotification/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t delete item right now. Please review the log and try again.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_redirect('magneto_appnotification/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find a item to delete.'));
        $this->_redirect('magneto_appnotification/*/');
    }
}
