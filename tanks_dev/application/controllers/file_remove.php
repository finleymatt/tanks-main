<?php
require_once(__DIR__ . '/file_functions.php');

$upload_id = $_POST['upload_id'];
$form_code = $_POST['form_code'];
$form_id = $_POST['form_id'];
$file_object_array['user'] = Session::instance()->get('UserID');
if(strpos($upload_id, ",")) {
	$ids = explode(",", $upload_id);
	foreach($ids as $id) {
		$response = remove_file($id, $form_code, $form_id);
	}
} else {
	$response = remove_file($upload_id, $form_code, $form_id);
}
$delete_info = json_decode($response, true);

$_SESSION['RemoveSuccess'] = 1;
$previous_url = $_SERVER['HTTP_REFERER'];
$upload_pos = strpos($previous_url, 'UploadSuccess');
$remove_pos = strpos($previous_url, 'RemoveSuccess');

// remove previous upload and delete information
if($upload_pos !== false) {
	$previous_url = substr($previous_url, 0, $upload_pos - 1);
}
if($remove_pos !== false) {
	$previous_url = substr($previous_url, 0, $remove_pos - 1);
}

$url = $previous_url . "?RemoveSuccess=" . $delete_info['flag_outvar'];

header('Location: ' . $url);exit;
/*function remove_file($upload_id, $form_code, $form_id) {
	$domain = str_replace('tanks.', '', url::fullpath(''));
	$url = $domain . "data/tank/delfileupload?n_inparm_upload_id=" . $upload_id . "&vc_inparm_form_code=" . $form_code . "&n_inparm_form_id=" . $form_id
	 	. "&c_inparm_commit_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}*/
