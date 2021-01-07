<?php

require APPPATH . 'libraries/REST_Controller.php';

/**
 * 
 */
class Ecalendar extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model(array('M_user','My_model','M_ecalendar'));
	}

	public function getEvents_get($community){
		$query = $this->M_ecalendar->get_events_byComGroupped($community,'event_date');
		
		// echo "<pre>";
		// print_r($query);

		$data = array();

		foreach ($query as $key => $value) {
			
			$temp = '';
			$query_2 = $this->M_ecalendar->get_events_byComDate($community,$value->event_date);
			$count = count($query_2);
			foreach ($query_2 as $key2 => $value2) {
				// $tmp = array(
				// 	$value2->event_name,
				// 	date("g:i A", strtotime($value2->event_time)),
				// 	$value2->name
				// );

				$temp .= '["'.$value2->event_name.'","'.date("g:i A", strtotime($value2->event_time)).'","'.$value2->name.'"]'.($key2 == $count-1 ? '' : ',');
			}

			$temporary = array(
				'date' => $value->event_date,
				'events' => '['.$temp.']'
			);

			array_push($data, $temporary);

		}


		if($data != null){
			
			$resp = $this->My_model->is_authenticate_json('1',$data);
			$this->response($resp);

		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}

	public function getHoliday_get(){
		$url = 'https://raw.githubusercontent.com/guangrei/Json-Indonesia-holidays/master/calendar.json';
		$json = file_get_contents($url);
		$decode = json_decode($json);

		// echo "<pre>";
		// print_r($decode);
		$data = array();
		foreach ($decode as $key => $value) {
			// echo $key;
			// echo "<br/>";
			// echo $value->deskripsi;

			$tahun = substr($key, 0,4);
			$bulan = substr($key, 4,2);
			$hari = substr($key, 6,2);
			$susun = $tahun.'-'.$bulan.'-'.$hari;

			if($susun == 'crea-te-d-'){}else{

				$temp = array(
					'date' => $susun,
					'desc' => (!empty($value->deskripsi) ? $value->deskripsi : '-')
				);

				array_push($data, $temp);

			}

		}

		if($data != null || !empty($data)){
		
			$resp = $this->My_model->is_authenticate_json('1',$data);
			$this->response($resp);

		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
		// echo "<pre>";
		// print_r($data);


	}

	public function getNakama_get($community){
		$data = $this->M_ecalendar->getNakama_byCOM($community);
		
		if($data != null){
			
			$resp = $this->My_model->is_authenticate_json('1',$data);
			$this->response($resp);

		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}

	public function save_event_post(){
		if($this->post()){

			$array = array(
				'event_name' => $this->post('event_name'),
				'event_date' => date('Y-m-d',strtotime($this->post('event_date'))),
				'event_time' => date('H:i:s',strtotime($this->post('event_time'))),
				'stat' => 0,
				'event_by' => $this->post('user_id')
			);

			if($this->M_ecalendar->save_plan($array)){
				$resp = $this->My_model->is_authenticate('1',$array);
				$this->response($resp);
			}else{
				$resp = $this->My_model->noProcess();
				$this->response($resp);
			}


		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}

	public function getEventsList_get($community){
		$data = $this->M_ecalendar->getEventsList_ByCOM($community);
		
		if($data != null){
			
			$resp = $this->My_model->is_authenticate_json('1',$data);
			$this->response($resp);

		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}

	public function getEventsListByUID_get($id){
		$data = $this->M_ecalendar->getEventsList_ByUID($id);
		
		if($data != null){

			$data2 = array(
				'cancel' => $this->M_ecalendar->getDataEventsByStat($id,2),
				'publish' => $this->M_ecalendar->getDataEventsByStat($id,1),
			);
			
			$resp = $this->My_model->is_authenticate_json_double('1',$data,$data2);
			$this->response($resp);

		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}

	public function getVotedUsersByEventID_get($id){
		$data = $this->M_ecalendar->getVotedUsersEVENTID($id);
		if($data != null){
			
			$data2 = array(
				'no' => $this->M_ecalendar->getVotedUserByStatEventID($id,0),
				'yes' => $this->M_ecalendar->getVotedUserByStatEventID($id,1),
			);

			$resp = $this->My_model->is_authenticate_json_double('1',$data,$data2);
			$this->response($resp);

		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}

	public function doVote_post(){
		if($this->post()){

			$array = array(
				'event_id' => $this->post('event_id'),
				'user_id' => $this->post('user_id'),
				'vote_date' => date('Y-m-d H:i:s'),
				'vote_stat' => $this->post('vote_stat')
			);

			if($this->M_ecalendar->save_do_vote($array,$this->post('event_id'),$this->post('user_id'))){
				$resp = $this->My_model->is_authenticate('1',$array);
				$this->response($resp);
			}else{
				$resp = $this->My_model->noProcess();
				$this->response($resp);
			}


		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}

	public function processHistory_post(){
		if($this->post()){

			if($this->M_ecalendar->process_PubDelCan($this->post('event_id'),$this->post('stuff'))){
				$resp = $this->My_model->is_authenticate('1');
				$this->response($resp);
			}else{
				$resp = $this->My_model->noProcess();
				$this->response($resp);
			}

		}else{
			$resp = $this->My_model->noProcess();
			$this->response($resp);
		}
	}

}

?>