<?php
	class Comman_model extends CI_Model {
	 function __construct()
		{
			parent::__construct();
			$this->load->database();
		} 
	public function getAll($array,$order=array(),$table='ci_posts',$first=''){
				$ret = array();
				if(!empty($array)){
					$data = $this->db->where($array);
				}
				if(!empty($order)){
					$this->db->order_by($order[0], $order[1]); 
				}	
				$data = $this->db->get($table);
				if($data->num_rows()>=1){
					foreach($data->result_array() as  $row){
						$ret[] = $row;	
					}
					if($first=='first'){
						$ret = $ret[0];	
					}
				}
					return $ret;
			} 
	public function getField($array,$order=array(),$table='ci_posts',$field=''){
				$ret = array();
				if(!empty($array)){
					$data = $this->db->where($array);
				}
				if(!empty($order)){
					$this->db->order_by($order[0], $order[1]); 
				}
				$data = $this->db->get($table);
				if($data->num_rows()>=1){
					foreach($data->result_array() as  $row){
						$ret[] = $row[$field];	
					}
				}
					return $ret;
			} 
			
	public function getFields($array,$order=array(),$table='ci_posts',$fields=array()){
				$ret = array();
				if(!empty($array)){
					$data = $this->db->where($array);
				}
				if(!empty($order)){
					$this->db->order_by($order[0], $order[1]); 
				}	
				$data = $this->db->get($table);
				if($data->num_rows()>=1){
					foreach($data->result_array() as  $k=>$row){
						$ret[] = $row[$fields[0]];	
						$ret[] = $row[$fields[1]];	
					}
				}
					return $ret;
			} 
		public function deleteByID($cond,$table){
			$this->db->delete($table,$cond);
		}
		public function updateData($key,$id,$data,$table){
			$fields = $this->db->list_fields($table);
			if (!empty($fields) && is_array($data)) {
				$data = array_intersect_key($data, array_flip($fields));
			}
			$this->db->where($key,$id);
			$this->db->update($table,$data);
			return true;
		}
		
		public function deleteData($key,$id,$table){
			$this->db->where($key,$id);
			$this->db->delete($table);
			return true;
		}
		public function saveData($table,$data){
			$fields = $this->db->list_fields($table);
			if (!empty($fields) && is_array($data)) {
				$data = array_intersect_key($data, array_flip($fields));
			}
			$this->db->insert($table,$data);
			return $this->db->insert_id();
		}
		
		public function getAI($db,$table){
				$return = array();
				$qdata = $this->db->query("SELECT `AUTO_INCREMENT` FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db' AND   TABLE_NAME   = '$table'");
				if($qdata->num_rows()==1){
					$return = $qdata->result_array();
				}
				return $return[0]['AUTO_INCREMENT'];
		}
		
		public function create_unique_slug($string,$table='ci_posts',$field='post_name',$key=NULL,$value=NULL)
		{
		$t =& get_instance();
		$slug = url_title($string);
		$slug = strtolower($slug);
		$i = 0;
		$params = array ();
		$params[$field] = $slug;
		if($key)$params["$key !="] = $value;
		while ($t->db->where($params)->get($table)->num_rows())
		{  
			if (!preg_match ('/-{1}[0-9]+$/', $slug ))
				$slug .= '-' . ++$i;
			else
				$slug = preg_replace ('/[0-9]+$/', ++$i, $slug );			 
			$params [$field] = $slug;
		}  
		return $slug; 
		}	
		public function getPostData($id){
			$data = array();
			$d = $this->db->get_where('ci_posts',array(
				'id' => $id
			));
			if($d->num_rows()==1){
				$fetch = $d->result_array();
				$data = $fetch[0];
			}
			return $data;
		}
		
		 public function getpostdata2($con,$order=array(),$table='')
			{
				$result= array();
				if(!empty($con))
				{
					$this->db->where($con);
				}
				if(!empty($order))
				{
					$this->db->order_by($order[0],$order[1]);
				}
				$data = $this->db->get($table);
				foreach($data->result_array() as $row)
				{
					$result[]= $row;
				}
				if(!empty($result)){
				return $result[0];
				}
				else
				{
				return;
				}
			}
		
		public function deletePost($key='id',$value='',$table=''){
			$this->db->where($key,$value);
			$this->db->delete($table); 
			return true;
		}	

		public function removeMeta($array=''){
			$this->db->where($array);
			$this->db->delete('ci_post_terms'); 
			return true;
		}

		public function getAllTerms($array,$order=array(),$table='ci_terms',$first='')
		{
			$result=array();
			$this->db->select(array('A.*','B.title as parent_title'));
			$this->db->from($table.' as A');			
			$this->db->join($table.' as B','A.parent_id=B.id','left');
			if(!empty($array)){
				$this->db->where($array);
			}
			if(!empty($order)){
				$this->db->order_by($order[0], $order[1]); 
			}				
			$data = $this->db->get();
			foreach($data->result_array() as $row)
			{
				$result[]= $row;
			}
		   return $result;
		}


}
	


?>