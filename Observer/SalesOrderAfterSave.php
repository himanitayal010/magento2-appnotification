<?php

namespace Magneto\AppNotification\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class SalesOrderAfterSave implements ObserverInterface
{
	const XML_SERVER_KEY = 'appnotification/general/server_key';

	protected $_eventManager;
	protected $date;
	protected $scopeConfig;

	public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_eventManager = $eventManager;
        $this->date = $date;
        $this->scopeConfig = $scopeConfig;
    }

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
	    $order = $observer->getEvent()->getOrder();
	    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $server_key = $this->scopeConfig->getValue(self::XML_SERVER_KEY, $storeScope);
	    
	    if ($order instanceof \Magento\Framework\Model\AbstractModel) {

	    	if($order->getState() == 'canceled' || $order->getState() == 'closed' || $order->getState() == 'processing' || $order->getState() == 'pending' || $order->getState() == 'holded' || $order->getState() == 'complete' || $order->getState() == 'payment_review' || $order->getState() == 'pending_payment' || $order->getState() == 'fraud' || $order->getState() == 'processing' || $order->getState() == 'paypay_canceled_reversal' || $order->getState() == 'pending_paypal' || $order->getState() == 'paypal_reversed' )
	    	{
	       		$customer_id = $order->getCustomerId();
	       		if($customer_id)
	       		{
	       			$title = "Order Status Changed";
		       		$subtitle = "Your order status has been changed Order Id: #".$order->getId()." Status: ".$order->getState();

		       		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
					$storeId =$storeManager->getStore()->getStoreId();
					if($storeId == '2'){
						$title = "تم تغيير حالة الطلب";
		       			$subtitle = "تم تغيير حالة طلبك. رقم التعريف الخاص بالطلب: #".$order->getId()." Status: ".$order->getState();
					}
					$data = array('title'=> $title, 'subtitle'=> $subtitle, 'customer_id'=> $customer_id);
					$baseUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

		       		$notificationModel = $objectManager->get('\Magneto\AppNotification\Model\AppNotification');
		       		$notificationModel->setData($data);
		       		$notificationModel->save();

	       			// Get Count Start
			        $notificationupdateTable = $objectManager->get('Magneto\AppNotification\Model\ResourceModel\AppNotificationUpdate\Collection')->addFieldToFilter('customer_id', $customer_id);
			        $notif_update_count = count($notificationupdateTable); 

			        $notificationTable = $objectManager->get('Magneto\AppNotification\Model\ResourceModel\AppNotification\Collection')->addFieldToFilter('customer_id', $customer_id);
			        $notif_count = count($notificationTable);

			        $badge = $notif_count - $notif_update_count;
			        // Get Count End

	            	$url = 'https://fcm.googleapis.com/fcm/send';         
	            	$headers = array(
			      				'Authorization:key=AAAAHP7-WOo:APA91bE-8UF0Q2HHcVNok7syzKrKRuC4lA_Q5St-KR3dqgGtWFmu4WNtY-X6Pm0topyHlIH291jktxSWyNRaZw1b-zLV2yW9zb_MR-vc9CbA9uzMy3LxDDpBU_mGXaL9Iq7EeTaQlFN8',
			      				'Content-Type: application/json'
			    			); 	

	            	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

	            	$fcm_token_android = $fcm_token_ios = array();
						$collection = $objectManager->get('\Magneto\AppNotification\Model\ResourceModel\RegisterDevice\Collection')->addFieldToFilter('customer_id', $customer_id);

					if($collection->getData()){
				        	foreach ($collection as $value) {
				        		if($value->getDeviceType() == 'android'){
				        			$fcm_token_android[] = $value->getFirebaseToken();
				        		}
				        		if($value->getDeviceType() == 'ios'){
					        		$fcm_token_ios[] = $value->getFirebaseToken();
				        		}
					        }
					        // Send for ANDROID
				        	$payload = array(
						        // 'to' => $send_to,
						        "registration_ids" => $fcm_token_android,
						        'priority' => 'high',
						        'content_available' => true,
						        "mutable_content" => true,
						        "data" => [
						        	"mediaUrl" => '',
						        	"id" => $customer_id,
						        	"AppName" => 'Ecommerce',
						        	"logo" => "".$baseUrl."pub/media/magneto/appnotification/mobilelogo.png",
						        	"query" => "".$baseUrl."webcomponents/page/myorders",
				            		"navigationFlag" => '1',
				            		"type" => '4',
				            		"title" => $title,
						        	"body" => $subtitle,
						        	"badge" => $badge
						        ]
			        		);
			    			$ch = curl_init();
						    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
						    curl_setopt( $ch,CURLOPT_POST, true );
						    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
						    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
						    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
						    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $payload ) );
						    $result = curl_exec($ch );
						    curl_close( $ch );

						    // Send for IOS
						    $payload = array(
                                    "registration_ids" => $fcm_token_ios,
                                    "priority" => "high",
                                    "mutable_content"=> true,
                                    "notification"=> [
                                        "id"=> "".$customer_id."",
                                        "AppName"=>"Ecommerce",
                                        "logo"=>"".$baseUrl."pub/media/magneto/appnotification/mobilelogo.png",
                                        "title"=>"".$title."",
                                        "subtitle"=>"".$subtitle."",
                                        "badge"=> "".$badge."",
                                        "image"=>"",
                                        "query"=> "".$baseUrl."webcomponents/page/myorders",
                                        "navigationFlag"=> "0", 
                                        "type"=>"1"
                                    ]

                            );
			    			$ch = curl_init();
						    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
						    curl_setopt( $ch,CURLOPT_POST, true );
						    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
						    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
						    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
						    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $payload ) );
						    $result = curl_exec($ch );
						    curl_close( $ch );
					}
	       		}
	       		
	       	}
	    }
	    return $this;
	}
}