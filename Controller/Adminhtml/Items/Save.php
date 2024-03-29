<?php

namespace Magneto\AppNotification\Controller\Adminhtml\Items;

class Save extends \Magneto\AppNotification\Controller\Adminhtml\Items
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('Magneto\AppNotification\Model\AppNotification');
                $data = $this->getRequest()->getPostValue();

                if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
                    try{
                        $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'image']);
                        $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                        $imageAdapter = $this->adapterFactory->create();
                        $uploaderFactory->addValidateCallback('custom_image_upload',$imageAdapter,'validateUploadFile');
                        $uploaderFactory->setAllowRenameFiles(true);
                        $uploaderFactory->setFilesDispersion(true);
                        $mediaDirectory = $this->filesystem->getDirectoryRead($this->directoryList::MEDIA);
                        $destinationPath = $mediaDirectory->getAbsolutePath('magneto/appnotification');
                        $result = $uploaderFactory->save($destinationPath);
                        if (!$result) {
                            throw new LocalizedException(
                                __('File cannot be saved to path: $1', $destinationPath)
                            );
                        }
                        
                        $imagePath = 'magneto/appnotification'.$result['file'];
                        $data['image'] = $imagePath;
                    } catch (\Exception $e) {
                    }
                }
                if(isset($data['image']['delete']) && $data['image']['delete'] == 1) {
                    $mediaDirectory = $this->filesystem->getDirectoryRead($this->directoryList::MEDIA)->getAbsolutePath();
                    $file = $data['image']['value'];
                    $imgPath = $mediaDirectory.$file;
                    if ($this->_file->isExists($imgPath))  {
                        $this->_file->deleteFile($imgPath);
                    }
                    $data['image'] = NULL;
                }
                if (isset($data['image']['value'])){
                    $data['image'] = $data['image']['value'];
                }
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }
                $sendData = $data;

                $customer_id = $data['customer_id'];
                unset($data['customer_id']);
                $store_ids = $data['store_ids'];

                foreach ($customer_id as $value) {
                    $data['customer_id'] = $value;
                    foreach ($store_ids as $keyvalue) {
                        $data['store_ids'] = $keyvalue;
                        $model->setData($data);
                        $session = $this->_objectManager->get('Magento\Backend\Model\Session');

                        $session->setPageData($model->getData());

                        $model->save();
                    }

                    
                }                
                $this->_eventManager->dispatch(
                    'notification_observer_name',
                    [$sendData]
                );
                
                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('magneto_appnotification/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('magneto_appnotification/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('magneto_appnotification/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('magneto_appnotification/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('magneto_appnotification/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('magneto_appnotification/*/');
    }
}
