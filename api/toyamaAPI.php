<?php
date_default_timezone_set('Asia/Calcutta');
require "../utilities/class.MySQL.php";
require "../model/class.Hub.php";
require "../model/class.Room.php";
require "../model/class.RoomNode.php";
require "../model/class.NodeSwitch.php";
require "../model/class.Appliance.php";
require "../model/class.Sensor.php";
require "../model/class.Scene.php";
require "../model/class.SceneSensor.php";
require "../model/class.SceneSwitch.php";
require "../model/class.OneTouchController.php";
require "../model/class.OneTouchControllerMapping.php";

//define('FIREBASE_API_KEY', 'AAAAkHwmL5o:APA91bHF9OCdvv1pgvED4N7M3Wl3HOpgIWn5JpwHsgc-WjDLneFDpMLQCKg5fnHXjh43w9gU0BDHa2OSDc6hok9_xPoXOaQgpKmzKm91aWWggVm22f7hpJlxR1Mxo4oGCNwA9lKeo8kwvDMUR_cfndCSA9fd3jTPCw');
define('FIREBASE_API_KEY', 'AAAAbWhrkAI:APA91bFCmqse6aF-XwUrLZv_zqf1PgORsBSl09Um0DNSXCS9RHKOU0I2Ofmu5L9nlome8pgR_RFOoPLx2KKMZJWNODBi4J5pbe4eTkEbRv87_g_KCCKH66uYWJRsY2ZXalWWyfmNbMvA4g2EhekCsiYDc3u5qzMkbA');

$parameterString=file_get_contents("php://input");
$requestParameters=json_decode($parameterString,true);
if (json_last_error() === JSON_ERROR_NONE) {
} else { 
    sendResponse("Failure","Invalid JSON String, ".json_last_error()); 
}
//var_dump($requestParameters,true);
$commandToExecute=$requestParameters['commandToExecute']?$requestParameters['commandToExecute']:"";
$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
$serialNumber=$requestParameters['serialNumber']?$requestParameters['serialNumber']:"";
logIncomingRequest($hubId,$serialNumber,$commandToExecute,$parameterString);
//exit();

// if(empty($hubId) || empty($commandToExecute)) {
// 	sendResponse("Failure","Please provide all parameters");
// }

$errorString="";

switch($commandToExecute) {
	case "customerLogin":
		$email=$requestParameters['email']?$requestParameters['email']:"";
		$password=$requestParameters['password']?$requestParameters['password']:"";
		customerLogin($email,$password);
		break;
	case "getHubs":
		$customerId=$requestParameters['customerId']?$requestParameters['customerId']:"0";
		getHubs($customerId);
		break;
	case "getHubsForVoice":
		$email=$requestParameters['email']?$requestParameters['email']:"0";
		getHubsForVoice($email);
		break;
	case "getHub":
		$serialNumber=$requestParameters['serialNumber']?$requestParameters['serialNumber']:"";
		getHub($serialNumber);
		break;
	case "getNodes":
		getNodes();
		break;
	case "getNodeLoadTypes":
		getNodeLoadTypes();
		break;
	case "getRoomTypes":
		getRoomTypes();
		break;
	case "getSensorTypes":
		getSensorTypes();
		break;
	case "getRooms":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		getRooms($hubId);
		break;
	case "getRoomNodes":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		getRoomNodes($hubId);
		break;
	case "getNodeSwitches":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		getNodeSwitches($hubId);
		break;
	case "getScenes":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		getScenes($hubId);
		break;
	case "toggleSwitchViaVoice":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$roomNameToSearch=$requestParameters['roomNameToSearch']?$requestParameters['roomNameToSearch']:"";
		$switchNameToSearch=$requestParameters['switchNameToSearch']?$requestParameters['switchNameToSearch']:"";
		$percent=$requestParameters['percent']?$requestParameters['percent']:"0";
		toggleSwitchViaVoice($hubId,$roomNameToSearch,$switchNameToSearch,$percent);
		break;
	case "toggleSwitchViaMobile":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$nodeSwitchId=$requestParameters['nodeSwitchId']?$requestParameters['nodeSwitchId']:"";
		$percent=$requestParameters['percent']?$requestParameters['percent']:"0";
		toggleSwitchViaMobile($hubId,$nodeSwitchId,$percent);
		break;
	case "toggleAllSwitches":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$nodeSwitchStates=$requestParameters['nodeSwitchStates']?$requestParameters['nodeSwitchStates']:"";
		$ahuStatuses=$requestParameters['ahuStatuses']?$requestParameters['ahuStatuses']:"";
		toggleAllSwitches($hubId,$nodeSwitchStates,$ahuStatuses);
		break;
	case "toggleSwitches":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$nodeSwitchStates=$requestParameters['nodeSwitchStates']?$requestParameters['nodeSwitchStates']:"";
		toggleSwitches($hubId,$nodeSwitchStates);
		break;
	case "toggleAHUs":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$ahuStatuses=$requestParameters['ahuStatuses']?$requestParameters['ahuStatuses']:"";
		toggleAHUs($hubId,$ahuStatuses);
		break;
	case "applySceneViaVoice":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$sceneNameToApply=$requestParameters['sceneNameToApply']?$requestParameters['sceneNameToApply']:"";
		applySceneViaVoice($hubId,$sceneNameToApply);
		break;
	case "applySceneViaMobile":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$sceneId=$requestParameters['sceneId']?$requestParameters['sceneId']:"0";
		applySceneViaMobile($hubId,$sceneId);
		break;
	case "toggleSensorViaVoice":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$roomNameToSearch=$requestParameters['roomNameToSearch']?$requestParameters['roomNameToSearch']:"";
		$sensorNameToSearch=$requestParameters['sensorNameToSearch']?$requestParameters['sensorNameToSearch']:"";
		$isToArm=$requestParameters['isToArm']?$requestParameters['isToArm']:"0";
		toggleSensorViaVoice($hubId,$roomNameToSearch,$sensorNameToSearch,$isToArm);
		break;
	case "toggleSensorViaMobile":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$sensorId=$requestParameters['sensorId']?$requestParameters['sensorId']:"0";
		$isToArm=$requestParameters['isToArm']?$requestParameters['isToArm']:"0";
		toggleSensorViaMobile($hubId,$sensorId,$isToArm);
		break;
	case "addOrGetCustomerViaGoogle":
		$email=$requestParameters['email']?$requestParameters['email']:"";
		$firstname=$requestParameters['firstname']?$requestParameters['firstname']:"";
		$lastname=$requestParameters['lastname']?$requestParameters['lastname']:"";
		addOrGetCustomerViaGoogle($email,$firstname,$lastname);
		break;
	case "addCustomer":
		$integratorCode=$requestParameters['integratorCode']?$requestParameters['integratorCode']:"1";
		$email=$requestParameters['email']?$requestParameters['email']:"";
		$mobile=$requestParameters['mobile']?$requestParameters['mobile']:"";
		$password=$requestParameters['password']?$requestParameters['password']:"";
		$firstname=$requestParameters['firstname']?$requestParameters['firstname']:"";
		$lastname=$requestParameters['lastname']?$requestParameters['lastname']:"";
		addCustomer($integratorCode,$email,$mobile,$password,$firstname,$lastname);
		break;
	case "deleteCustomer":
		$customerId=$requestParameters['customerId']?$requestParameters['customerId']:"0";
		deleteCustomer($customerId);
		break;
	case "addHub":
		$customerId=$requestParameters['customerId']?$requestParameters['customerId']:"0";
		$serialNumber=$requestParameters['serialNumber']?$requestParameters['serialNumber']:"";
		$simpleName=$requestParameters['simpleName']?$requestParameters['simpleName']:"";
		$baudRate=$requestParameters['baudRate']?$requestParameters['baudRate']:"9600";
		$softwareVersion=$requestParameters['softwareVersion']?$requestParameters['softwareVersion']:"4.0.0";
		$dateTested=time();
		$dateAllocated=time();
		addHub($customerId,$serialNumber,$simpleName,$baudRate,$softwareVersion,$dateTested,
		$dateAllocated);
		break;
	case "addHubViaGoogle":
		$customerId=$requestParameters['customerId']?$requestParameters['customerId']:"0";
		$serialNumber=$requestParameters['serialNumber']?$requestParameters['serialNumber']:"";
		$simpleName=$requestParameters['simpleName']?$requestParameters['simpleName']:"";
		$baudRate=$requestParameters['baudRate']?$requestParameters['baudRate']:"9600";
		$softwareVersion=$requestParameters['softwareVersion']?$requestParameters['softwareVersion']:"4.0.0";
		$integratorCode=$requestParameters['integratorCode']?$requestParameters['integratorCode']:"0";
		$dateTested=time();
		$dateAllocated=time();
		addHubViaGoogle($customerId,$serialNumber,$simpleName,$baudRate,$softwareVersion,
		$integratorCode,$dateTested,$dateAllocated);
		break;
	case "deleteHub":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		deleteHub($hubId);
		break;
	case "saveHubVersion":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$softwareVersion=$requestParameters['softwareVersion']?$requestParameters['softwareVersion']:"";
		saveHubVersion($hubId,$softwareVersion);
		break;
	case "addRoom":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$roomName=$requestParameters['roomName']?$requestParameters['roomName']:"";
		$roomTypeId=$requestParameters['roomTypeId']?$requestParameters['roomTypeId']:"0";
		addRoom($hubId,$roomName,$roomTypeId);
		break;
	case "deleteRoom":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$roomId=$requestParameters['roomId']?$requestParameters['roomId']:"0";
		deleteRoom($hubId,$roomId);
		break;
	case "addRoomNode":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$roomId=$requestParameters['roomId']?$requestParameters['roomId']:"0";
		$roomNodeName=$requestParameters['roomNodeName']?$requestParameters['roomNodeName']:"";
		$nodeId=$requestParameters['nodeId']?$requestParameters['nodeId']:"0";
		$version=$requestParameters['version']?$requestParameters['version']:"4";
		$macIDLow=$requestParameters['macIDLow']?$requestParameters['macIDLow']:"0";
		$macIDHigh=$requestParameters['macIDHigh']?$requestParameters['macIDHigh']:"0";
		if($macIDHigh==0 || $macIDLow==0) {
			sendResponse("Failure","Invalid Mac ID.");
		}
		addRoomNode($hubId,$roomId,$roomNodeName,$nodeId,$version,$macIDHigh,$macIDLow);
		break;
	case "deleteRoomNode":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$roomNodeId=$requestParameters['roomNodeId']?$requestParameters['roomNodeId']:"0";
		deleteRoomNode($hubId,$roomNodeId);
		break;
	case "saveNodeIDForRoomNode":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$roomNodeId=$requestParameters['roomNodeId']?$requestParameters['roomNodeId']:"0";
		$nodeIDHigh=$requestParameters['nodeIDHigh']?$requestParameters['nodeIDHigh']:"0";
		$nodeIDLow=$requestParameters['nodeIDLow']?$requestParameters['nodeIDLow']:"0";
		saveNodeIDForRoomNode($hubId,$roomNodeId,$nodeIDHigh,$nodeIDLow);
		break;
	case "addSensor":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$roomId=$requestParameters['roomId']?$requestParameters['roomId']:"0";
		$sensorTypeId=$requestParameters['sensorTypeId']?$requestParameters['sensorTypeId']:"0";
		$sensorName=$requestParameters['sensorName']?$requestParameters['sensorName']:"";
		$version=$requestParameters['version']?$requestParameters['version']:"4";
		$macIDLow=$requestParameters['macIDLow']?$requestParameters['macIDLow']:"0";
		$macIDHigh=$requestParameters['macIDHigh']?$requestParameters['macIDHigh']:"0";
		addSensor($hubId,$roomId,$sensorTypeId,$sensorName,$version,$macIDHigh,$macIDLow);
		break;
	case "deleteSensor":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$sensorId=$requestParameters['sensorId']?$requestParameters['sensorId']:"0";
		deleteSensor($hubId,$sensorId);
		break;
	case "saveNodeIDForSensor":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$sensorId=$requestParameters['sensorId']?$requestParameters['sensorId']:"0";
		$nodeIDHigh=$requestParameters['nodeIDHigh']?$requestParameters['nodeIDHigh']:"0";
		$nodeIDLow=$requestParameters['nodeIDLow']?$requestParameters['nodeIDLow']:"0";
		saveNodeIDForSensor($hubId,$sensorId,$nodeIDHigh,$nodeIDLow);
		break;
	case "addOneTouchController":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$simpleName=$requestParameters['simpleName']?$requestParameters['simpleName']:"";
		$version=$requestParameters['version']?$requestParameters['version']:"4";
		$macIDLow=$requestParameters['macIDLow']?$requestParameters['macIDLow']:"0";
		$macIDHigh=$requestParameters['macIDHigh']?$requestParameters['macIDHigh']:"0";
		addOneTouchController($hubId,$simpleName,$version,$macIDHigh,$macIDLow);
		break;
	case "deleteOneTouchController":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$oneTouchControllerId=$requestParameters['oneTouchControllerId']?$requestParameters['oneTouchControllerId']:"0";
		deleteOneTouchController($hubId,$oneTouchControllerId);
		break;
	case "saveNodeIDForOTC":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$oneTouchControllerId=$requestParameters['oneTouchControllerId']?$requestParameters['oneTouchControllerId']:"0";
		$nodeIDHigh=$requestParameters['nodeIDHigh']?$requestParameters['nodeIDHigh']:"0";
		$nodeIDLow=$requestParameters['nodeIDLow']?$requestParameters['nodeIDLow']:"0";
		saveNodeIDForOTC($hubId,$oneTouchControllerId,$nodeIDHigh,$nodeIDLow);
		break;
	case "addOTCMapping":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$oneTouchControllerId=$requestParameters['oneTouchControllerId']?$requestParameters['oneTouchControllerId']:"0";
		$switchNumber=$requestParameters['switchNumber']?$requestParameters['switchNumber']:"0";
		$percent=$requestParameters['percent']?$requestParameters['percent']:"0";
		$key=$requestParameters['key']?$requestParameters['key']:"";
		$value=$requestParameters['value']?$requestParameters['value']:"";		
		addOTCMapping($hubId,$oneTouchControllerId,$switchNumber,$percent,$key,$value);
		break;
	case "addOTCMappings":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$oneTouchControllerId=$requestParameters['oneTouchControllerId']?$requestParameters['oneTouchControllerId']:"0";
		$otcMappings=$requestParameters['otcMappings']?$requestParameters['otcMappings']:"";
		addOTCMappings($hubId,$oneTouchControllerId,$otcMappings);
		break;
	case "deleteOTCMapping":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$mappingId=$requestParameters['mappingId']?$requestParameters['mappingId']:"0";
		deleteOTCMapping($hubId,$mappingId);
		break;
	case "addAppliance":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$roomId=$requestParameters['roomId']?$requestParameters['roomId']:"0";
		$nodeSwitchLoadTypeId=$requestParameters['nodeSwitchLoadTypeId']?$requestParameters['nodeSwitchLoadTypeId']:"0";
		$simpleName=$requestParameters['simpleName']?$requestParameters['simpleName']:"";
		$nodeSwitchId=$requestParameters['nodeSwitchId']?$requestParameters['nodeSwitchId']:"0";
		$isIREnabled=$requestParameters['isIREnabled']?$requestParameters['isIREnabled']:"0";
		$customValue=$requestParameters['customValue']?$requestParameters['customValue']:"";
		addAppliance($hubId,$roomId,$nodeSwitchLoadTypeId,$simpleName,$nodeSwitchId,$isIREnabled,$customValue);
		break;
	case "deleteAppliance":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}	
		$applianceId=$requestParameters['applianceId']?$requestParameters['applianceId']:"0";
		deleteAppliance($hubId,$applianceId);
		break;
	case "saveScene":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		$sceneId=$requestParameters['sceneId']?$requestParameters['sceneId']:"0";
		$sceneName=$requestParameters['sceneName']?$requestParameters['sceneName']:"";
		$sceneSwitches=$requestParameters['sceneSwitches']?$requestParameters['sceneSwitches']:"";
		$sceneSensors=$requestParameters['sceneSensors']?$requestParameters['sceneSensors']:"";
		saveScene($hubId,$sceneId,$sceneName,$sceneSwitches,$sceneSensors);
		break;
	case "deleteScene":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}	
		$sceneId=$requestParameters['sceneId']?$requestParameters['sceneId']:"0";
		deleteScene($hubId,$sceneId);
		break;
	case "sceneApplied":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}	
		$sceneId=$requestParameters['sceneId']?$requestParameters['sceneId']:"0";
		sceneApplied($hubId,$sceneId);
		break;
	case "saveHubFCMToken":
		$serialNumber=$requestParameters['serialNumber']?$requestParameters['serialNumber']:"";
		if($serialNumber=="") {
			sendResponse("Failure","Invalid Hub");
		}
		$fcmToken=$requestParameters['fcmToken']?$requestParameters['fcmToken']:"";
		$deviceID=$requestParameters['deviceID']?$requestParameters['deviceID']:"";
		$deviceOS=$requestParameters['deviceOS']?$requestParameters['deviceOS']:"";
		saveHubFCMToken($serialNumber,$fcmToken,$deviceID,$deviceOS);
		break;
	case "saveCustomerFCMToken":
		$customerId=$requestParameters['customerId']?$requestParameters['customerId']:"0";
		$fcmToken=$requestParameters['fcmToken']?$requestParameters['fcmToken']:"";
		$deviceID=$requestParameters['deviceID']?$requestParameters['deviceID']:"";
		$deviceOS=$requestParameters['deviceOS']?$requestParameters['deviceOS']:"";
		saveCustomerFCMToken($customerId,$fcmToken,$deviceID,$deviceOS);
		break;
	case "sendHubErrorToCustomers":
		$serialNumber=$requestParameters['serialNumber']?$requestParameters['serialNumber']:"";
		if($serialNumber=="") {
			sendResponse("Failure","Invalid Hub");
		}
		$errorMessageToLog=$requestParameters['errorMessageToLog']?
			$requestParameters['errorMessageToLog']:"";
		$errorLevel=$requestParameters['errorLevel']?$requestParameters['errorLevel']:"";
		$errorMessage=$requestParameters['errorMessage']?$requestParameters['errorMessage']:"";
		sendHubErrorToCustomers($serialNumber,$errorLevel,$errorMessage);
		break;
	case "checkHubFCMTokenValidity":
		$hubId=$requestParameters['hubId']?$requestParameters['hubId']:"0";
		if($hubId==0) {
			sendResponse("Failure","Invalid Hub");
		}
		sendFCMTokenRegisteredCommandToHub($hubId);
		break;
	case "testJSONInput":
		$jsonString=$requestParameters['jsonString']?$requestParameters['jsonString']:"";
		testJSONInput($jsonString);
		break;
	default:
		sendResponse("Failure","This command is not enabled yet.");
		break;
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
function getHubs($customerId) {
	$db = new MySQL();
	$query = "SELECT customerId FROM customers WHERE 
	customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."';";
	$customer = $db->executeQueryAndGetFirstRow($query);
	if($customer===FALSE) {
		sendResponse("Failure","Invalid Customer.");
	} else {
		$query = "SELECT * FROM hubs 
		WHERE customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."'";
		$dbhubs = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
		$messageArray=array();
		$hubs=[];
		if($dbhubs===FALSE) { } 
		else {
			for($i=0;$i<sizeof($dbhubs);$i++) {
				$dbg=$dbhubs[$i];
				$g=new Hub($dbg['hubId'],$dbg['serialNumber'],$dbg['simpleName'],$dbg['baudRate'],$dbg['softwareVersion']);
				$g->rooms=getRoomsInHub($dbg['hubId']);
				$g->scenes=getScenesInHub($dbg['hubId']);
				$g->otcs=getOTCsInHub($dbg['hubId']);
				$hubs[]=$g;
			}
		}
		$messageArray["hubs"]=$hubs;
		sendResponse("Success",$messageArray);
	}
}
function getHubsViaEmail($email) {
	$db = new MySQL();
	$query = "SELECT customerId FROM customers WHERE 
	email='".mysqli_real_escape_string($db->mysql_link,$email)."';";
	$customer = $db->executeQueryAndGetFirstRow($query);
	if($customer===FALSE) {
		sendResponse("Failure","Invalid Customer.");
	} else {
		$customerId=$customer->customerId;
		$query = "SELECT * FROM hubs 
		WHERE customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."'";
		$dbhubs = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
		$messageArray=array();
		$hubs=[];
		if($dbhubs===FALSE) { } 
		else {
			for($i=0;$i<sizeof($dbhubs);$i++) {
				$dbg=$dbhubs[$i];
				$g=new Hub($dbg['hubId'],$dbg['serialNumber'],$dbg['simpleName'],$dbg['baudRate'],$dbg['softwareVersion']);
				$g->rooms=getRoomsInHub($dbg['hubId']);
				$g->scenes=getScenesInHub($dbg['hubId']);
				$g->otcs=getOTCsInHub($dbg['hubId']);
				$hubs[]=$g;
			}
		}
		$messageArray["hubs"]=$hubs;
		sendResponse("Success",$messageArray);
	}
}
function getHubsForVoice($email) {
	$db = new MySQL();
	$query = "SELECT customerId,firstname,lastname FROM customers WHERE 
	email='".mysqli_real_escape_string($db->mysql_link,$email)."';";
	$customer = $db->executeQueryAndGetFirstRow($query);
	if($customer===FALSE) {
		sendResponse("Failure","Invalid Customer.");
	} else {
		$customerId=$customer->customerId;
		$query = "SELECT * FROM hubs 
		WHERE customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."'";
		$dbhubs = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
		$messageArray=array();
		$hubs=[];
		if($dbhubs===FALSE) { } 
		else {
			for($i=0;$i<sizeof($dbhubs);$i++) {
				$dbg=$dbhubs[$i];
				$g=new Hub($dbg['hubId'],$dbg['serialNumber'],$dbg['simpleName'],$dbg['baudRate'],
				$dbg['softwareVersion']);
				$hubs[]=$g;
			}
		}
		$messageArray["firstname"]=$customer->firstname;
		$messageArray["hubs"]=$hubs;
		sendResponse("Success",$messageArray);
	}
}
function getHub($serialNumber) {
	$db = new MySQL();
	$query = "SELECT * FROM hubs WHERE 
	serialNumber='".mysqli_real_escape_string($db->mysql_link,$serialNumber)."';";
	$dbh = $db->executeQueryAndGetFirstRow($query);
	if($dbh===FALSE) {
		sendResponse("Failure","Hub with this Serial Number is not found.");
	} else {
		$hub=new Hub($dbh->hubId,$dbh->serialNumber,$dbh->simpleName,
		$dbh->baudRate,$dbh->softwareVersion);
		$hub->rooms=getRoomsInHub($dbh->hubId);
		$hub->scenes=getScenesInHub($dbh->hubId);
		$hub->otcs=getOTCsInHub($dbh->hubId);
		sendResponse("Success",$hub);
	}
}
function getNodes() {
	$db = new MySQL();
	$query = "SELECT * FROM nodes";
	$nodes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	if($nodes===FALSE) {
		sendResponse("Failure","No Data Available");
	} else {
		sendResponse("Success",$nodes);
	}
}
function getNodeLoadTypes() {
	$db = new MySQL();
	$query = "SELECT * FROM nodeswitchloadtypes";
	$nodeswitchloadtypes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	if($nodeswitchloadtypes===FALSE) {
		sendResponse("Failure","No Data Available");
	} else {
		sendResponse("Success",$nodeswitchloadtypes);
	}
}
function getRoomTypes() {
	$db = new MySQL();
	$query = "SELECT * FROM roomtypes";
	$roomtypes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	if($roomtypes===FALSE) {
		sendResponse("Failure","No Data Available");
	} else {
		sendResponse("Success",$roomtypes);
	}
}
function getSensorTypes() {
	$db = new MySQL();
	$query = "SELECT * FROM sensortypes";
	$sensortypes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	if($sensortypes===FALSE) {
		sendResponse("Failure","No Data Available");
	} else {
		sendResponse("Success",$sensortypes);
	}
}
function getRoomsInHub($hubId) {
	$db = new MySQL();
	$query = "SELECT * FROM rooms 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
	$dbrooms = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	$rooms=[];
	if($dbrooms===FALSE) {
		$rooms=[];
	} else {
		for($i=0;$i<sizeof($dbrooms);$i++) {
			$dbr=$dbrooms[$i];
			// getting node details
			$query = "SELECT roomType FROM roomtypes WHERE 
			roomTypeId='".mysqli_real_escape_string($db->mysql_link,$dbr['roomTypeId'])."';";
			$node = $db->executeQueryAndGetFirstRow($query);
			$rt="";
			if($node===FALSE) {	} 
			else {
				$rt=$node->roomType;
			}
			$r=new Room($dbr['roomId'],$dbr['roomName'],$rt);
			$r->roomNodes=getRoomNodesInRoom($dbr['roomId']);
			$r->sensors=getSensorsInRoom($dbr['roomId']);
			$r->appliances=getAppliancesInRoom($dbr['roomId']);
			$rooms[]=$r;
		}
	}
	return $rooms;
}
function getRooms($hubId) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	} else {
		$query = "SELECT * FROM rooms 
		WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
		$rooms = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
		$messageArray=array();
		if($rooms===FALSE) {
			$messageArray["rooms"]=[];
		} else {
			$messageArray["rooms"]=$rooms;
		}
		sendResponse("Success",$messageArray);
	}
}
function getRoomNodesInRoom($roomId) {
	$db = new MySQL();
	$query = "SELECT * FROM roomnodes 
	WHERE roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."'";
	$dbroomnodes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	$roomnodes=[];
	if($dbroomnodes===FALSE) {
		$roomnodes=[];
	} else {
		for($i=0;$i<sizeof($dbroomnodes);$i++) {
			$dbrn=$dbroomnodes[$i];
			// getting node details
			$query = "SELECT nodeName,nodeType FROM nodes WHERE 
			nodeId='".mysqli_real_escape_string($db->mysql_link,$dbrn['nodeId'])."';";
			$node = $db->executeQueryAndGetFirstRow($query);
			$nn="";
			$nt="";
			if($node===FALSE) {	} 
			else {
				$nn=$node->nodeName;
				$nt=$node->nodeType;
			}
			$rn=new RoomNode($dbrn['roomNodeId'],$dbrn['roomNodeName'],$nn,$nt,$dbrn['version'],$dbrn['macIDLow'],$dbrn['macIDHigh'],$dbrn['nodeIDLow'],$dbrn['nodeIDHigh']);
			$rn->nodeSwitches=getNodeSwitchesInRoomNode($dbrn['roomNodeId']);
			$roomnodes[]=$rn;
		}
	}
	return $roomnodes;
}
function getRoomNodes($hubId) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	} else {
		$query = "SELECT * FROM roomnodes 
		WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
		$roomnodes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
		if($roomnodes===FALSE) {
			sendResponse("Failure","No Data Available");
		} else {
			sendResponse("Success",$roomnodes);
		}
		$query = "SELECT * FROM rooms 
		WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
		$rooms = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
		$messageArray=array();
		if($rooms===FALSE) {
			$messageArray["rooms"]=[];
		} else {
			$messageArray["rooms"]=$rooms;
		}
		sendResponse("Success",$messageArray);
	}
}
function getSensorsInRoom($roomId) {
	$db = new MySQL();
	$query = "SELECT * FROM sensors 
	WHERE roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."'";
	$dbsensors = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	$sensors=[];
	if($dbsensors===FALSE) {
		$sensors=[];
	} else {
		for($i=0;$i<sizeof($dbsensors);$i++) {
			$dbrn=$dbsensors[$i];
			$query = "SELECT sensorType FROM sensortypes WHERE 
			sensorTypeId='".mysqli_real_escape_string($db->mysql_link,$dbrn['sensorTypeId'])."';";
			$node = $db->executeQueryAndGetFirstRow($query);
			$rt="";
			if($node===FALSE) {	} 
			else {
				$rt=$node->sensorType;
			}
			$rn=new Sensor($dbrn['sensorId'],$dbrn['sensorName'],$dbrn['version'],$rt,$dbrn['macIDHigh'],$dbrn['macIDLow'],$dbrn['nodeIDHigh'],$dbrn['nodeIDLow'],$dbrn['isArmed']);
			$sensors[]=$rn;
		}
	}
	return $sensors;
}
function getAppliancesInRoom($roomId) {
	$db = new MySQL();
	$query = "SELECT * FROM appliances 
	WHERE roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."'";
	$dbappliances = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	$appliances=[];
	if($dbappliances===FALSE) {
		$appliances=[];
	} else {
		for($i=0;$i<sizeof($dbappliances);$i++) {
			$dbrn=$dbappliances[$i];
			// getting load type details
			$query = "SELECT loadType FROM nodeswitchloadtypes WHERE 
			nodeSwitchLoadTypeId='".mysqli_real_escape_string($db->mysql_link,$dbrn['nodeSwitchLoadTypeId'])."';";
			$node = $db->executeQueryAndGetFirstRow($query);
			$nt="";
			if($node===FALSE) {	} 
			else {
				$nt=$node->loadType;
			}
			$rn=new Appliance($dbrn['applianceId'],$dbrn['simpleName'],$nt,$dbrn['isIREnabled'],$dbrn['nodeSwitchId'],$dbrn['customValue']);
			//($dbrn['roomNodeId'],$dbrn['roomNodeName'],$nn,$nt,$dbrn['version'],$dbrn['macIDLow'],$dbrn['macIDHigh']);
			$rn->nodeSwitches=getNodeSwitchesInRoomNode($dbrn['roomNodeId']);
			$appliances[]=$rn;
		}
	}
	return $appliances;
}
function getNodeSwitchesInRoomNode($roomNodeId) {
	$db = new MySQL();
	$query = "SELECT * FROM nodeswitches 
	WHERE roomNodeId='".mysqli_real_escape_string($db->mysql_link,$roomNodeId)."'";
	$dbnodeswitches = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	$nodeswitches=[];
	if($dbnodeswitches===FALSE) {
		$nodeswitches=[];
	} else {
		for($i=0;$i<sizeof($dbnodeswitches);$i++) {
			$dbns=$dbnodeswitches[$i];
			$ns=new NodeSwitch();
			$ns->assignProperties($dbns['nodeSwitchId'],$dbns['switchName'],$dbns['type'],$dbns['category'],$dbns['switchNumber'],$dbns['customValue'],$dbns['percent']);
			$nodeswitches[]=$ns;
		}
	}
	return $nodeswitches;
}
function getNodeSwitches($hubId) {
	$db = new MySQL();
	$query = "SELECT * FROM nodeswitches WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
	$nodeswitches = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	if($nodeswitches===FALSE) {
		sendResponse("Failure","No Data Available");
	} else {
		sendResponse("Success",$nodeswitches);
	}
}
function getScenesInHub($hubId) {
	$db = new MySQL();
	$query = "SELECT * FROM scenes 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
	$dbscenes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	$scenes=[];
	if($dbscenes===FALSE) {
		$scenes=[];
	} else {
		for($i=0;$i<sizeof($dbscenes);$i++) {
			$dbs=$dbscenes[$i];
			$s=new Scene($dbs['sceneId'],$dbs['sceneName']);
			$s->sceneSwitches=getSceneSwitchesInScene($dbs['sceneId']);
			$s->sceneSensors=getSceneSensorsInScene($dbs['sceneId']);
			$scenes[]=$s;
		}
	}
	return $scenes;
}
function getScenes($hubId) {
	$db = new MySQL();
	$query = "SELECT * FROM scenes 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
	$scenes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	if($scenes===FALSE) {
		sendResponse("Failure","No Data Available");
	} else {
		sendResponse("Success",$scenes);
	}
}
function getSceneSwitchesInScene($sceneId) {
	$db = new MySQL();
	$query = "SELECT * FROM sceneswitches 
	WHERE sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."'";
	$dbsceneswitches = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	$sceneswitches=[];
	if($dbsceneswitches===FALSE) {
		$sceneswitches=[];
	} else {
		for($i=0;$i<sizeof($dbsceneswitches);$i++) {
			$dbss=$dbsceneswitches[$i];
			$ss=new SceneSwitch($dbss['sceneSwitchId'],$dbss['nodeSwitchId'],$dbss['percent']);
			$sceneswitches[]=$ss;
		}
	}
	return $sceneswitches;
}
function getSceneSensorsInScene($sceneId) {
	$db = new MySQL();
	$query = "SELECT * FROM scenesensors 
	WHERE sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."'";
	$dbscenesensors = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	$scenesensors=[];
	if($dbscenesensors===FALSE) {
		$scenesensors=[];
	} else {
		for($i=0;$i<sizeof($dbscenesensors);$i++) {
			$dbss=$dbscenesensors[$i];
			$ss=new SceneSensor($dbss['sceneSensorId'],$dbss['sensorId'],$dbss['isToArm']);
			$scenesensors[]=$ss;
		}
	}
	return $scenesensors;
}
function getOTCsInHub($hubId) {
	$db = new MySQL();
	$query = "SELECT * FROM onetouchcontrollers 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
	$dbroomnodes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	$roomnodes=[];
	if($dbroomnodes===FALSE) {
		$roomnodes=[];
	} else {
		for($i=0;$i<sizeof($dbroomnodes);$i++) {
			$dbrn=$dbroomnodes[$i];
			$rn=new OneTouchController($dbrn['oneTouchControllerId'],$dbrn['simpleName'],$dbrn['version'],$dbrn['macIDLow'],$dbrn['macIDHigh'],$dbrn['nodeIDLow'],$dbrn['nodeIDHigh']);
			$rn->mappings=getMappingsInOTC($dbrn['oneTouchControllerId']);
			$roomnodes[]=$rn;
		}
	}
	return $roomnodes;
}
function getMappingsInOTC($oneTouchControllerId) {
	$db = new MySQL();
	$query = "SELECT * FROM onetouchcontrollermappings 
	WHERE oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."'";
	$dbnodeswitches = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	$nodeswitches=[];
	if($dbnodeswitches===FALSE) {
		$nodeswitches=[];
	} else {
		for($i=0;$i<sizeof($dbnodeswitches);$i++) {
			$dbns=$dbnodeswitches[$i];
			$ns=new OneTouchControllerMapping($dbns['mappingId'],$dbns['switchNumber'],$dbns['percent'],$dbns['key'],$dbns['value']);
			$nodeswitches[]=$ns;
		}
	}
	return $nodeswitches;
}
function addOrGetCustomerViaGoogle($email,$firstname,$lastname) {
	$db = new MySQL();
	$query = "SELECT customerId FROM customers WHERE 
	email='".mysqli_real_escape_string($db->mysql_link,$email)."';";
	$customer = $db->executeQueryAndGetFirstRow($query);
	if($customer===FALSE) {
		$query = "INSERT INTO customers(email,firstname,lastname,dateAdded) VALUES(
			'".mysqli_real_escape_string($db->mysql_link,$email)."',
			'".mysqli_real_escape_string($db->mysql_link,$firstname)."',
			'".mysqli_real_escape_string($db->mysql_link,$lastname)."',
			'".time()."');";
			if($db->executeQuery($query)) {
				$messageArray=array();
				$messageArray["customerId"]=$db->getLastInsertID();
				sendResponse("Success",$messageArray);
			} else {
				sendResponse("Failure",$db->getErrorString());
			}
	} else {
		$customerId=$customer->customerId;
		$messageArray=array();
		$messageArray["customerId"]=$customerId;
		sendResponse("Success",$messageArray);
	}
}
function addCustomer($integratorCode,$email,$mobile,$password,$firstname,$lastname) {
	$db = new MySQL();
	$query = "SELECT integratorId FROM integrators WHERE 
	integratorCode='".mysqli_real_escape_string($db->mysql_link,$integratorCode)."';";
	$integrator = $db->executeQueryAndGetFirstRow($query);
	if($integrator===FALSE) {
		sendResponse("Failure","Invalid Integrator Code.");
	} else {
		$integratorId=$integrator->integratorId;
	}
	$query = "SELECT customerId FROM customers WHERE 
	email='".mysqli_real_escape_string($db->mysql_link,$email)."';";
	if(!isValueUnique($query)) {
		sendResponse("Failure","A Customer with this Email already exists.");
	}
	$query = "SELECT customerId FROM customers WHERE 
	mobile='".mysqli_real_escape_string($db->mysql_link,$mobile)."';";
	if(!isValueUnique($query)) {
		sendResponse("Failure","A Customer with this Mobile Number already exists.");
	}
	$query = "INSERT INTO customers(integratorId,email,mobile,`password`,firstname,lastname,dateAdded) VALUES(
	'".mysqli_real_escape_string($db->mysql_link,$integratorId)."',
	'".mysqli_real_escape_string($db->mysql_link,$email)."',
	'".mysqli_real_escape_string($db->mysql_link,$mobile)."',
	'".mysqli_real_escape_string($db->mysql_link,$password)."',
	'".mysqli_real_escape_string($db->mysql_link,$firstname)."',
	'".mysqli_real_escape_string($db->mysql_link,$lastname)."',
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
function addHub($customerId,$serialNumber,$simpleName,$baudRate,$softwareVersion,$dateTested,
	$dateAllocated) {
	$db = new MySQL();
	$query = "SELECT customerId FROM customers WHERE 
	customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."';";
	$customer = $db->executeQueryAndGetFirstRow($query);
	if($customer===FALSE) {
		sendResponse("Failure","Invalid Customer.");
	}
	$query = "SELECT hubId FROM hubs WHERE 
	serialNumber='".mysqli_real_escape_string($db->mysql_link,$serialNumber)."';";
	if(!isValueUnique($query)) {
		sendResponse("Failure","A Hub with this Serial Number already exists.");
	}
	$query = "SELECT hubId FROM hubs WHERE 
	simpleName='".mysqli_real_escape_string($db->mysql_link,$simpleName)."' AND 
	customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."';";
	if(!isValueUnique($query)) {
		sendResponse("Failure","A Hub with this Name already exists.");
	}
	$query = "INSERT INTO hubs(customerId,serialNumber,simpleName,baudRate,softwareVersion,dateTested,dateAllocated,dateAdded) VALUES(
	'".mysqli_real_escape_string($db->mysql_link,$customerId)."',
	'".mysqli_real_escape_string($db->mysql_link,$serialNumber)."',
	'".mysqli_real_escape_string($db->mysql_link,$simpleName)."',
	'".mysqli_real_escape_string($db->mysql_link,$baudRate)."',
	'".mysqli_real_escape_string($db->mysql_link,$softwareVersion)."',
	'".mysqli_real_escape_string($db->mysql_link,$dateTested)."',
	'".mysqli_real_escape_string($db->mysql_link,$dateAllocated)."',
	'".time()."');";
	if($db->executeQuery($query)) {
		$messageArray=array();
		$messageArray["hubId"]=$db->getLastInsertID();
		sendResponse("Success",$messageArray);
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function addHubViaGoogle($customerId,$serialNumber,$simpleName,$baudRate,$softwareVersion,
	$integratorCode,$dateTested,$dateAllocated) {
	$db = new MySQL();
	$query = "SELECT customerId FROM customers WHERE 
	customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."';";
	$customer = $db->executeQueryAndGetFirstRow($query);
	if($customer===FALSE) {
		sendResponse("Failure","Invalid Customer.");
	}
	$query = "SELECT integratorId FROM integrators WHERE 
	integratorCode='".mysqli_real_escape_string($db->mysql_link,$integratorCode)."';";
	$integrator = $db->executeQueryAndGetFirstRow($query);
	if($integrator===FALSE) {
		sendResponse("Failure","Invalid Integrator Code.");
	} else {
		$integratorId=$integrator->integratorId;
	}
	$query = "SELECT hubId FROM hubs WHERE 
	serialNumber='".mysqli_real_escape_string($db->mysql_link,$serialNumber)."';";
	if(!isValueUnique($query)) {
		sendResponse("Failure","A Hub with this Serial Number already exists.");
	}
	$query = "SELECT hubId FROM hubs WHERE 
	simpleName='".mysqli_real_escape_string($db->mysql_link,$simpleName)."' AND 
	customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."';";
	if(!isValueUnique($query)) {
		sendResponse("Failure","A Hub with this Name already exists.");
	}
	$query = "INSERT INTO hubs(customerId,serialNumber,simpleName,baudRate,softwareVersion,
		integratorId,dateTested,dateAllocated,dateAdded) VALUES(
	'".mysqli_real_escape_string($db->mysql_link,$customerId)."',
	'".mysqli_real_escape_string($db->mysql_link,$serialNumber)."',
	'".mysqli_real_escape_string($db->mysql_link,$simpleName)."',
	'".mysqli_real_escape_string($db->mysql_link,$baudRate)."',
	'".mysqli_real_escape_string($db->mysql_link,$softwareVersion)."',
	'".mysqli_real_escape_string($db->mysql_link,$integratorId)."',
	'".mysqli_real_escape_string($db->mysql_link,$dateTested)."',
	'".mysqli_real_escape_string($db->mysql_link,$dateAllocated)."',
	'".time()."');";
	if($db->executeQuery($query)) {
		$messageArray=array();
		$messageArray["hubId"]=$db->getLastInsertID();
		sendResponse("Success",$messageArray);
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function deleteHub($hubId) {
	$db = new MySQL();
	// write a transaction and query like below
	//$query="DELETE FROM nodeswitches 
	//WHERE roomNodeId='".mysqli_real_escape_string($db->mysql_link,$roomNodeId)."';";
	//if($db->executeQuery($query)) {
	//}
	$query="DELETE FROM hubs 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	if($db->executeQuery($query)) {
		sendResponse("Success","Hub Deleted");
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function saveHubVersion($hubId,$softwareVersion) {
	$db = new MySQL();
	$query = "SELECT hubId,simpleName FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	$query = "SELECT customerId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$customerId=$db->getFirstRowFirstColumn($query);
	if($customerId=='' || $customerId===FALSE) {
		$errorString = "Invalid Customer.";
	}
	$query="SELECT customerFCMTokenId,fcmToken,deviceOS FROM customerfcmtokens 
	WHERE customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."' 
	ORDER BY dateReceived DESC LIMIT 1";
	$fcmObject=$db->executeQueryAndGetFirstRow($query);
	if($fcmObject===FALSE) {
		$errorString .= " Customer is not enabled for Remote Operations";
	} else {
		if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
			$errorString .= " ".$db->getErrorString();
		}
		
		$fcmToken = $fcmObject->fcmToken;
		$deviceOS = $fcmObject->deviceOS;

		$notificationArray = array(
			"title" => $hub->simpleName." is now running.",
			"body" => "Your Hub with name: ".$hub->simpleName." is up and running."
		);
		$dataArray = array(
			'remoteCommand' => "hubRunning",
			'hubId' => $hubId,
			'simpleName' => $hub->simpleName
		);
		$messageArray = array(
			'to' => $fcmToken,
			'notification' => $notificationArray,			
			'data' => $dataArray,
			'priority' => "high"
		);
		$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
		if($sendNotificationResult===true) {
			$errorString = "Notified Mobile App.";
		} else {
			$errorString.= " ".$sendNotificationResult;
		}
	}
	$query="UPDATE hubs SET softwareVersion='".$softwareVersion."' 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	if($db->executeQuery($query)) {
		sendResponse("Success","Hub Version Saved. ".$errorString);	
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function addRoom($hubId,$roomName,$roomTypeId) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	$query = "SELECT roomId FROM rooms WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."' AND 
	roomName='".mysqli_real_escape_string($db->mysql_link,$roomName)."';";
	if(!isValueExists($query)) {
		$query = "INSERT INTO rooms(hubId,roomName,roomTypeId,dateAdded) VALUES(
		'".mysqli_real_escape_string($db->mysql_link,$hubId)."',
		'".mysqli_real_escape_string($db->mysql_link,$roomName)."',
		'".mysqli_real_escape_string($db->mysql_link,$roomTypeId)."',
		'".time()."');";
		if($db->executeQuery($query)) {
			$messageArray=array();
			$messageArray["roomId"]=$db->getLastInsertID();
			sendRefreshDataCommandToHub($hubId);
			sendResponse("Success",$messageArray);
		} else {
			sendResponse("Failure",$db->getErrorString());
		}
	} else {
		sendResponse("Failure","A Room with this name already exists on this hub.");
	}
}
function deleteRoom($hubId,$roomId) {
	$db = new MySQL();
	$query="DELETE FROM rooms 
	WHERE roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."';";
	if($db->executeQuery($query)) {
		sendRefreshDataCommandToHub($hubId);
		sendResponse("Success","Room Deleted");
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function addRoomNode($hubId,$roomId,$roomNodeName,$nodeId,$version,$macIDHigh,$macIDLow) {
	$db = new MySQL();
	if($version==0) {
		sendResponse("Failure","Invalid Switch Version.");
	}
	if($nodeId==0) {
		sendResponse("Failure","Invalid Node Type.");
	}
	$query = "SELECT roomId FROM rooms WHERE 
	roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."';";
	$room = $db->executeQueryAndGetFirstRow($query);
	if($room===FALSE) {
		sendResponse("Failure","Invalid Room.");
	}
	$query = "INSERT INTO roomnodes(roomId,roomNodeName,nodeId,`version`,macIDLow,macIDHigh,dateAdded) VALUES(
	'".mysqli_real_escape_string($db->mysql_link,$roomId)."',
	'".mysqli_real_escape_string($db->mysql_link,$roomNodeName)."',
	'".mysqli_real_escape_string($db->mysql_link,$nodeId)."',
	'".mysqli_real_escape_string($db->mysql_link,$version)."',
	'".mysqli_real_escape_string($db->mysql_link,$macIDLow)."',
	'".mysqli_real_escape_string($db->mysql_link,$macIDHigh)."',
	'".time()."');";
	if($db->executeQuery($query)) {
		$roomNodeId=$db->getLastInsertID();
		if(generateAndAddNodeSwitches($nodeId,$roomNodeId)) {
			$messageArray=array();
			$messageArray["roomNodeId"]=$db->getLastInsertID();
			sendRefreshDataCommandToHub($hubId);
			sendResponse("Success",$messageArray);
		} else {
			sendResponse("Failure","Unexpected error, please check server logs.");
		}
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function deleteRoomNode($hubId,$roomNodeId) {
	$db = new MySQL();
	$query="DELETE FROM nodeswitches 
	WHERE roomNodeId='".mysqli_real_escape_string($db->mysql_link,$roomNodeId)."';";
	if($db->executeQuery($query)) {
		$query="DELETE FROM roomnodes 
		WHERE roomNodeId='".mysqli_real_escape_string($db->mysql_link,$roomNodeId)."';";
		if($db->executeQuery($query)) {
			sendRefreshDataCommandToHub($hubId);
			sendResponse("Success","Room Node Deleted");
		} else {
			sendResponse("Failure",$db->getErrorString());
		}
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function saveNodeIDForRoomNode($hubId,$roomNodeId,$nodeIDHigh,$nodeIDLow) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	$query = "SELECT roomNodeId FROM roomnodes WHERE 
	roomNodeId='".mysqli_real_escape_string($db->mysql_link,$roomNodeId)."';";
	if(!isValueExists($query)) {
		sendResponse("Failure","Room Node not Found.");
	}
	// update existing room node
	$query="UPDATE roomnodes SET nodeIDHigh='".$nodeIDHigh."',nodeIDLow='".$nodeIDLow."' 
	WHERE roomNodeId='".mysqli_real_escape_string($db->mysql_link,$roomNodeId)."';";
	if($db->executeQuery($query)) {
		sendResponse("Success","Roomnode Node ID Saved.");	
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function addSensor($hubId,$roomId,$sensorTypeId,$sensorName,$version,$macIDHigh,$macIDLow) {
	$db = new MySQL();
	$query = "SELECT roomId FROM rooms WHERE 
	roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."';";
	$room = $db->executeQueryAndGetFirstRow($query);
	if($room===FALSE) {
		sendResponse("Failure","Invalid Room.");
	}
	$query = "SELECT sensorId FROM sensors WHERE 
	roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."' AND 
	sensorName='".mysqli_real_escape_string($db->mysql_link,$sensorName)."';";
	if(!isValueExists($query)) {
		$query = "INSERT INTO sensors(roomId,sensorTypeId,sensorName,`version`,macIDLow,macIDHigh,dateAdded) VALUES(
		'".mysqli_real_escape_string($db->mysql_link,$roomId)."',
		'".mysqli_real_escape_string($db->mysql_link,$sensorTypeId)."',
		'".mysqli_real_escape_string($db->mysql_link,$sensorName)."',
		'".mysqli_real_escape_string($db->mysql_link,$version)."',
		'".mysqli_real_escape_string($db->mysql_link,$macIDLow)."',
		'".mysqli_real_escape_string($db->mysql_link,$macIDHigh)."',
		'".time()."');";
		if($db->executeQuery($query)) {
			$messageArray=array();
			$messageArray["sensorId"]=$db->getLastInsertID();
			sendRefreshDataCommandToHub($hubId);
			sendResponse("Success",$messageArray);
		} else {
			sendResponse("Failure",$db->getErrorString());
		}
	} else {
		sendResponse("Failure","A Sensor with this name already exists in this Room on this hub.");
	}
}
function deleteSensor($hubId,$sensorId) {
	$db = new MySQL();
	$query="DELETE FROM sensors 
	WHERE sensorId='".mysqli_real_escape_string($db->mysql_link,$sensorId)."';";
	if($db->executeQuery($query)) {
		sendRefreshDataCommandToHub($hubId);
		sendResponse("Success","Sensor Deleted");
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function saveNodeIdForSensor($hubId,$sensorId,$nodeIDHigh,$nodeIDLow) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	$query = "SELECT sensorId FROM sensors WHERE 
	sensorId='".mysqli_real_escape_string($db->mysql_link,$sensorId)."';";
	if(!isValueExists($query)) {
		sendResponse("Failure","Sensor not Found.");
	}
	// update existing room node
	$query="UPDATE sensors SET nodeIDHigh='".$nodeIDHigh."',nodeIDLow='".$nodeIDLow."' 
	WHERE sensorId='".mysqli_real_escape_string($db->mysql_link,$sensorId)."';";
	if($db->executeQuery($query)) {
		sendResponse("Success","Sensor Node ID Saved.");	
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function addOneTouchController($hubId,$simpleName,$version,$macIDHigh,$macIDLow) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	$query = "SELECT oneTouchControllerId FROM onetouchcontrollers WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."' AND 
	simpleName='".mysqli_real_escape_string($db->mysql_link,$simpleName)."';";
	if(!isValueExists($query)) {
		$query = "INSERT INTO onetouchcontrollers(hubId,simpleName,`version`,macIDHigh,macIDLow,dateAdded) VALUES(
		'".mysqli_real_escape_string($db->mysql_link,$hubId)."',
		'".mysqli_real_escape_string($db->mysql_link,$simpleName)."',
		'".mysqli_real_escape_string($db->mysql_link,$version)."',
		'".mysqli_real_escape_string($db->mysql_link,$macIDHigh)."',
		'".mysqli_real_escape_string($db->mysql_link,$macIDLow)."',
		'".time()."');";
		if($db->executeQuery($query)) {
			$messageArray=array();
			$messageArray["oneTouchControllerId"]=$db->getLastInsertID();
			sendRefreshDataCommandToHub($hubId);
			sendResponse("Success",$messageArray);
		} else {
			sendResponse("Failure",$db->getErrorString());
		}
	} else {
		sendResponse("Failure","A One Touch Controller with this name already exists on this hub.");
	}
}
function deleteOneTouchController($hubId,$oneTouchControllerId) {
	$db = new MySQL();
	$query="DELETE FROM onetouchcontrollermappings 
	WHERE oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."';";
	if($db->executeQuery($query)) {
		$query="DELETE FROM onetouchcontrollers 
		WHERE oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."';";
		if($db->executeQuery($query)) {
			sendRefreshDataCommandToHub($hubId);
			sendResponse("Success","OneTouchController Deleted");
		} else {
			sendResponse("Failure",$db->getErrorString());
		}
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function saveNodeIDForOTC($hubId,$oneTouchControllerId,$nodeIDHigh,$nodeIDLow) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	$query = "SELECT oneTouchControllerId FROM onetouchcontrollers WHERE 
	oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."';";
	if(!isValueExists($query)) {
		sendResponse("Failure","OneTouchController not Found.");
	}
	// update existing room node
	$query="UPDATE onetouchcontrollers SET nodeIDHigh='".$nodeIDHigh."',nodeIDLow='".$nodeIDLow."' 
	WHERE oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."';";
	if($db->executeQuery($query)) {
		sendResponse("Success","OneTouchController Node ID Saved.");	
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function addOTCMapping($hubId,$oneTouchControllerId,$switchNumber,$percent,$key,$value) {
	$db = new MySQL();
	$query = "SELECT oneTouchControllerId FROM onetouchcontrollers WHERE 
	oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid OneTouchController.");
	}
	$query = "SELECT mappingId FROM onetouchcontrollermappings WHERE 
	switchNumber='".mysqli_real_escape_string($db->mysql_link,$switchNumber)."' AND
	oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."';";
	if(isValueExists($query)) {
		$query = "UPDATE onetouchcontrollermappings SET 
		`percent`='".mysqli_real_escape_string($db->mysql_link,$percent)."',
		`key`='".mysqli_real_escape_string($db->mysql_link,$key)."',
		`value`='".mysqli_real_escape_string($db->mysql_link,$value)."',
		dateAdded='".time()."'
		WHERE switchNumber='".mysqli_real_escape_string($db->mysql_link,$switchNumber)."' AND 
		oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."';";
		if($db->executeQuery($query)) {
			sendRefreshDataCommandToHub($hubId);
			sendResponse("Success","Updated Successfully.");
		} else {
			sendResponse("Failure",$db->getErrorString());
		}
	} else {
		$query = "INSERT INTO onetouchcontrollermappings(oneTouchControllerId,switchNumber,
		`percent`,`key`,`value`,dateAdded) VALUES(
		'".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."',
		'".mysqli_real_escape_string($db->mysql_link,$switchNumber)."',
		'".mysqli_real_escape_string($db->mysql_link,$percent)."',
		'".mysqli_real_escape_string($db->mysql_link,$key)."',
		'".mysqli_real_escape_string($db->mysql_link,$value)."',
		'".time()."');";
		if($db->executeQuery($query)) {
			$messageArray=array();
			$messageArray["mappingId"]=$db->getLastInsertID();
			sendRefreshDataCommandToHub($hubId);
			sendResponse("Success",$messageArray);
		} else {
			sendResponse("Failure",$db->getErrorString());
		}
	}
}
function addOTCMappings($hubId,$oneTouchControllerId,$otcMappings) {
	$db = new MySQL();
	$query = "SELECT oneTouchControllerId FROM onetouchcontrollers WHERE 
	oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid OneTouchController.");
	}
	for($i=0;$i<sizeof($otcMappings);$i++) {
		if(!empty($otcMappings)) {
			if($otcMappings[$i]['switchNumber']>0) {
				$query = "SELECT mappingId FROM onetouchcontrollermappings WHERE 
				switchNumber='".mysqli_real_escape_string($db->mysql_link,$otcMappings[$i]['switchNumber'])."' AND 
				oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."';";
				if(isValueExists($query)) {
					$query = "UPDATE onetouchcontrollermappings SET 
					`percent`='".mysqli_real_escape_string($db->mysql_link,$otcMappings[$i]['percent'])."',
					`key`='".mysqli_real_escape_string($db->mysql_link,$otcMappings[$i]['key'])."',
					`value`='".mysqli_real_escape_string($db->mysql_link,$otcMappings[$i]['value'])."',
					dateAdded='".time()."'
					WHERE switchNumber='".mysqli_real_escape_string($db->mysql_link,$otcMappings[$i]['switchNumber'])."' AND 
					oneTouchControllerId='".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."';";
					if($db->executeQuery($query)) {
						//sendResponse("Success","Updated Successfully.");
					} else {
						sendResponse("Failure",$db->getErrorString());
					}
				} else {
					$query = "INSERT INTO onetouchcontrollermappings(oneTouchControllerId,switchNumber,
					`percent`,`key`,`value`,dateAdded) VALUES(
					'".mysqli_real_escape_string($db->mysql_link,$oneTouchControllerId)."',
					'".mysqli_real_escape_string($db->mysql_link,$otcMappings[$i]['switchNumber'])."',
					'".mysqli_real_escape_string($db->mysql_link,$otcMappings[$i]['percent'])."',
					'".mysqli_real_escape_string($db->mysql_link,$otcMappings[$i]['key'])."',
					'".mysqli_real_escape_string($db->mysql_link,$otcMappings[$i]['value'])."',
					'".time()."');";
					if($db->executeQuery($query)) {
						
					} else {
						sendResponse("Failure",$db->getErrorString());
					}
					
				}
			}
		}
	}
	sendRefreshDataCommandToHub($hubId);
	sendResponse("Success","Added Successfully.");
}
function deleteOTCMapping($hubId,$mappingId) {
	$db = new MySQL();
	$query="DELETE FROM onetouchcontrollermappings 
	WHERE mappingId='".mysqli_real_escape_string($db->mysql_link,$mappingId)."';";
	if($db->executeQuery($query)) {
		sendRefreshDataCommandToHub($hubId);
		sendResponse("Success","Mapping Deleted");
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function addAppliance($hubId,$roomId,$nodeSwitchLoadTypeId,$simpleName,
$nodeSwitchId,$isIREnabled,$customValue) {
	$db = new MySQL();
	$query = "SELECT roomId FROM rooms WHERE 
	roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."';";
	$room = $db->executeQueryAndGetFirstRow($query);
	if($room===FALSE) {
		sendResponse("Failure","Invalid Room.");
	}
	$query = "SELECT applianceId FROM appliances WHERE 
	roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."';";
	if(isValueUnique($query)) {
		$query = "INSERT INTO appliances(roomId,nodeSwitchLoadTypeId,simpleName,nodeSwitchId,isIREnabled,customValue,dateAdded) VALUES(
		'".mysqli_real_escape_string($db->mysql_link,$roomId)."',
		'".mysqli_real_escape_string($db->mysql_link,$nodeSwitchLoadTypeId)."',
		'".mysqli_real_escape_string($db->mysql_link,$simpleName)."',
		'".mysqli_real_escape_string($db->mysql_link,$nodeSwitchId)."',
		'".mysqli_real_escape_string($db->mysql_link,$isIREnabled)."',
		'".mysqli_real_escape_string($db->mysql_link,$customValue)."',
		'".time()."');";
		if($db->executeQuery($query)) {
			$messageArray=array();
			$messageArray["applianceId"]=$db->getLastInsertID();
			sendRefreshDataCommandToHub($hubId);
			sendResponse("Success",$messageArray);
		} else {
			sendResponse("Failure",$db->getErrorString());
		}
	} else {
		sendResponse("Failure","An appliance with this name already exists in this Room on this hub.");
	}
}
function deleteAppliance($hubId,$applianceId) {
	$db = new MySQL();
	$query="DELETE FROM appliances 
	WHERE applianceId='".mysqli_real_escape_string($db->mysql_link,$applianceId)."';";
	if($db->executeQuery($query)) {
		sendRefreshDataCommandToHub($hubId);
		sendResponse("Success","Appliance Deleted");
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function generateAndAddNodeSwitches($nodeId,$roomNodeId) {
	$db = new MySQL();

	$nodeSwitches=Array();
	$oSNumber=1;
	$dSNumber=6;
	$cSNumber=1;

	$query = "SELECT * FROM nodes WHERE nodeId='".mysqli_real_escape_string($db->mysql_link,$nodeId)."'";
	$node = $db->executeQueryAndGetFirstRow($query,MYSQLI_ASSOC);

	switch($node->nodeName) {
		case "340":
		$dSNumber=1;
		break;
		case "310":
		$dSNumber=4;
		break;
		case "113":
		$dSNumber=4;
		break;
		case "102":
		$oSNumber=2;
		break;
		default:
		break;
	}
	// AHU is a special case, where switch number 0 is also being used
	if($node->hasMaster=="1") {
		// add master switch
		$ns=new NodeSwitch();
		$ns->category="Master";
		$ns->switchName="Master";
		$ns->type="Master";
		$ns->switchNumber=0;
		$ns->percent=0;
		$nodeSwitches[]=$ns;
	}
	
	switch($node->nodeType) {
		case "Normal":
			for($i=0;$i<$node->onOffCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="Light";
				$ns->type="OnOff";
				$ns->percent=0;
				if($oSNumber==6) {
					// see above commented for explanation
					$oSNumber+=$node->dimmerCount;
				}
				$ns->switchNumber=$oSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			for($i=0;$i<$node->dimmerCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="Fan";
				$ns->type="Dimmer";
				$ns->percent=0;
				$ns->switchNumber=$dSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			break;
		case "Curtain":
			for($i=0;$i<$node->onOffCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="Curtain";
				$ns->type="Curtain";
				$ns->percent=0;
				$ns->switchNumber=$cSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			break;
		case "AHU":
			// AHU is a special case above, where switch number 0 is also being used
			$ns=new NodeSwitch();
			$ns->category="AHU";
			$ns->type="OnOff";
			$ns->percent=0;
			$ns->switchNumber=0;
			$ns->switchName=$ns->category.$ns->switchNumber;
			$nodeSwitches[]=$ns;

			for($i=0;$i<$node->onOffCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="AHU";
				$ns->type="OnOff";
				$ns->percent=0;
				$ns->switchNumber=$oSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			break;
		case "Bell": // for all others except curtain, logic more or less the same
			for($i=0;$i<$node->onOffCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="Bell";
				$ns->type="OnOff";
				$ns->percent=0;
				$ns->switchNumber=$oSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			for($i=0;$i<$node->dimmerCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="Bell";
				$ns->type="Dimmer";
				$ns->percent=0;
				$ns->switchNumber=$dSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			break;
		case "TwoWay": // for all others except curtain, logic more or less the same
			for($i=0;$i<$node->onOffCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="TwoWay";
				$ns->type="OnOff";
				$ns->percent=0;
				$ns->switchNumber=$oSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			for($i=0;$i<$node->dimmerCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="TwoWay";
				$ns->type="Dimmer";
				$ns->percent=0;
				$ns->switchNumber=$dSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			break;
		case "Door": // for all others except curtain, logic more or less the same
			for($i=0;$i<$node->onOffCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="TwoWay";
				$ns->type="OnOff";
				$ns->percent=0;
				$ns->switchNumber=$oSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			for($i=0;$i<$node->dimmerCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="TwoWay";
				$ns->type="Dimmer";
				$ns->percent=0;
				$ns->switchNumber=$dSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			break;
		default: // for all others except curtain, logic more or less the same
			for($i=0;$i<$node->onOffCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="Light";
				$ns->type="OnOff";
				$ns->percent=0;
				$ns->switchNumber=$oSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			for($i=0;$i<$node->dimmerCount;$i++) {
				$ns=new NodeSwitch();
				$ns->category="Fan";
				$ns->type="Dimmer";
				$ns->percent=0;
				$ns->switchNumber=$dSNumber++;
				$ns->switchName=$ns->category.$ns->switchNumber;
				$nodeSwitches[]=$ns;
			}
			break;
	} // of switch
	
	$isDone=true;
	for($i=0;$i<sizeof($nodeSwitches);$i++) {
		$query = "INSERT INTO nodeswitches(roomNodeId,switchName,`type`,category,switchNumber,customValue,percent,dateAdded) VALUES(
		'".mysqli_real_escape_string($db->mysql_link,$roomNodeId)."',
		'".mysqli_real_escape_string($db->mysql_link,$nodeSwitches[$i]->switchName)."',
		'".mysqli_real_escape_string($db->mysql_link,$nodeSwitches[$i]->type)."',
		'".mysqli_real_escape_string($db->mysql_link,$nodeSwitches[$i]->category)."',
		'".mysqli_real_escape_string($db->mysql_link,$nodeSwitches[$i]->switchNumber)."',
		'".mysqli_real_escape_string($db->mysql_link,$nodeSwitches[$i]->customValue)."',
		'".mysqli_real_escape_string($db->mysql_link,$nodeSwitches[$i]->percent)."',
		'".time()."');";
		if(!$db->executeQuery($query)) {
			$isDone=false;
		}
	}
	return $isDone;
}
function testJSONInput($jsonString) {
	var_dump(json_decode($jsonString));
	var_dump(json_decode($jsonString,true));
}
function saveScene($hubId,$sceneId,$sceneName,$sceneSwitches,$sceneSensors) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	if($sceneId>0) {
		$query = "SELECT sceneId FROM scenes WHERE 
		sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."';";
		if(!isValueExists($query)) {
			sendResponse("Failure","Scene not Found to Edit.");
		}
		// update existing scene
		$query = "SELECT sceneId FROM scenes WHERE 
		sceneId!='".mysqli_real_escape_string($db->mysql_link,$sceneId)."' AND 
		sceneName='".mysqli_real_escape_string($db->mysql_link,$sceneName)."' AND 
		hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
		if(isValueExists($query)) {
			sendResponse("Failure","A Scene with this name already exists on this hub.");
		} else {
			$query="UPDATE scenes SET sceneName='".$sceneName."' 
			WHERE sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."';";
			if($db->executeQuery($query)) {
				$query="DELETE FROM sceneswitches 
				WHERE sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."';";
				if($db->executeQuery($query)) {
					
				} else {
					sendResponse("Failure",$db->getErrorString());
				}
				$query="DELETE FROM scenesensors 
				WHERE sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."';";
				if($db->executeQuery($query)) {
					
				} else {
					sendResponse("Failure",$db->getErrorString());
				}
			} else {
				sendResponse("Failure",$db->getErrorString());
			}
		}
	} else {
		// add new scene
		$query = "SELECT sceneId FROM scenes WHERE
		sceneName='".mysqli_real_escape_string($db->mysql_link,$sceneName)."' AND 
		hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
		if(isValueExists($query)) {
			sendResponse("Failure","A Scene with this name already exists on this hub.");
		} else {
			$query = "INSERT INTO scenes(hubId,sceneName,dateAdded) VALUES(
			'".mysqli_real_escape_string($db->mysql_link,$hubId)."',
			'".mysqli_real_escape_string($db->mysql_link,$sceneName)."',
			'".time()."');";
			if($db->executeQuery($query)) {
				$sceneId=$db->getLastInsertID();
			} else {
				sendResponse("Failure",$db->getErrorString());
			}
		}
	}
	// add scene switches and scene sensors
	for($i=0;$i<sizeof($sceneSwitches);$i++) {
		if(!empty($sceneSwitches)) {
			if($sceneSwitches[$i]['nodeSwitchId']>0) {
				$query = "INSERT INTO sceneswitches(sceneId,nodeSwitchId,percent,dateAdded) VALUES(
				'".mysqli_real_escape_string($db->mysql_link,$sceneId)."',
				'".mysqli_real_escape_string($db->mysql_link,$sceneSwitches[$i]['nodeSwitchId'])."',
				'".mysqli_real_escape_string($db->mysql_link,$sceneSwitches[$i]['percent'])."',
				'".time()."');";
				if(!$db->executeQuery($query)) {
					sendResponse("Failure",$db->getErrorString());
				}
			}
		}			
	}
	for($i=0;$i<sizeof($sceneSensors);$i++) {
		if(!empty($sceneSensors)) {
			if($sceneSensors[$i]['sensorId']>0) {
				$query = "INSERT INTO scenesensors(sceneId,sensorId,isToArm,dateAdded) VALUES(
				'".mysqli_real_escape_string($db->mysql_link,$sceneId)."',
				'".mysqli_real_escape_string($db->mysql_link,$sensorId)."',
				'".mysqli_real_escape_string($db->mysql_link,$isToArm)."',
				'".time()."');";
				if(!$db->executeQuery($query)) {
					sendResponse("Failure",$db->getErrorString());
				}
			}
		}
	}
	$messageArray=array();
	$messageArray["sceneId"]=$sceneId;
	sendRefreshDataCommandToHub($hubId);
	sendResponse("Success",$messageArray);
}
function deleteScene($hubId,$sceneId) {
	$db = new MySQL();
	$query="DELETE FROM sceneswitches 
	WHERE sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."';";
	if(!$db->executeQuery($query)) {
		sendResponse("Failure",$db->getErrorString());
	}
	$query="DELETE FROM scenesensors 
	WHERE sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."';";
	if(!$db->executeQuery($query)) {
		sendResponse("Failure",$db->getErrorString());
	}
	$query="DELETE FROM sceneirindices 
	WHERE sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."';";
	if(!$db->executeQuery($query)) {
		sendResponse("Failure",$db->getErrorString());
	}
	$query="DELETE FROM scenes 
	WHERE sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."';";
	if($db->executeQuery($query)) {
		sendRefreshDataCommandToHub($hubId);
		sendResponse("Success","Scene Deleted");
	} else {
		sendResponse("Failure",$db->getErrorString());
	}
}
function sceneApplied($hubId,$sceneId) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	$query = "SELECT sceneId FROM scenes WHERE 
	sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Scene.");
	}
	try {
		$db->beginTransaction();
		//Updating Node Switch States table based on Scene Applied
		$query="SELECT nodeSwitchId,percent FROM sceneswitches WHERE 
		sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."'";
		$sceneSwitches = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
		if($db->getRowsCount()>0){
			for($i=0;$i<sizeof($sceneSwitches);$i++) {
				if($sceneSwitches[$i]['nodeSwitchId'] == '' || $sceneSwitches[$i]['nodeSwitchId'] == '0') {
					continue;
				}
				$query="UPDATE nodeswitches SET
				percent='".mysqli_real_escape_string($db->mysql_link,$sceneSwitches[$i]['percent'])."' WHERE 
				nodeSwitchId='".mysqli_real_escape_string($db->mysql_link,$sceneSwitches[$i]['nodeSwitchId'])."'";
				$db->executeQuery($query);
			}
		}
		//Updating Sensors table based on Scene Applied
		$sceneSensors = $db->executeQueryAndGetArray("SELECT sensorId,ssArmed FROM scenesensors WHERE 
		sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."'",MYSQLI_ASSOC);
		if($db->getRowsCount()>0){
			for($i=0;$i<sizeof($sceneSensors);$i++){
				if($sceneSensors[$i]['sensorId'] == '' || $sceneSensors[$i]['sensorId'] == '0'){continue;}
				$db->executeQuery("UPDATE sensors SET
				isArmed='".mysqli_real_escape_string($db->mysql_link,$sceneSensors[$i]['isArmed'])."' WHERE 
				sensorId='".mysqli_real_escape_string($db->mysql_link,$sceneSensors[$i]['sensorId'])."'");
			}
		}
		$db->endTransaction();
		sendResponse("Success","Done Successfully.");
	} catch(Exception $e){
		$db->rollbackTransaction();
		sendResponse("Failure",$db->getErrorString());
	}
}
function saveHubFCMToken($serialNumber,$fcmToken,$deviceID,$deviceOS) {
	$db = new MySQL();
	if($serialNumber=="" || $fcmToken=="" || $deviceID=="") { 
		sendResponse("Failure","Please provide all parameters");
	} else {
		$query = "SELECT hubId,simpleName FROM hubs WHERE 
		serialNumber='".mysqli_real_escape_string($db->mysql_link,$serialNumber)."';";
		$hub = $db->executeQueryAndGetFirstRow($query);
		if($hub===FALSE) {
			sendResponse("Failure","Invalid Hub.");
		} else {
			$hubId=$hub->hubId;
			$query = "INSERT INTO hubfcmtokens(hubId,fcmToken,deviceID,deviceOS,dateReceived) VALUES(
			'".mysqli_real_escape_string($db->mysql_link,$hubId)."',
			'".mysqli_real_escape_string($db->mysql_link,$fcmToken)."',
			'".mysqli_real_escape_string($db->mysql_link,$deviceID)."',
			'".mysqli_real_escape_string($db->mysql_link,$deviceOS)."',
			'".time()."'
			);";
			if($db->executeQuery($query)) {
				sendResponse("Success","FCM Token Saved.");
			} else {
				sendResponse("Failure",$db->getErrorString());
			}
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
function toggleSwitchViaVoice($hubId,$roomNameToSearch,$switchNameToSearch,$percent) {
	$db = new MySQL();
	$query = "SELECT roomId,roomName FROM rooms 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
	//echo $query;
	$rooms = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	if($db->getRowsCount()>0){
		for($i=0;$i<sizeof($rooms);$i++) {
			$roomName=$rooms[$i]['roomName'];
			//echo $roomName." ";
			if(strcmp(sanitizeStringToCompare($roomName),sanitizeStringToCompare($roomNameToSearch))==0) {
				$roomId=$rooms[$i]['roomId'];
				$query = "SELECT roomNodeId FROM roomnodes 
				WHERE roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."'";
				$roomnodes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
				if($db->getRowsCount()>0){
					for($j=0;$j<sizeof($roomnodes);$j++) {
						$roomNodeId=$roomnodes[$j]['roomNodeId'];
						$query = "SELECT nodeSwitchId,switchName FROM nodeswitches 
						WHERE roomNodeId='".mysqli_real_escape_string($db->mysql_link,$roomNodeId)."'";
						$nodeswitches = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
						if($db->getRowsCount()>0){
							for($k=0;$k<sizeof($nodeswitches);$k++) {
								$switchName=$nodeswitches[$k]['switchName'];
								if(strcmp(sanitizeStringToCompare($switchName),sanitizeStringToCompare($switchNameToSearch))==0) {
									$nodeSwitchId=$nodeswitches[$k]['nodeSwitchId'];
									toggleSwitchViaMobile($hubId,$nodeSwitchId,$percent);
								}
							}
							sendResponse("Failure","Room found but switch not found on the target hub.");
						}
						sendResponse("Failure","Room found but switch not found on the target hub.");
					}
					sendResponse("Failure","Room found but switch not found on the target hub.");
				}
				sendResponse("Failure","Room found but switch not found on the target hub.");
			}
		}
		sendResponse("Failure","Room not found on the target hub.");
	}
	sendResponse("Failure","Room not found on the target hub.");
}
function toggleSwitchViaMobile($hubId,$nodeSwitchId,$percent) {
	$db = new MySQL();
	if($hubId<=0) {
		sendResponse("Failure","Invalid Hub");
	}
	$query="SELECT hubFCMTokenId,fcmToken,deviceOS FROM hubfcmtokens 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."' 
	ORDER BY dateReceived DESC LIMIT 1";
	$fcmObject=$db->executeQueryAndGetFirstRow($query);
	if($fcmObject===FALSE) {
		sendResponse("Failure","Hub is not enabled for Remote Operations");
	} else {
		if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
			sendResponse("Failure",$db->getErrorString());
		}
		
		$fcmToken = $fcmObject->fcmToken;
		$deviceOS = $fcmObject->deviceOS;
		
		$dataArray = array(
			'remoteCommand' => "toggleSwitch",
			'nodeSwitchId' => $nodeSwitchId,
			'percent' => $percent
		);
		$messageArray = array(
			'to' => $fcmToken,
			'data' => $dataArray,
			'priority' => "high"
		);
		
		$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
		if($sendNotificationResult===true) {
			sendResponse("Success","Command Sent");
		} else {
			sendResponse("Failure",$sendNotificationResult);
		}
	}
}
function toggleAllSwitches($hubId,$nodeSwitchStates,$ahuStatuses) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	for($i=0;$i<sizeof($nodeSwitchStates);$i++) {
		if(!empty($nodeSwitchStates[$i])) {
			$query="UPDATE nodeswitches SET 
			percent='".$nodeSwitchStates[$i]['percent']."' 
			WHERE nodeSwitchId='".mysqli_real_escape_string($db->mysql_link,$nodeSwitchStates[$i]['nodeSwitchId'])."';";
			if($db->executeQuery($query)) {
					
			} else {
				sendResponse("Failure","ns: ".$db->getErrorString());
			}
		}
	}
	for($i=0;$i<sizeof($ahuStatuses);$i++) {
		if(!empty($ahuStatuses[$i])) {
			$query = "SELECT ahuStatusId FROM ahustatuses WHERE 
			roomNodeId='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['roomNodeId'])."';";
			if($db->hasRecords($query)) {
				$query="UPDATE ahustatuses SET
				fanStatus='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['fanStatus'])."',
				fanSpeed='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['fanSpeed'])."',
				setTemperature='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['setTemperature'])."',
				roomTemperature='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['roomTemperature'])."',
				displayStatus='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['displayStatus'])."',
				dateAdded='".time()."',
				caller='".mysqli_real_escape_string($db->mysql_link,'hub')."' WHERE
				roomNodeId='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['roomNodeId'])."';";
				if($db->executeQuery($query)) {
					
				} else {
					sendResponse("Failure","ahu update: ".$db->getErrorString());
				}
			} else {
				$query = "INSERT INTO ahustatuses(roomNodeId,fanStatus,fanSpeed,setTemperature,roomTemperature,displayStatus,dateAdded,caller) VALUES(
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['roomNodeId'])."',
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['fanStatus'])."',
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['fanSpeed'])."',
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['setTemperature'])."',
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['roomTemperature'])."',
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['displayStatus'])."',
				'".time()."',
				'".mysqli_real_escape_string($db->mysql_link,'hub')."');";
				if($db->executeQuery($query)){
					
				} else {
					sendResponse("Failure","ahu ins: ".$db->getErrorString());
				}
			}
		}
	}
	sendResponse("Success","All Switch States written to the Server.");
}
function toggleSwitches($hubId,$nodeSwitchStates) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	for($i=0;$i<sizeof($nodeSwitchStates);$i++) {
		if(!empty($nodeSwitchStates[$i])) {
			$query="UPDATE nodeswitches SET 
			percent='".$nodeSwitchStates[$i]['percent']."' 
			WHERE nodeSwitchId='".mysqli_real_escape_string($db->mysql_link,$nodeSwitchStates[$i]['nodeSwitchId'])."';";
			if($db->executeQuery($query)) {
					
			} else {
				sendResponse("Failure","ns: ".$db->getErrorString());
			}
		}
	}
	$query = "SELECT customerId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$customerId=$db->getFirstRowFirstColumn($query);
	if($customerId=='' || $customerId===FALSE) {
		$errorString = "Invalid Customer.";
	}
	$query="SELECT customerFCMTokenId,fcmToken,deviceOS FROM customerfcmtokens 
	WHERE customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."' 
	ORDER BY dateReceived DESC LIMIT 1";
	$fcmObject=$db->executeQueryAndGetFirstRow($query);
	if($fcmObject===FALSE) {
		$errorString .= " Customer is not enabled for Remote Operations";
	} else {
		if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
			$errorString .= " ".$db->getErrorString();
		}
		
		$fcmToken = $fcmObject->fcmToken;
		$deviceOS = $fcmObject->deviceOS;
		
		$dataArray = array(
			'remoteCommand' => "toggleSwitches",
			'nodeSwitchStates' => $nodeSwitchStates
		);
		$messageArray = array(
			'to' => $fcmToken,
			'data' => $dataArray,
			'priority' => "high"
		);
		
		$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
		if($sendNotificationResult===true) {
			$errorString = "Notified Mobile App.";
		} else {
			$errorString.= " ".$sendNotificationResult;
		}
	}
	sendResponse("Success","Switch States written to the Server. ".$errorString);
}
function toggleAHUs($hubId,$ahuStatuses) {
	$db = new MySQL();
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	for($i=0;$i<sizeof($ahuStatuses);$i++) {
		if(!empty($ahuStatuses[$i])) {
			$query = "SELECT ahuStatusId FROM ahustatuses WHERE 
			roomNodeId='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['roomNodeId'])."';";
			if($db->hasRecords($query)) {
				$query="UPDATE ahustatuses SET
				fanStatus='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['fanStatus'])."',
				fanSpeed='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['fanSpeed'])."',
				setTemperature='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['setTemperature'])."',
				roomTemperature='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['roomTemperature'])."',
				displayStatus='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['displayStatus'])."',
				dateAdded='".time()."',
				caller='".mysqli_real_escape_string($db->mysql_link,'hub')."' WHERE
				roomNodeId='".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['roomNodeId'])."';";
				if($db->executeQuery($query)) {
					
				} else {
					sendResponse("Failure","ahu update: ".$db->getErrorString());
				}
			} else {
				$query = "INSERT INTO ahustatuses(roomNodeId,fanStatus,fanSpeed,setTemperature,roomTemperature,displayStatus,dateAdded,caller) VALUES(
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['roomNodeId'])."',
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['fanStatus'])."',
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['fanSpeed'])."',
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['setTemperature'])."',
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['roomTemperature'])."',
				'".mysqli_real_escape_string($db->mysql_link,$ahuStatuses[$i]['displayStatus'])."',
				'".time()."',
				'".mysqli_real_escape_string($db->mysql_link,'hub')."');";
				if($db->executeQuery($query)){
					
				} else {
					sendResponse("Failure","ahu ins: ".$db->getErrorString());
				}
			}
		}
	}
	$query = "SELECT customerId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	$customerId=$db->getFirstRowFirstColumn($query);
	if($customerId=='' || $customerId===FALSE) {
		$errorString = "Invalid Customer.";
	}
	$query="SELECT customerFCMTokenId,fcmToken,deviceOS FROM customerfcmtokens 
	WHERE customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."' 
	ORDER BY dateReceived DESC LIMIT 1";
	$fcmObject=$db->executeQueryAndGetFirstRow($query);
	if($fcmObject===FALSE) {
		$errorString .= " Customer is not enabled for Remote Operations";
	} else {
		if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
			$errorString .= " ".$db->getErrorString();
		}
		
		$fcmToken = $fcmObject->fcmToken;
		$deviceOS = $fcmObject->deviceOS;
		
		$dataArray = array(
			'remoteCommand' => "toggleAHUs",
			'ahuStatuses' => $ahuStatuses
		);
		$messageArray = array(
			'to' => $fcmToken,
			'data' => $dataArray,
			'priority' => "high"
		);
		
		$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
		if($sendNotificationResult===true) {
			$errorString = "Notified Mobile App.";
		} else {
			$errorString.= " ".$sendNotificationResult;
		}
	}
	sendResponse("Success","AHU States written to the Server. ".$errorString);
}
function applySceneViaVoice($hubId,$sceneNameToApply) {
	$db = new MySQL();
	if(isValueInteger($sceneNameToApply)) {
		applySceneViaMobile($hubId,$sceneNameToApply);
	} else {
		$query = "SELECT sceneId,sceneName FROM scenes 
		WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
		$scenes = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
		if($db->getRowsCount()>0){
			for($j=0;$j<sizeof($scenes);$j++) {
				$sceneName=$scenes[$j]['sceneName'];
				if(strcmp(sanitizeStringToCompare($sceneName),sanitizeStringToCompare($sceneNameToApply))==0) {
					$sceneId=$scenes[$j]['sceneId'];
					applySceneViaMobile($hubId,$sceneId);
				}
			}
			sendResponse("Failure","Scene not found on your Hub.");
		} else {
			sendResponse("Failure","Scene not found on your Hub.");
		}
	}
}
function applySceneViaMobile($hubId,$sceneId) {
	$db = new MySQL();
	if($hubId<=0) {
		sendResponse("Failure","Invalid Hub");
	}
	$query = "SELECT hubId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."';";
	if(!$db->hasRecords($query)) {
		sendResponse("Failure","Hub not Found.");
	}
	if($sceneId<=0) {
		sendResponse("Failure","Invalid Scene");
	}
	$query = "SELECT sceneId FROM scenes WHERE 
	sceneId='".mysqli_real_escape_string($db->mysql_link,$sceneId)."';";
	if(!$db->hasRecords($query)) {
		sendResponse("Failure","Scene not Found.");
	}
	$query="SELECT hubFCMTokenId,fcmToken,deviceOS FROM hubfcmtokens 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."' 
	ORDER BY dateReceived DESC LIMIT 1";
	$fcmObject=$db->executeQueryAndGetFirstRow($query);
	if($fcmObject===FALSE) {
		sendResponse("Failure","Hub is not enabled for Remote Operations");
	} else {
		if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
			sendResponse("Failure",$db->getErrorString());
		}
		
		$fcmToken = $fcmObject->fcmToken;
		$deviceOS = $fcmObject->deviceOS;
		
		$dataArray = array(
			'remoteCommand' => "applyScene",
			'sceneId' => $sceneId
		);
		$messageArray = array(
			'to' => $fcmToken,
			'data' => $dataArray,
			'priority' => "high"
		);
		
		$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
		if($sendNotificationResult===true) {
			sendResponse("Success","Command Sent");
		} else {
			sendResponse("Failure",$sendNotificationResult);
		}
	}
}
function toggleSensorViaVoice($hubId,$roomNameToSearch,$sensorNameToSearch,$isToArm) {
	$db = new MySQL();
	$query = "SELECT roomId,roomName FROM rooms 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."'";
	$rooms = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
	if($db->getRowsCount()>0){
		for($i=0;$i<sizeof($rooms);$i++) {
			$roomName=$rooms[$i]['roomName'];
			if(strcmp(sanitizeStringToCompare($roomName),sanitizeStringToCompare($roomNameToSearch))==0) {
				$roomId=$rooms[$i]['roomId'];
				$query = "SELECT sensorId,sensorName FROM sensors 
				WHERE roomId='".mysqli_real_escape_string($db->mysql_link,$roomId)."'";
				$sensors = $db->executeQueryAndGetArray($query,MYSQLI_ASSOC);
				if($db->getRowsCount()>0){
					for($k=0;$k<sizeof($sensors);$k++) {
						$sensorName=$sensors[$k]['sensorName'];
						if(strcmp(sanitizeStringToCompare($sensorName),sanitizeStringToCompare($sensorNameToSearch))==0) {
							$sensorId=$sensors[$k]['sensorId'];
							toggleSensorViaMobile($hubId,$sensorId,$isToArm);
						}
					}
					sendResponse("Failure","Room found but sensor not found on the target hub.");
				}
				sendResponse("Failure","Room found but sensor not found on the target hub.");
			}
		}
		sendResponse("Failure","Room not found on the target hub.");
	}
	sendResponse("Failure","Room not found on the target hub.");
}
function toggleSensorViaMobile($hubId,$sensorId,$isToArm) {
	$db = new MySQL();
	if($hubId<=0) {
		sendResponse("Failure","Invalid Hub");
	}
	$query="SELECT hubFCMTokenId,fcmToken,deviceOS FROM hubfcmtokens 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."' 
	ORDER BY dateReceived DESC LIMIT 1";
	$fcmObject=$db->executeQueryAndGetFirstRow($query);
	if($fcmObject===FALSE) {
		sendResponse("Failure","Hub is not enabled for Remote Operations");
	} else {
		if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
			sendResponse("Failure",$db->getErrorString());
		}	
		$fcmToken = $fcmObject->fcmToken;
		$deviceOS = $fcmObject->deviceOS;
		
		$dataArray = array(
			'remoteCommand' => "toggleSensor",
			'sensorId' => $sensorId,
			'isToArm' => $isToArm
		);
		$messageArray = array(
			'to' => $fcmToken,
			'data' => $dataArray,
			'priority' => "high"
		);
		
		$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
		if($sendNotificationResult===true) {
			sendResponse("Success","Command Sent");
		} else {
			sendResponse("Failure",$sendNotificationResult);
		}
	}
}
function sendIRCommandToHub($hubId,$irIndexId,$key,$value) {
	$db = new MySQL();
	if($hubId<=0) {
		sendResponse("Failure","Invalid Hub");
	}
	$query="SELECT hubFCMTokenId,fcmToken,deviceOS FROM hubfcmtokens 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."' 
	ORDER BY dateReceived DESC LIMIT 1";
	$fcmObject=$db->executeQueryAndGetFirstRow($query);
	if($fcmObject===FALSE) {
		sendResponse("Failure","Hub is not enabled for Remote Operations");
	} else {
		if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
			sendResponse("Failure",$db->getErrorString());
		}	
		$fcmToken = $fcmObject->fcmToken;
		$deviceOS = $fcmObject->deviceOS;
		$time=time();
		$dataArray = array(
			'remoteCommand' => "executeIRCommand",
			'irIndexId' => $irIndexId,
			'key' => $key,
			'value' => $value,
			'time' => $time
		);
		$messageArray = array(
			'to' => $fcmToken,
			'data' => $dataArray,
			'priority' => "high"
		);
		
		$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
		if($sendNotificationResult===true) {
			sendResponse("Success","Command Sent");
		} else {
			sendResponse("Failure",$sendNotificationResult);
		}
	}
}
function sendRefreshDataCommandToHub($hubId) {
	$db = new MySQL();
	if($hubId<=0) {
		$errorString="Invalid Hub";
		return $errorString;
	}
	$query="SELECT hubFCMTokenId,fcmToken,deviceOS FROM hubfcmtokens 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."' 
	ORDER BY dateReceived DESC LIMIT 1";
	$fcmObject=$db->executeQueryAndGetFirstRow($query);
	if($fcmObject===FALSE) {
		$errorString="Hub is not enabled for Remote Operations";
		return $errorString;
	} else {
		if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
			sendResponse("Failure",$db->getErrorString());
		}	
		$fcmToken = $fcmObject->fcmToken;
		$deviceOS = $fcmObject->deviceOS;
		$time=time();
		$dataArray = array(
			'remoteCommand' => "refreshData",
			'time' => $time
		);
		$messageArray = array(
			'to' => $fcmToken,
			'data' => $dataArray,
			'priority' => "high"
		);
		
		$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
		if($sendNotificationResult===true) {
			return true;
		} else {
			return $sendNotificationResult;
		}
	}
}
function sendFCMTokenRegisteredCommandToHub($hubId) {
	$db = new MySQL();
	if($hubId<=0) {
		sendResponse("Failure","Invalid Hub");
	}
	$query="SELECT hubFCMTokenId,fcmToken,deviceOS FROM hubfcmtokens 
	WHERE hubId='".mysqli_real_escape_string($db->mysql_link,$hubId)."' 
	ORDER BY dateReceived DESC LIMIT 1";
	$fcmObject=$db->executeQueryAndGetFirstRow($query);
	if($fcmObject===FALSE) {
		sendResponse("Failure","Hub is not registered for Remote Operations");
	} else {
		if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
			sendResponse("Failure",$db->getErrorString());
		}	
		$fcmToken = $fcmObject->fcmToken;
		$deviceOS = $fcmObject->deviceOS;
		$time=time();
		$dataArray = array(
			'remoteCommand' => "fcmTokenRegistered",
			'time' => $time
		);
		$messageArray = array(
			'to' => $fcmToken,
			'data' => $dataArray,
			'priority' => "high"
		);
		
		$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
		if($sendNotificationResult===true) {
			sendResponse("Success","Hub registered for Remote Operations.");
		} else {
			sendResponse("Failure","Hub not registered for Remote Operations. ".
			$sendNotificationResult);
		}
	}
}
function sendHubErrorToCustomers($serialNumber,$errorLevel,$errorMessage) {
	$db = new MySQL();
	$query = "SELECT hubId,simpleName FROM hubs WHERE 
	serialNumber='".mysqli_real_escape_string($db->mysql_link,$serialNumber)."';";
	$hub = $db->executeQueryAndGetFirstRow($query);
	if($hub===FALSE) {
		sendResponse("Failure","Invalid Hub.");
	}
	$query = "SELECT customerId FROM hubs WHERE 
	hubId='".mysqli_real_escape_string($db->mysql_link,$hub->hubId)."';";
	$customerId=$db->getFirstRowFirstColumn($query);
	if($customerId=='' || $customerId===FALSE) {
		$errorString = "Invalid Customer.";
	}
	$query="SELECT customerFCMTokenId,fcmToken,deviceOS FROM customerfcmtokens 
	WHERE customerId='".mysqli_real_escape_string($db->mysql_link,$customerId)."' 
	ORDER BY dateReceived DESC LIMIT 1";
	$fcmObject=$db->executeQueryAndGetFirstRow($query);
	if($fcmObject===FALSE) {
		$errorString .= " Customer is not enabled for Remote Notifications";
	} else {
		if($fcmObject->fcmToken == '' || $fcmObject->fcmToken === FALSE){
			$errorString .= " ".$db->getErrorString();
		}
		
		$fcmToken = $fcmObject->fcmToken;
		$deviceOS = $fcmObject->deviceOS;

		$notificationArray = array(
			"title" => $errorLevel." in Hub: ".$hub->simpleName.", please make a note.",
			"body" => "Your Hub with name: ".$hub->simpleName.
				" has an error. Error: ".$errorMessage
		);
		$dataArray = array(
			'remoteCommand' => "hubError",
			'hubId' => $hub->hubId,
			'simpleName' => $hub->simpleName,
			'errorLevel' => $errorLevel,
			'errorMessage' => $errorMessage
		);
		$messageArray = array(
			'to' => $fcmToken,
			'notification' => $notificationArray,			
			'data' => $dataArray,
			'priority' => "high"
		);
		$sendNotificationResult=sendPushNotificationToToken($fcmToken,$messageArray);
		if($sendNotificationResult===true) {
			sendResponse("Success","Notified Mobile App. ".$errorString);
		} else {
			sendResponse("Failure",$errorString);
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
function logIncomingRequest($hubId,$serialNumber,$commandToExecute,$parameterString) {
	try {
		$db = new MySQL();
		$query = "INSERT INTO logs(hubId,serialNumber,commandToExecute,parameterString,dateAdded) VALUES(
		'".mysqli_real_escape_string($db->mysql_link,$hubId)."',
		'".mysqli_real_escape_string($db->mysql_link,$serialNumber)."',
		'".mysqli_real_escape_string($db->mysql_link,$commandToExecute)."',
		'".mysqli_real_escape_string($db->mysql_link,$parameterString)."',
		'".time()."');";
		//$db->executeQuery($query);
		if($db->executeQuery($query)) {
		 	//sendResponse("Success","Query Logged.");
		} else {
		 	//sendResponse("Failure",$db->getErrorString());
		}
	} catch(Exception $e){
		echo $e->getMessage();
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

// function isValueUnique($tableName,$fieldName,$value) {
// 	$db = new MySQL();
// 	if(!empty($tableName) and !empty($fieldName) and !empty($value)){
// 		$query = "SELECT * FROM `".$tableName."` WHERE `".$fieldName."`='".mysqli_real_escape_string($db->mysql_link,$value)."'";
// 		if($db->hasRecords($query)){
// 			return false;
// 		}else{
// 			return true;
// 		}
// 	}
// }
?>