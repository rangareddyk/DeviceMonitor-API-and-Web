<?php
$serialNumber=$_REQUEST['sn'];
$event=$_REQUEST['event'];
$key=$_REQUEST['key'];
$value=$_REQUEST['value'];

if(!empty($serialNumber)) {
	//$url = 'https://157solutions.com/devmon/api/deviceMonitorAPI.php?commandToExecute=processDevicePolls&serialNumber='.$serialNumber.'&event='.$event.'&key='.$key.'&value='.$value;
	
	$messageArray = array();
	$messageArray['commandToExecute'] = "processDevicePolls";
	$messageArray['serialNumber'] = $serialNumber;	
	$messageArray['event'] = $event;	
	$messageArray['key'] = $key;
	$messageArray['value'] = $value;
	
	//firebase server url to send the curl request
	$url = 'https://157solutions.com/devmon/api/deviceMonitorAPI.php';
	//building headers for the request
	$headers = array(		
		'Content-Type: application/json'
	);

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
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageArray));					
	//finally executing the curl request
	$result = curl_exec($ch);
	print_r($result);
    if ($result === FALSE) {
		//echo "cURL Failed ";
		echo 0;
	} else {
		$resultObject=json_decode($result);		
        if($resultObject->Status == "Success"){
			echo 1;
		}else{
			echo 0;
		}
	}
	
} else {
	echo 0;
	//echo 'Please provide serial number';
}
?>