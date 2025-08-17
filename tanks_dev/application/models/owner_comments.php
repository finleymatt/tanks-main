<?php


Class Owner_comments_Model extends Model
{
	public $table_name = 'USTX.OWNER_COMMENTS';
	public $pks = array('ID');  // use ROWID as unique key
	//public $pks = array('OWNER_ID');
	public $parent_pks = array('Owners_mvw' => array('OWNER_ID'));
	//public $more_select = array('dbms_lob.substr(ROWID) RID');  // since owner_comments lacks unique ID

	public function get_owner_comment($owner_id, $comment_id) {
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/getownercomment?n_imparm_owner_id=' . $owner_id . '&n_inparm_owner_comment_id=' . $comment_id 
			. '&flag_outvar=[length%3D1%2Ctype%3Dchr%2Cvalue%3D]&msg_outvar=[length%3D500%2Ctype%3Dchr%2Cvalue%3D]&out_cur=';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$results = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($results, true);

		if(is_null($result) || $result['flag_outvar'] == 'N') {
			return array();
		} else {
			return $result['result']['out_cur'];
		}
	
	}
	
	public function insert($parent_ids, $data) {

		$owner_id = $parent_ids[0];
		$user_id = Session::instance()->get('SEPuserID');
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$comments = urlencode($data['comments']);
		$url = $domain . 'data/tank/postownercomment?n_inparm_owner_id=' . $owner_id . '&n_inparm_comments=' . $comments . '&vc_inparm_update_userid='
			. $user_id . '&c_inparm_commit_flag=Y&id_outvar=[length=8,type=int,value=]&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	public function update($id, $data) {
		//$this->db->set('user_modified', Session::instance()->get('UserID'));
		//$this->db->set('date_modified', 'sysdate', FALSE);

		//return(parent::update($ids, $data));
		$comment_id = $id[0];
		$user_id = Session::instance()->get('SEPuserID');
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/putownercomment?n_inparm_comments_id=' . $comment_id . '&vc_inparm_comments=' . $data['comments'] . '&vc_inparm_update_userid=' . $user_id 
			. '&c_inparm_commit_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	public function delete($ids) {
		$owner_id = $ids[0];
		$comment_id = $ids[1];
		$domain = str_replace('tanks.', '', url::fullpath(''));
		$url = $domain . 'data/tank/delownercomment?n_inparm_owner_id=' . $owner_id . '&n_inparm_comment_id=' . $comment_id 
			. '&c_inparm_commit_flag=Y&flag_outvar=[length=1,type=chr,value=]&msg_outvar=[length=500,type=chr,value=]';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);  // HOW LONG TO WAIT FOR A RESPONSE
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	protected function _validate_rules($vdata) {
		$vdata->add_rules('comments', 'required');
		return($vdata);
	}
}
