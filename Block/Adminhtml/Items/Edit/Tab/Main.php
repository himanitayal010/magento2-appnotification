<?php

namespace Magneto\AppNotification\Block\Adminhtml\Items\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface
{
    protected $systemStore;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Magento\Framework\Registry $registry, 
        \Magento\Framework\Data\FormFactory $formFactory, 
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) 
    {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('App Notification');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('App Notification');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model = $this->_coreRegistry->registry('current_magneto_appnotification_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('App Notification')]);
        if ($model->getId()) {
            $fieldset->addField('appnotification_id', 'hidden', ['name' => 'appnotification_id']);
        }
        $fieldset->addField(
            'title',
            'text',
            ['name' => 'title', 'label' => __('Title'), 'title' => __('Title'), 'required' => true]
        );
        $fieldset->addField(
            'subtitle',
            'text',
            ['name' => 'subtitle', 'label' => __('Subtitle'), 'title' => __('Subtitle'), 'required' => false]
        );
        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required'  => false
            ]
        );
        $fieldset->addField(
            'customer_id',
            'multiselect',
            [
                'name' => 'customer_id', 
                'label' => __('Select Customers'), 
                'title' => __('Select Customers'),  
                'required' => true,
                'values' => $objectManager->get('\Magneto\AppNotification\Model\Source\Customers')->toOptionArray()
            ]
        );
        $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name'     => 'store_ids[]',
                'label'    => __('Store Views'),
                'title'    => __('Store Views'),
                'required' => true,
                'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
            ]
        );
        $fieldset->addField(
            'created_at',
            'date',
            [
                'name' => 'created_at',
                'label' => __('Created At'),
                'date_format' => 'yyyy-MM-dd',
                'time_format' => 'hh:mm:ss'
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
