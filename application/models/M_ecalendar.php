<?php

/**
 * 
 */
class M_ecalendar extends CI_Model
{
	
	public function get_events_byComGroupped($community,$grouppedBy){
		$this->db->select('events.id,events.event_date');
		$this->db->join('users','users.id = events.event_by');
		
		if($community == 'FOUNDER'){}else{
			$this->db->where('users.organization',$community);
			$this->db->or_where('users.organization','FOUNDER');
		}

		$this->db->where('events.stat',1);
		$this->db->order_by('event_date','ASC');
		$this->db->order_by('event_time','ASC');
		$this->db->group_by($grouppedBy);
		$query = $this->db->get('events')->result();
		return $query;
	}

	public function get_events_byCom($community){
		$this->db->select('events.*,users.organization,users.name');
		$this->db->join('users','users.id = events.event_by');
		
		if($community == 'FOUNDER'){}else{
			$this->db->where('users.organization',$community);
			$this->db->or_where('users.organization','FOUNDER');
		}

		$this->db->where('events.stat',1);
		$this->db->order_by('event_date','ASC');
		$this->db->order_by('event_time','ASC');
		$query = $this->db->get('events')->result();
		return $query;
	}

	public function get_events_byComDate($community,$date){
		$this->db->select('events.*,users.organization,users.name');
		$this->db->join('users','users.id = events.event_by');
		
		if($community == 'FOUNDER'){}else{
			$this->db->where('users.organization',$community);
			$this->db->or_where('users.organization','FOUNDER');
		}

		$this->db->where('events.stat',1);
		$this->db->where('event_date',$date);
		$this->db->order_by('event_date','ASC');
		$this->db->order_by('event_time','ASC');
		$query = $this->db->get('events')->result();
		return $query;
	}

	public function getNakama_byCOM($community){
		
		if($community == 'FOUNDER'){}else{
			$this->db->where('organization',$community);
			$this->db->or_where('organization','FOUNDER');
		}

		$query = $this->db->get('users')->result();
		return $query;
	}

	function save_plan($data){
		if($this->db->insert('events',$data)){
			return true;
		}else{
			return false;
		}
	}

	function getEventsList_ByCOM($community){
		$this->db->select('events.*,users.name as `event_by_name`,users.organization,DATE_FORMAT(events.event_time, "%h:%i %p") as `time_conv`');
		$this->db->from('events');
		$this->db->join('users','events.event_by = users.id');
		$this->db->where('events.event_date >',date('Y-m-d'));
		if($community == 'FOUNDER'){}else{
			$this->db->where('users.organization',$community);
			$this->db->or_where('users.organization','FOUNDER');
		}
		$this->db->where('events.stat',0);
		$this->db->order_by('events.event_date','ASC');
		$this->db->order_by('events.event_time','ASC');
		$query = $this->db->get()->result();
		return $query;
	}

	function getEventsList_ByUID($id){
		$this->db->select('events.*,users.name as `event_by_name`,users.organization,DATE_FORMAT(events.event_time, "%h:%i %p") as `time_conv`');
		$this->db->from('events');
		$this->db->join('users','events.event_by = users.id');
		$this->db->where('users.id',$id);
		$this->db->order_by('events.event_date','ASC');
		$this->db->order_by('events.event_time','ASC');
		$query = $this->db->get()->result();
		return $query;
	}

	function getDataEventsByStat($id,$stat){
		$this->db->select('events.*,users.name as `event_by_name`,users.organization,DATE_FORMAT(events.event_time, "%h:%i %p") as `time_conv`');
		$this->db->from('events');
		$this->db->join('users','events.event_by = users.id');
		$this->db->where('users.id',$id);
		$this->db->where('events.stat',$stat);
		$this->db->order_by('events.event_date','ASC');
		$this->db->order_by('events.event_time','ASC');
		$query = $this->db->get()->num_rows();
		return $query;
	}

	function getVotedUsersEVENTID($id){
		$this->db->select('vote_events.*,users.name');
		$this->db->from('vote_events');
		$this->db->join('users','vote_events.user_id = users.id');
		$this->db->where('vote_events.event_id',$id);
		$query = $this->db->get()->result();
		return $query;
	}

	function getVotedUserByStatEventID($id,$stat){
		$this->db->select('vote_events.*,users.name');
		$this->db->from('vote_events');
		$this->db->join('users','vote_events.user_id = users.id');
		$this->db->where('vote_events.event_id',$id);
		$this->db->where('vote_stat',$stat);
		$query = $this->db->get()->num_rows();
		return $query;
	}

	function save_do_vote($data,$eventID,$userID){

		$check = $this->db->where('event_id',$eventID)->where('user_id',$userID)->get('vote_events');
		$rows_check = $check->num_rows();
		$data_check = $check->row();

		if($rows_check > 0){

			$this->db->where('id',$data_check->id);
			if($this->db->update('vote_events',$data)){
				return true;
			}else{
				return false;
			}

		}else{

			if($this->db->insert('vote_events',$data)){
				return true;
			}else{
				return false;
			}

		}

	}

	function process_PubDelCan($eventID,$act){

		if($act == 'publish'){
			$data = array(
				'stat' => 1
			);
			$this->db->where('id',$eventID);
			if($this->db->update('events',$data)){
				return true;
			}else{
				return false;
			}
		}else if($act == 'cancel'){
			$data = array(
				'stat' => 2
			);
			$this->db->where('id',$eventID);
			if($this->db->update('events',$data)){
				return true;
			}else{
				return false;
			}
		}else if($act == 'delete'){
			$this->db->where('event_id',$eventID)->delete('vote_events');

			$this->db->where('id',$eventID);
			if($this->db->delete('events')){
				return true;
			}else{
				return false;
			}
				
		}else{
			return false;
		}

	}
	
}

?>