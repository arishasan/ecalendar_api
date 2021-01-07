<?php
/**
 * 
 */
class My_model extends CI_Model
{
	function get($table){
		$qry = $this->db->get($table);
		return $qry->result();
	}

	function get_orderBY($table,$orderby,$ordered){
		$qry = $this->db->order_by($orderby,$ordered)->get($table);
		return $qry->result();
	}

	function getWhere($table,$primary,$id,$is_md5,$single = 1){
		if($is_md5 == 1){
			if($single == 1)$qry = $this->db->where('MD5('.$primary.')',$id)->get($table)->row();
			else $this->db->where('MD5('.$primary.')',$id)->get($table)->result();
		}else{
			if($single == 1)$qry = $this->db->where($primary,$id)->get($table)->row();
			else $qry = $this->db->where($primary,$id)->get($table)->result();
		}
		return $qry;
	}

	function save($table,$data){
		$qry = $this->db->insert($table,$data);
		if($qry){
			return true;
		}else{
			return false;
		}
	}

	function update($table,$data,$where){
		$this->db->where($where);
		$qry = $this->db->update($table,$data);
		if($qry){
			return true;
		}else{
			return false;
		}
	}

	function getWhereField($table,$primary,$id,$field){
		$qry = $this->db->where($primary,$id)->get($table)->row();
		return $qry->$field;
	}

	function getWhereNotIN($table_get,$whereID,$whereNot,$whereTable){
		$qry = $this->db->where($whereID.' NOT IN(SELECT '.$whereNot.' FROM '.$whereTable.')')->get($table_get)->result();
		return $qry;
	}

	function getWhereNotINLimit($table_get,$whereID,$whereNot,$whereTable,$limit,$is_random){
		if($is_random == 1){
			$qry = $this->db->where($whereID.' NOT IN(SELECT '.$whereNot.' FROM '.$whereTable.')')->limit($limit)->order_by('RAND()')->get($table_get)->result();
			return $qry;
		}else{
			$qry = $this->db->where($whereID.' NOT IN(SELECT '.$whereNot.' FROM '.$whereTable.')')->limit($limit)->get($table_get)->result();
			return $qry;
		}
	}

	function getWhereIN($table_get,$whereID,$whereNot,$whereTable){
		$qry = $this->db->where($whereID.' IN(SELECT '.$whereNot.' FROM '.$whereTable.')')->get($table_get)->result();
		return $qry;
	}


	function deleteSingle($table,$where){
		$qry = $this->db->where($where)->delete($table);
		if($qry){
			return true;
		}else{
			return false;
		}
	}

	function empty_response(){
	    $response = array(
	    	'status' => 502,
	    	'error' => true,
	    	'message' => 'Thus field cannot be empty!.'
	    );

	    return $response;
	}

	function noProcess(){
		$response = array(
	    	'status' => 502,
	    	'error' => true,
	    	'message' => 'No data!'
	    );
	    return $response;
	}

	function is_authenticate($stat,$data = null){

		if($stat == 1){
	        $response = array(
		    	'status' => 200,
		    	'error' => false,
		    	'message' => 'Success.',
		    	'data' => $data,
		    );
	        return $response;
		}else{
		    $response = array(
		    	'status' => 502,
		    	'error' => true,
		    	'message' => 'Error occured, contact admin for further advice.',
		    	'data' => null
		    );
		    return $response;
		}
	}

	function is_authenticate_json($stat,$data = null){

		if($stat == 1){
	        $response = array(
		    	'status' => 200,
		    	'error' => false,
		    	'message' => 'Success.',
		    	'data' => json_encode(
		    		array(
			    		'inner' => json_encode($data)
			    	)
		    	),
		    );
	        return $response;
		}else{
		    $response = array(
		    	'status' => 502,
		    	'error' => true,
		    	'message' => 'Error occured, contact admin for further advice.',
		    	'data' => null
		    );
		    return $response;
		}
	}

	function is_authenticate_json_double($stat,$data = null,$data2 = null){

		if($stat == 1){
	        $response = array(
		    	'status' => 200,
		    	'error' => false,
		    	'message' => 'Success.',
		    	'data' => json_encode(
		    		array(
			    		'inner' => json_encode($data),
			    		'outer' => json_encode($data2)
			    	)
		    	),
		    );
	        return $response;
		}else{
		    $response = array(
		    	'status' => 502,
		    	'error' => true,
		    	'message' => 'Error occured, contact admin for further advice.',
		    	'data' => null
		    );
		    return $response;
		}
	}

}
?>