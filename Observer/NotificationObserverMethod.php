<?php

namespace Magneto\AppNotification\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class NotificationObserverMethod implements ObserverInterface
{
	const XML_SERVER_KEY = 'appnotification/general/server_key';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * AdminFailed constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		$baseUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
		$storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $server_key = $this->scopeConfig->getValue(self::XML_SERVER_KEY, $storeScope);

        try{
            $eventData = $observer->getData();

            if($eventData[0]){
            	foreach($eventData[0]['customer_id'] as $cusId){
            		$customer_id = $cusId;
            		$title = $eventData[0]['title'];
            		if(!empty($eventData[0]['subtitle'])){
            			$subtitle = $eventData[0]['subtitle'];
            		}else{
            			$subtitle = '';
            		}
            		if(!empty($eventData[0]['image'])){
            			$image = $baseUrl.'pub/media/'.$eventData[0]['image'];
            		}else{
            			$image = '';
            		}	            		
	            		
	            	$created_at = $eventData[0]['created_at'];

	            	// Get Count Start
			        $notificationupdateTable = $objectManager->get('Magneto\AppNotification\Model\ResourceModel\AppNotificationUpdate\Collection')->addFieldToFilter('customer_id', $customer_id);
			        $notif_update_count = count($notificationupdateTable); 

			        $notificationTable = $objectManager->get('Magneto\AppNotification\Model\ResourceModel\AppNotification\Collection')->addFieldToFilter('customer_id', $customer_id);
			        $notif_count = count($notificationTable);

			        $badge = $notif_count - $notif_update_count;
			        // Get Count End

	            	$url = 'https://fcm.googleapis.com/fcm/send';         
	            	$headers = array(
			      				'Authorization:key='.$server_key,
			      				'Content-Type: application/json'
			    			); 	

	            	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

	            	$fcm_token_android = $fcm_token_ios = array();
					if($customer_id == '0'){
						$collection = $objectManager->get('\Magneto\AppNotification\Model\ResourceModel\RegisterDevice\Collection');
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
						        	"mediaUrl" => $image,
						        	"id" => $customer_id,
						        	"AppName" => 'Ecommerce',
						        	"logo" => "".$baseUrl."pub/media/magneto/appnotification/mobilelogo.png",
						        	"query" => "".$baseUrl."webcomponents/page/myorders",
				            		"navigationFlag" => '0',
				            		"type" => '1',
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


						    // Send For IOS
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
                                        "image"=>"".$image."",
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
					}else{
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
						        	"mediaUrl" => $image,
						        	"id" => $customer_id,
						        	"AppName" => 'Ecommerce',
						        	"logo" => "".$baseUrl."pub/media/magneto/appnotification/mobilelogo.png",
						        	"query" => "".$baseUrl."webcomponents/page/myorders",
				            		"navigationFlag" => '0',
				            		"type" => '1',
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
                                        "image"=>"".$image."",
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
        }catch(Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
    }
}