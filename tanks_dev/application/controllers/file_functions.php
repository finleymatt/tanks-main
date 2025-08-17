<?php

// ************************************ { 2020-12-08 - RC } ************************************
// DELETE THE FILE PATH URL FROM DATABASE
// *********************************************************************************************
function remove_file($upload_id, $form_code, $form_id) {
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
}

// ************************************ { 2020-12-08 - RC } ************************************
// SAVE THE FILE PATH URL TO DATABASE
// *********************************************************************************************
function save_file_url($method, $form_code, $id, $form_topic, $upload_name, $url, $user, $upload_id) {
	$domain = str_replace('tanks.', '', url::fullpath(''));
	if($method == 'POST') {
		$url = $domain . "data/tank/postfileupload?vc_inparm_form_code=" . $form_code . "&n_inparm_form_id=" . $id . "&vc_inparm_form_topic_code=" . $form_topic
			. "&vc_inparm_upload_name=" . $upload_name . "&vc_inparm_upload_filepath=" . $url . "&vc_inparm_update_userid=" . $user
			. "&c_inparm_commit_flag=Y&id_outvar=[length=10,type=int,value=]&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]";
	} else {
		$url = $domain . "data/tank/putfileupload?n_inparm_upload_id=" . $upload_id . "&vc_inparm_form_topic_code=" . $form_topic . "&vc_inparm_upload_name=" . $upload_name
			. "&vc_inparm_upload_filepath=" . $url . "&vc_inparm_update_userid=" . $user
			. "&c_inparm_commit_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]";
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}

// ************************************ { 2018-04-10 - RC } ************************************
// TAKE THE FILE OBJECT AND SEND IT OVER TO THE RESOURCE SPACE UPLOAD API
// *********************************************************************************************
function upload_to_rs($file_object) {
	// ************************************ { 2018-04-06 - RC } ************************************
	// SET THE DEFAULTS
	// *********************************************************************************************
	// There is only production version of Resource Space Upload API
	$url = "https://service.web.env.nm.gov/api/resource_space_upload";
	$post = array();

	// ************************************ { 2018-04-06 - RC } ************************************
	// CREATE THE FILE OBJECT
	// *********************************************************************************************

	if (is_file($file_object['file'])) {
		$cfile = curl_file_create($file_object['file']);
	} else {
		exit;	
	}

	// ************************************ { 2018-04-18 - RC } ************************************
	// CREATE THE HEADERS
	// *********************************************************************************************
	$headers = array("Content-Type: multipart/form-data");

	// ************************************ { 2018-04-06 - RC } ************************************
	// BUILD OUT THE POST DATA ARRAY
	// *********************************************************************************************
	$post['cloud'] = $file_object['cloud'];
	$post['collection_name'] = $file_object['collection_name'];
	$post['file'] = $cfile;
	$post['file_name'] = $file_object['file_text_name'];
	$post['shareable'] = 1;
	$post['tags'] = $file_object['tags'];
	$post['user'] = $file_object['user'];

	// ************************************ { 2018-04-10 - RC } ************************************
	// PERFORM THE CURL REQUEST
	// *********************************************************************************************
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_USERAGENT, "API Client");

	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
