<?php

/**
 * 
 */
class M_user extends CI_Model
{
	
	function authenticate($dev_id){

		$this->db->where('device_id',$dev_id);
		$query = $this->db->get('users')->num_rows();

		if($query > 0){
			return true;
		}else{
			return false;
		}

	}

	function get_user($dev_id){
		$this->db->where('device_id',$dev_id);
		$tbl = $this->db->get('users');
		$jml = $tbl->num_rows();
		$data = $tbl->row();

		if($jml > 0){
			return $data;
		}else{
			return false;
		}
	}

	function get_userBYID($id){
		$this->db->where('id',$id);
		$tbl = $this->db->get('users');
		$jml = $tbl->num_rows();
		$data = $tbl->row();

		if($jml > 0){
			return $data;
		}else{
			return false;
		}
	}

	function create_user($data){
		if($this->db->insert('users',$data)){
			return true;
		}else{
			return false;
		}
	}

	function update_user($data,$where){
		$this->db->where($where);
		if($this->db->update('users',$data)){
			return true;
		}else{
			return false;
		}
	}

	function updateLastLogin($dev_id){
		$data = array(
			'last_login' => date('Y-m-d H:i:s')
		);
		$this->db->where('device_id',$dev_id);
		$tbl = $this->db->update('users',$data);

		if($tbl){
			return true;
		}else{
			return false;
		}
	}
	
}

?>