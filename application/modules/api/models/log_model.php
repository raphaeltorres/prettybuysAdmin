<?php
class Log_model extends CI_Model {

	public function __construct() {
		parent::__construct();
		$utctimestamp = $this->db->query("SELECT UTC_TIMESTAMP() as utctimestamp");
		$this->utctimestamp = $utctimestamp->row()->utctimestamp;
		
		$locale		= ( strlen($this->uri->segment(3)) > 2 ) ? $this->uri->segment(4) : $this->uri->segment(3);
		$dbPrefix	= $this->config->item('db_prefix');
		
		//load database based on locale
		$this->db	= $this->load->database($dbPrefix.$locale,TRUE);
	}
	
	public function getAllLogs() {
		$this->db->select('*')
		->from('api_logs')
		->join('api_clients','api_logs.log_client_id = api_clients.auth','inner')
		->order_by('log_id', 'asc');
		$query = $this->db->get();

		//user data exist
		if ($query->num_rows() > 0){
			$response['rc']					= 0;
			$response['success']			= true;
			$response['data']['loglist']	= $query->result();
			$response['log_query']			 = str_replace('\n',' ',$this->db->last_query());	
		}
		else{ //no record found  
			$err_message = ( $this->db->_error_message() ) ? $this->db->_error_message() : 'No Records Found.';
			$response['rc']			= 999;
			$response['success']	= false;
			$response['message'][]	= $err_message;
			$response['log_query']			 = str_replace('\n',' ',$this->db->last_query());	
		}
		return $response;
	}

	public function getLog($logid) {
		$this->db->select('*')
			->from('api_logs')
			->join('api_clients','api_logs.log_client_id = api_clients.auth','inner')
			->where('log_id', $logid);
			$query = $this->db->get('api_logs');

		//user data exist
		if ($query->num_rows() > 0){
			$response['rc']					= 0;
			$response['success']			= true;
			$response['data']['loginfo']	= $query->result();
			$response['log_query']			 = str_replace('\n',' ',$this->db->last_query());	
		}
		else{ //no record found  
			$err_message = ( $this->db->_error_message() ) ? $this->db->_error_message() : 'No Records Found.';
			$response['rc']			= 999;
			$response['success']	= false;
			$response['message'][]	= $err_message;
			$response['log_query']			 = str_replace('\n',' ',$this->db->last_query());	
		}
		return $response;
	}
	
	
	public function delLog($id) {
		$this->db->where('log_id', $id);
		$query = $this->db->get('api_logs');

		//user data exist
		if ($query->num_rows() > 0){
			$this->db->where('log_id', $id);
			$querys = $this->db->delete('api_logs');

			$return['rc'] 			= 0;
			$return['success'] 		= true;
			$return['message'][]	= 'Log id '.$id.' Successfully Removed';
			$response['log_query']			 = str_replace('\n',' ',$this->db->last_query());	
				 
		}
		else{ //userdata don't exist	 

			$return['rc'] 			= 0;
			$return['success'] 		= true;
			$return['message'][]	= 'Log id '.$id.' does not exist.';
			$response['log_query']			 = str_replace('\n',' ',$this->db->last_query());	
		}		

		return $return;
	}	



	public function addLog($data) {
		//sanitized data
		$data = $this->security->xss_clean($data);

		//insert data
		$query = $this->db->insert('api_logs', $data);
		if ( $this->db->affected_rows() > 0 ){
			$response['rc']			= 0;
			$response['message'][]	= 'Log has been successfully added.';
			$response['log_query']			 = str_replace('\n',' ',$this->db->last_query());	
		}
		else{
			$response['rc']			= 999;
			$response['message'][]	= 'Failed to add log.';
			$response['log_query']			 = str_replace('\n',' ',$this->db->last_query());	
		}
		
		return $response;
	}
		
	public function editLog($id,$data)
	{	
		//sanitized data
		$data = $this->security->xss_clean($data);
		
		$this->db->where('log_id', $id);
		$this->db->update('api_logs', $data); 
		
		if ( $this->db->affected_rows() > 0 ){
			$response['rc']			= 0;
			$response['success']	= true;
			$response['message'][]	= 'log has been successfully modified.';
			$response['message'][]	= $data;
			$response['log_query']			 = str_replace('\n',' ',$this->db->last_query());	
		}
		else{
			$response['rc']			= 999;
			$response['success']	= false;
			$response['message'][]	= 'Failed to edit log.';
			$response['message'][]	= $data;
			$response['log_query']			 = str_replace('\n',' ',$this->db->last_query());	
		}
		return $response;
	}
	

	
	
// end of the log model

}
?>