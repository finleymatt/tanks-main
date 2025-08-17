<?php
require_once(__DIR__ . '/file_functions.php');
$files = $_FILES["fileToUpload"];
foreach($files["error"] as $error) {
	if($error !== 0){
		echo "There is an error while uploading the file; Please select a valid file";
	}
}
//var_dump($_FILES["fileToUpload"]);exit;
// ************************************ { 2020-10-29 - RC } ************************************
// SET THE DEFAULTS
// *********************************************************************************************
$upload_directory = substr(__DIR__, 0, strrpos(__DIR__, "/")+1) . 'uploads';

$tmp_names = $_FILES["fileToUpload"]["tmp_name"];
$file_names = $_FILES["fileToUpload"]["name"];
$upload_data = array_combine($tmp_names, $file_names);
foreach($upload_data as $tmp_folder => $file_name) {
	move_uploaded_file($tmp_folder, $upload_directory.'/'.$file_name);
}

$cloud_instance = 'waste';  // OPTIONS: air, oit, sundry, waste, water
$file_objects = array();
$file_array = array();

$tags_array = array();
$tag_list = '';


$id = $_POST['id'];
$form_name = $_POST['form_name'];
$file_exist = json_decode(Controller::_instance('Facility')->file_exist($form_name, $id), true);
if($file_exist['flag_outvar'] == 'Y') {
        if(is_null($file_exist['msg_outvar'])) {
		$method = 'PUT';
	} else {
		$method = 'POST';
	}
} else {
	echo $file_exist['msg_outvar'];
}

foreach($file_names as $key => $file_name) {
$full_path = $upload_directory.'/'.$file_name;

// ************************************ { 2018-04-06 - RC } ************************************
// ASSIGN THE VALUES
// *********************************************************************************************
$collection = $_POST['form_name'];

// ************************************ { 2018-04-06 - RC } ************************************
// CREATE AN ARRAY OF ALL OF THE DATA
// *********************************************************************************************
$file_object_array = array();
$file_object_array['cloud'] = $cloud_instance;

$file_object_array['user'] = Session::instance()->get('UserID');
$file_object_array['file'] = $full_path;
$file_object_array['collection_name'] = $collection;
$file_object_array['tags'] = $tag_list;
$file_object_array['file_name'] = $file_name;
$file_object_array['file_text_name'] = preg_replace("~'~", '', preg_replace('~_~', ' ', $file_name));
$file_objects = array();
$file_objects[] = $file_object_array;
//var_dump($file_objects);
// ************************************ { 2018-04-10 - RC } ************************************
// LOOP THROUGH THE FILE OBJECT AND UPLOAD THE FILES
// *********************************************************************************************
foreach ($file_objects as $file) {

	$response = upload_to_rs($file);
	$rs_info = json_decode($response, true);
	// ************************************ { 2020-10-30 - RC } ************************************
	// REMOVE THE UPLOADED FILE IN WEB SERVER
	// *********************************************************************************************
	unlink($file['file']);
	
	// ************************************ { 2020-11-03 - RC } ************************************
	// SAVE THE FILE PATH URL TO DATABASE
	// *********************************************************************************************
	
	//$id = $_POST['id'];
	$form_topic = '';
	//$form_name = $_POST['form_name'];
	$upload_id = $_POST['upload_id'];
	$upload_name = $_POST['upload_name'];
	//$upload_name = 'TestResultsAttachment';
	$url = $rs_info['file_info']['file_path'];
	$user = Session::instance()->get('UserID'); 
	//$file_exist = json_decode(Controller::_instance('Facility')->file_exist($form_name, $id), true);

	/*if($file_exist['flag_outvar'] == 'Y') {
		if(is_null($file_exist['msg_outvar'])) {
			$method = 'PUT';
		} else {
			$method = 'POST';
		}
	} else {
		echo $file_exist['msg_outvar'];
	}*/
//echo $id . '<br>';
	save_file_url($method, $form_name, $id, $form_topic, $upload_name, $url, $user, $upload_id);
}
}

$_SESSION['UploadSuccess'] = 1;
$previous_url = $_SERVER['HTTP_REFERER'];
$upload_pos = strpos($previous_url, 'UploadSuccess');
$remove_pos = strpos($previous_url, 'RemoveSuccess');
// remove previous upload and delete information
if($upload_pos !== false) {
	$previous_url = substr($previous_url, 0, $upload_pos - 1);
} else if ($remove_pos !== false) {
	$previous_url = substr($previous_url, 0, $remove_pos - 1);
}

$url = $previous_url . "?UploadSuccess=" .$rs_info['success'];

header('Location: ' . $url);
