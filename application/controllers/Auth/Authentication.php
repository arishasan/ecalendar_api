<?php

require APPPATH . 'libraries/REST_Controller.php';

/**
 * 
 */
class Authentication extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model(array('M_user','My_model'));
	}

	public function auth_login_post(){
		if($this->post()){

			// if($this->M_user->authenticate($this->post('device_id'))){
				$data = $this->M_user->get_user($this->post('device_id'));

				if(empty($data) || $data == false){
					$array = array(
						'name' => $this->post('name'),
						'device_id' => $this->post('device_id'),
						'registered_at' => date('Y-m-d H:i:s'),
						'last_login' => date('Y-m-d H:i:s'),
						'organization' => $this->post('organization')
					);
					$this->M_user->create_user($array);
					$data = $this->M_user->get_user($this->post('device_id'));
					$resp = $this->My_model->is_authenticate('1',$data);
					$this->response($resp);
				}else{
					$this->M_user->updateLastLogin($this->post('device_id'));
					$resp = $this->My_model->is_authenticate('1',$data);
					$this->response($resp);
				}

			// }else{
			// 	$resp = $this->My_model->is_authenticate('0');
			// 	$this->response($resp);
			// }

		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}

	public function save_profile_post(){
		if($this->post()){

			// if($this->M_user->authenticate($this->post('device_id'))){
				$data = $this->M_user->get_userBYID($this->post('id'));

				if(!empty($data) || $data != false){
					$array = array(
						'name' => $this->post('name'),
					);

					$where = array(
						'id' => $this->post('id')
					);

					$this->M_user->update_user($array,$where);
					$data = $this->M_user->get_userBYID($this->post('id'));
					$resp = $this->My_model->is_authenticate('1',$data);
					$this->response($resp);
				}else{
					$resp = $this->My_model->noProcess();
					$this->response($resp);
				}

			// }else{
			// 	$resp = $this->My_model->is_authenticate('0');
			// 	$this->response($resp);
			// }

		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}

	public function checkDevice_login_post(){
		if($this->post()){

			$data = $this->M_user->get_user($this->post('device_id'));

			if(empty($data) || $data == false){
				$resp = $this->My_model->noProcess();
				$this->response($resp);
			}else{
				$this->M_user->updateLastLogin($this->post('device_id'));
				$resp = $this->My_model->is_authenticate('1',$data);
				$this->response($resp);
			}

		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}



}

?>