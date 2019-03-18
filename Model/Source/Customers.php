<?php

namespace Magneto\AppNotification\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Option\ArrayInterface;

/**
 * Item status functionality model
 */
class Customers implements ArrayInterface
{
    protected $_customerFactory;

    public function __construct(
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory
    ) {
        $this->_customerFactory = $customerFactory;
    }

    /**
     * Get customer collection
     */
    public function getCustomerCollection()
    {
        return $this->_customerFactory->create();
    }


    /*
    *   Option getter
    *   @return array
    */
    public function toOptionArray()
    {
        $arr = $this->toArray();
        $ret = [];
        $ret[] = ['value' => 0, 'label' => 'All Customers'];
        foreach ($arr as $key => $value) {

            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }

    /*
    *   Get options in "key-value" format
    *   @return array
    */
    public function toArray()
    {
        $customers = $this->getCustomerCollection();

        $customersList = array();
        foreach ($customers as $customer) {

            $customersList[$customer->getEntityId()] = __($customer->getName());
        }
        return $customersList;
    }
}