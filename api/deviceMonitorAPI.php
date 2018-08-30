<?php
date_default_timezone_set('Asia/Calcutta');
require "../utilities/class.MySQL.php";

define('FIREBASE_API_KEY', 'AAAAC70odRs:APA91bFnp6fLtZ_2iyNMkcTMCcJSBCg5s-z5VlrLgjhOA_r8wh8ihuCTmuI3p_EG6Y4q-A6elApSf5R5FNP96R3RX6vkE7OLsDST3zs1uTEedo2PfTtZpMGfb01fHU5Vx7eLqPjMY1PS');

$parameterString=file_get_contents("php://input");



$requestParameters=json_decode($parameterString,true);

if (json_last_error() === JSON_ERROR_NONE) {
	//sendResponse("Failure","parsed JSON String, ");
} else { 
    sendResponse("Failure","Invalid JSON String, ".json_last_error()); 
}
//print_r($requestParameters);
//exit();
$commandToExecute=$requestParameters['commandToExecute']?$requestParameters['commandToExecute']:"";
$serialNumber=$requestParameters['serialNumber']?$requestParameters['serialNumber']:"";
//$inputParams = print_r($requestParameters,true);

logIncomingRequest($serialNumber,$commandToExecute,$parameterString);

$errorString="";

switch($commandToExecute) {
	case "processDevicePolls":
		$serialNumber=$requestParameters['serialNumber']?$requestParameters['serialNumber']:"";
		$event=$requestParameters['event']?$requestParameters['event']:"";
		$key=$requestParameters['key']?$requestParameters['key']:"";
		$value=$requestParameters['value']?$requestParameters['value']:"0";
		processDevicePolls($serialNumber,$event,$key,$value);
		break;
	case "customerLogin":
		$email=$requestParameters['email']?$requestParameters['email']:"";
		$password=$requestParameters['password']?$requestParameters['password']:"";
		customerLogin($email,$password);
		break;
	case "addCustomer":
		$firstname=$requestParameters['firstname']?$requestParameters['firstname']:"";
		$lastname=$requestParameters['lastname']?$requestParameters['lastname']:"";
		$primaryMobile=$requestParameters['primaryMobile']?$requestParameters['primaryMobile']:"1";
		$secondaryMobile=$requestParameters['secondaryMobile']?$requestParameters['secondaryMobile']:"";
		$email=$requestParameters['email']?$requestParameters['email']:"";
		$password=$requestParameters['password']?$requestParameters['password']:"";		
		addCustomer($firstname,$lastname,$primaryMobile,$secondaryMobile,$email,$password);
		break;
	case "deleteCustomer":
		$customerId=$requestParameters['customerId']?$requestParameters['customerId']:"0";
		deleteCustomer($customerId);
		break;
	case "getDeviceTypes":		
		getDeviceTypes();
		break;
	case "getCustomerDevices":
		$customerId=$requestParameters['customerId']?$requestParameters['customerId']:"0";
		getCustomerDevices($customerId);
		break;	
	case "registerDevice":
		$customerId=$this->_request['customerId'];
		$serialNumber=$this->_request['serialNumber'];
		$latitude=$this->_request['latitude'];
		$longitude=$this->_request['longitude'];
		$simpleName=$this->_request['simpleName'];
		$mobileNumber=$this->_request['mobileNumber'];
		registerDevice($customerId,$serialNumber,$latitude,$longitude,$simpleName,$mobileNumber);
		break;
	case "saveCustomerFCMToken":
		$customerId=$requestParameters['customerId']?$requestParameters['customerId']:"0";
		$fcmToken=$requestParameters['fcmToken']?$requestParameters['fcmToken']:"";
		$deviceID=$requestParameters['deviceID']?$requestParameters['deviceID']:"";
		$deviceOS=$requestParameters['deviceOS']?$requestParameters['deviceOS']:"";
		saveCustomerFCMToken($customerId,$fcmToken,$deviceID,$deviceOS);
		break;
	default:
		sendResponse("Failure","This command is not enabled yet.");
		break;
}

function processDevicePolls($sn,$event,$key,$value){
	$db = new MySQL();
	$deviceData = $db->executeQueryAndGetFirstRow("SELECT deviceId,deviceTypeId FROM devices WHERE serialNumber='".mysqli_real_escape_string($db->mysql_link,$sn)."'");
	if($deviceData===FALSE){
		sendResponse("Failure","Invalid Serial Number.");
	}
	
	$customerData = $db->executeQueryAndGetFirstRow("SELECT customerDeviceId,customerId,simpleName FROM customerdevices WHERE deviceId='".mysqli_real_escape_string($db->mysql_link,$deviceData->deviceId)."' AND isLinkActive=1 ORDER BY customerDeviceId DESC LIMIT 1");
	if($customerData===FALSE){
		sendResponse("Failure","This serial Number doesn't linked to any customer");
	}

	$deviceEventId = $db->getFirstRowFirstColumn("SELECT deviceEventId FROM deviceevents WHERE deviceTypeId='".mysqli_real_escape_string($db->mysql_link,$deviceData->deviceTypeId)."' AND eventName='".mysqli_real_escape_string($db->mysql_link,$event)."';");
	
	$actionToTake=$db->getFirstRowFirstColumn("SELECT actionToTake FROM eventactions WHERE deviceEventId='".mysqli_real_escape_string($db->mysql_link,$deviceEventId)."' AND `key`='".mysqli_real_escape_string($db->mysql_link,$key)."' AND `value`='".mysqli_real_escape_string($db->mysql_link,$value)."';");
	
	if($deviceEventId===FALSE){
		sendResponse("Failure","There is some technical error while processing your request.");
	}

	$dateReceived = time();

	$query = "INSERT INTO devicepolls(customerDeviceId,deviceEventId,dateReceived) VALUES('".mysqli_real_escape_string($db->mysql_link,$customerData->customerDeviceId)."','".mysqli_real_escape_string($db->mysql_link,$deviceEventId)."','".date('Y-m-d H:i:s',$dateReceived)."');";

	if($db->executeQuery($query)) {
		$devicePollId = $db->getLastInsertID();
		$query1 = "INSERT INTO devicepollextras(devicePollId,`key`,`value`) VALUES('".mysqli_real_escape_string($db->mysql_link,$devicePollId)."','".mysqli_real_escape_string($db->mysql_link,$key)."','".mysqli_real_escape_string($db->mysql_link,$value)."');";
		if(!$db->executeQuery($query1)){
			sendResponse("Failure",$db->getErrorString());
		}		
		
		//Everything done well. So need to send push notification if actionToTake exists
		if($actionToTake===FALSE){
			sendResponse("Success","Device Poll Saved Successfully. There is no actions to take");
		}
		//Only for Push notification we need fcm data. So getting here
		$fcm=$db->executeQueryAndGetFirstRow("SELECT customerFCMTokenId,fcmToken,deviceOS FROM customerfcmtokens WHERE customerId='".mysqli_real_escape_string($db->mysql_link,$customerData->customerId)."'");
		if($fcm === FALSE){			
			sendResponse("Failure",$db->getErrorString());			
		}
		
		try{
			//$notify = sendPushNotification($fcm->fcmToken,$sn,$event,$actionToTake,$key,$value,date('d-m-Y h:i:s a',$dateReceived));
			//sendResponse("Success",$notify);			
			if(sendPushNotification($fcm->fcmToken,$sn,$event,$actionToTake,$key,$value,date('d-m-Y h:i:s a',$dateReceived))){
				sendResponse("Success","Device Poll Saved Successfully. ".$actionToTake." Notification Sent");
			}else{
				sendResponse("Failure","Error Sending Notification");
			}
		}catch(Exception $e){
			sendResponse("Failure",$e->getMessage());
		}
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function customerLogin($email,$password) {
	$db = new MySQL();
	if($email != '' && $password != '') {
		$query = "SELECT customerId FROM customers WHERE 
		email='".mysqli_real_escape_string($db->mysql_link,$email)."' AND 
		password='".mysqli_real_escape_string($db->mysql_link,$password)."';";
		$customer = $db->executeQueryAndGetFirstRow($query);
		if($customer===FALSE) {
			sendResponse("Failure","Invalid Credentials.");
		} else {
			//var_dump($customer);
			$customerId=$customer->customerId;
			$query="SELECT customerFCMTokenId,fcmToken,deviceOS FROM customerfcmtokens 
			WHERE customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."' 
			ORDER BY dateReceived DESC LIMIT 1";
			$fcmObject=$db->executeQueryAndGetFirstRow($query);
			if($fcmObject===FALSE) {
				$errorString .= " No other token or active session found for this Customer.";
			} else {
				if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
					$errorString .= " ".$db->getErrorString();
				}
				$fcmToken = $fcmObject->fcmToken;
				$deviceOS = $fcmObject->deviceOS;
				
				$dataArray = array(
					'remoteCommand' => "logoutCustomer"
				);
				$messageArray = array(
					'to' => $fcmToken,
					'data' => $dataArray,
					'priority' => "high"
				);
				$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
				if($sendNotificationResult===true) {
					$errorString = "Sent Logout Message to previous logged in session.";
				} else {
					$errorString.= " ".$sendNotificationResult;
				}
			}
			$messageArray=array();
			$messageArray["customerId"]=$customerId;
			$messageArray["extras"]=$errorString;
			sendResponse("Success",$messageArray);
		}
	} else {
		sendResponse("Failure","All parameters are compulsory.");
	}
}
function addCustomer($firstname,$lastname,$primaryMobile,$secondaryMobile,$email,$password) {
	$db = new MySQL();
	
	$query = "SELECT customerId FROM customers WHERE 
	email='".mysqli_real_escape_string($db->mysql_link,$email)."';";
	if(!isValueUnique($query)) {
		sendResponse("Failure","A Customer with this Email already exists.");
	}
	$query = "SELECT customerId FROM customers WHERE 
	mobile='".mysqli_real_escape_string($db->mysql_link,$primaryMobile)."';";
	if(!isValueUnique($query)) {
		sendResponse("Failure","A Customer with this Primary Mobile Number already exists.");
	}
	$query = "INSERT INTO customers(firstname,lastname,primaryMobile,secondaryMobile,email,`password`,dateJoined) VALUES(
	'".mysqli_real_escape_string($db->mysql_link,$firstname)."',
	'".mysqli_real_escape_string($db->mysql_link,$lastname)."',
	'".mysqli_real_escape_string($db->mysql_link,$primaryMobile)."',
	'".mysqli_real_escape_string($db->mysql_link,$secondaryMobile)."',
	'".mysqli_real_escape_string($db->mysql_link,$email)."',
	'".mysqli_real_escape_string($db->mysql_link,$password)."',
	'".time()."');";
	if($db->executeQuery($query)) {
		$messageArray=array();
		$messageArray["customerId"]=$db->getLastInsertID();
		sendResponse("Success",$messageArray);
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function deleteCustomer($customerId) {
	$db = new MySQL();
	$query="DELETE FROM customers 
	WHERE customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."';";
	if($db->executeQuery($query)) {
		sendResponse("Success","Customer Deleted");
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function getDeviceTypes(){
	$db = new MySQL();
	$deviceTypes = $db->executeQueryAndGetArray("SELECT * FROM devicetypes",MYSQLI_ASSOC);
	if($deviceTypes===FALSE) {		
		sendResponse("Failure","No Data Available");
	} else {
		sendResponse("Success",$deviceTypes);
	}
}
function getCustomerDevices($customerId){
	$db = new MySQL();
	$customerDevices = $db->executeQueryAndGetArray("SELECT cd.*,d.serialNumber,dt.deviceTypeId,dt.deviceType FROM customerdevices AS cd, devices As d, devicetypes AS dt WHERE cd.deviceId=d.deviceId AND d.deviceTypeId=dt.deviceTypeId AND cd.customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."'",MYSQLI_ASSOC);
	if($customerDevices===FALSE) {		
		sendResponse("Failure","No Data Available");
	} else {
		sendResponse("Success",$customerDevices);
	}
}
function registerDevice($customerId,$serialNumber,$latitude,$longitude,$simpleName,$mobileNumber){
	if($customerId=='' || $serialNumber=='' || $latitude=='' || $longitude=='' || $simpleName == '' || $mobileNumber == ''){
		sendResponse("Failure","All fields are Compulsory");		
	}
	$db = new MySQL();
	$deviceId=$db->getFirstRowFirstColumn("SELECT deviceId FROM devices WHERE serialNumber='".mysqli_real_escape_string($db->mysql_link,$serialNumber)."'");
	if($deviceId === FALSE){
		sendResponse("Failure","Invalid serial number");		
	}	
	$query = "SELECT customerDeviceId FROM customerdevices WHERE 
		deviceId='".mysqli_real_escape_string($db->mysql_link,$deviceId)."' AND isLinkActive=1;";
		if($db->hasRecords($query)) {
			sendResponse("Failure","This device is already linked to another customer.");
		} else {			
			$query = "INSERT INTO customerdevices(customerId,deviceId,latitude,longitude,simpleName,mobileNumber,dateAdded) VALUES (
			'".mysqli_real_escape_string($this->db->mysql_link,$customerId)."',
			'".mysqli_real_escape_string($this->db->mysql_link,$deviceId)."',
			'".mysqli_real_escape_string($this->db->mysql_link,$latitude)."',
			'".mysqli_real_escape_string($this->db->mysql_link,$longitude)."',
			'".mysqli_real_escape_string($this->db->mysql_link,$simpleName)."',
			'".mysqli_real_escape_string($this->db->mysql_link,$mobileNumber)."',
			'".time()."');";
			if($db->executeQuery($query)) {
				$messageArray=array();
				$messageArray["customerDeviceId"]=$db->getLastInsertID();
				sendResponse("Success",$messageArray);
			} else {
				sendResponse("Failure",$db->getErrorString());				
			}			
		}
}
function saveCustomerFCMToken($customerId,$fcmToken,$deviceID,$deviceOS) {
	$db = new MySQL();
	if($customerId=="" || $fcmToken=="" || $deviceID=="") { 
		sendResponse("Failure","Please provide all parameters");
	} else {
		$query = "SELECT customerId FROM customers WHERE 
		customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."';";
		if(!$db->hasRecords($query)) {
			sendResponse("Failure","Customer not Found.");
		} else {
			$query = "INSERT INTO customerfcmtokens(customerId,fcmToken,deviceID,deviceOS,dateReceived) VALUES(
			'".mysqli_real_escape_string($db->mysql_link,$customerId)."',
			'".mysqli_real_escape_string($db->mysql_link,$fcmToken)."',
			'".mysqli_real_escape_string($db->mysql_link,$deviceID)."',
			'".mysqli_real_escape_string($db->mysql_link,$deviceOS)."',
			'".time()."'
			);";
			if($db->executeQuery($query)) {
				sendResponse("Success","Done Successfully.");
				//return $successResponse($db->getLastInsertID(),'customerFCMTokenId');
			} else {
				sendResponse("Failure",$db->getErrorString());
			}
		}
	}
}
function sendPushNotificationToToken($fcmToken,$messageArray) {	
	//print_r(json_encode($messageArray));
	if(trim($fcmToken)!="") {
		$url = 'https://fcm.googleapis.com/fcm/send';
		$headers = array(
			'Authorization: key='.FIREBASE_API_KEY,
			'Content-Type: application/json'
		);
		$ch = curl_init();					
		curl_setopt($ch, CURLOPT_URL, $url);					
		curl_setopt($ch, CURLOPT_POST, true);					
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);					
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);					
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageArray));
		$result = curl_exec($ch);
		curl_close($ch);
		if ($result === FALSE) {
			$errorString="cURL Failed ";//.curl_error($ch);
			return $errorString;
		} else {
			// $result is a json string
			$resultObject=json_decode($result);
			if (json_last_error() === JSON_ERROR_NONE) {
				if($resultObject->success==1) {
					return true;
				} else {
					//print_r($resultObject);
					$errorString=$resultObject->results[0]->error;
					//print_r($errorString);
					return $errorString;
				}
			}
		}
	} else {
		$errorString="FCM Token Empty";
		return $errorString;
	}
}
function sendPushNotification($fcmToken,$serialNumber,$event,$actionToTake,$key,$value,$dateReceived) {
	$messageArray = array();
	$messageArray['data']['serialNumber'] = $serialNumber;
	$messageArray['data']['eventTimeString'] = $dateReceived;//time();
	$messageArray['data']['event'] = $event;
	$messageArray['data']['action'] = $actionToTake;
	$messageArray['data']['key'] = $key;
	$messageArray['data']['value'] = $value;
	$fields = array(
		'to' => $fcmToken,
		'data' => $messageArray
	);
	
	//firebase server url to send the curl request
	$url = 'https://fcm.googleapis.com/fcm/send';
	//building headers for the request
	$headers = array(
		'Authorization: key='.FIREBASE_API_KEY,
		'Content-Type: application/json'
	);
	//Initializing curl to open a connection
	$ch = curl_init();					
	//Setting the curl url
	curl_setopt($ch, CURLOPT_URL, $url);					
	//setting the method as post
	curl_setopt($ch, CURLOPT_POST, true);					
	//adding headers
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);					
	//disabling ssl support
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);					
	//adding the fields in json format
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));					
	//finally executing the curl request
	$result = curl_exec($ch);
	//Now close the connection
	curl_close($ch);
	return $result;
	if ($result === FALSE) {
		//echo "cURL Failed ";
		return false;
	} else {
		// $result is a json string
		$resultObject=json_decode($result);
		if (json_last_error() === JSON_ERROR_NONE) {
			if($resultObject->success==1) {
				return true;
			} else {
				//echo "Error Occured: ".$resultObject->results->error;
				return false;
			}
		}
	}
}
function logIncomingRequest($serialNumber,$commandToExecute,$parameterString) {
	try {
		$db = new MySQL();		
		$query = "INSERT INTO testlog(serialNumber,commandToExecute,parameterString,dateAdded) VALUES(
			'".mysqli_real_escape_string($db->mysql_link,$serialNumber)."',
			'".mysqli_real_escape_string($db->mysql_link,$commandToExecute)."',
			'".mysqli_real_escape_string($db->mysql_link,$parameterString)."',
			'".date('Y-m-d H:i:s')."');";
		if(!$db->executeQuery($query)) {
			sendResponse("Failure",$db->getErrorString());
		}
	} catch(Exception $e){		
		sendResponse("Failure",$e->getMessage());
		exit();
	}
}
function sendResponse($status,$message){
	$returnArray=array();
	$returnArray["Status"]=$status;
	$returnArray["Message"]=$message;
	echo json_encode($returnArray);
	exit();
}
function sanitizeStringToCompare($sourceString) {
	$finalString="";
	$finalString=str_replace(" ","",$sourceString);
	$finalString=strtolower($finalString);
	return $finalString;
}
function isValueInteger($value) {
    return ctype_digit($value) || is_int($value);
}
function isValueUnique($query) {
	$db = new MySQL();
	if($db->hasRecords($query)) {
		return false;
	}else{
		return true;
	}
}
function isValueExists($query) {
	$db = new MySQL();
	if($db->hasRecords($query)) {
		return true;
	}else{
		return false;
	}
}




