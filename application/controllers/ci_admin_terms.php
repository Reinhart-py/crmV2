<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ci_admin_terms extends CI_Controller {
 function __construct() 
	{
    parent::__construct();
	 $this->load->library(array('Session','pagination','form_validation'));
	 $this->load->helper(array('form','url','directory','text'));
	 $this->load->model(array('ci_admin_model','comman_model'));
		 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		 header("Cache-Control: no-store, no-cache, must-revalidate"); 
		 header("Cache-Control: post-check=0, pre-check=0", false);
		 header("Pragma: no-cache");
		 header('Cache-Control: no-cache');
  
	}
	
	
	public function if_not_login($url = '')
    {
        if ($this->session->userdata('manager_id') == '') {
            if ($url != '') {
                redirect($url);
            } else {
                redirect('/manager/login');
            }
        } else {
            $data = array();
           // $data['logo'] = $this->ci_admin_model->getpostdata2(array('id'=>1),'','ci_logo');
           return $data;
        }
    } 
	
    public function is_user_login($url = '')
    {
        if ($this->session->userdata('manager_id') != '') {
            if ($url != '') {
                redirect($url);
            } else {
               redirect('manager/dashboard');
            }
        }
    }
	
	 private function _renamed($file)
    {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $sha = date('Y-m-d-His');
        $new = $sha . "." . $ext;
        return $new;
    }
   public function index()
	{
        
		$data=array();
		$data['info']=array(
			'title'=>'Manager - All Terms',
			'page_heading'=>'All Terms'
		);
		$data['info2']=$this->if_not_login();
		$data['category']= $this->comman_model->getAllTerms('','','ci_terms');
		$this->load->view('manager/terms/index',$data);
	}
	
	
     
	 //to add category
	public function add()
	{
		 
		  $data=array();
		  $data['info']=array(
			'title'=>'Manager - Categories',
			'page_heading'=>'Add Category'
		);
		$data['info2']=$this->if_not_login();		
	   	$data['category_list']= $this->comman_model->getAll(array('parent_id'=>0),'','ci_terms');
		  
	    if($this->input->server('REQUEST_METHOD')==='POST')
		{
			extract($_POST);
				
			$save= array(
			'title'=>$cat_name,
			'parent_id' =>$parent_id,
			'cat_type' =>$cat_type,	
			'created'=>date('Y-m-d H:i:s')
			);
			
			$cat_id=$this->comman_model->saveData('ci_terms',$save);
			redirect('manager/terms');
			
			
		}
	 $this->load->view('manager/terms/add', $data);
	}
	
	
	//edit category
     public function edit($did)
	 {
       	
		$data=array();
		$data['info']=array(
			'title'=>'Manager - Categories',
			'page_heading'=>'Edit Category'
		);
		$data['info2']=$this->if_not_login();
		if(empty($did))
	    {
	     redirect('manager/terms');
	    }
		$id = base64_decode($did);
		$data['category_list']= $this->comman_model->getAll(array('cat_type'=>'v_cat','parent_id'=>0),'','ci_terms');			
	    $data['list']=$this->comman_model->getpostdata2(array('id'=>$id),'','ci_terms');
		
		if($this->input->server('REQUEST_METHOD')==='POST')
		{
		    extract($_POST);	
			
			$cat_name= $this->security->xss_clean($cat_name);
			$save= array(
				'title'=>$cat_name,
				'parent_id' =>$parent_id,
				'cat_type' =>$cat_type,
				'status' =>$status
				
				);		
			
		   $response= $this->comman_model->updateData('id',$id,$save,'ci_terms');
			if($response)
			{
			$this->session->set_flashdata('message', 'Category updated successfully...');
		    	redirect('manager/terms');
			}
			
		}
        $this->load->view('manager/terms/edit', $data);
}
		
	
	 public function delete($cat_id)
	{
     	
		 $data=array();
		 $data['info']=array(
			'title'=>'Manager - Categories',
			'page_heading'=>'Delete Category'
		);
		$data['info2']=$this->if_not_login();
		 $data['product_list']=$this->ci_admin_model->getproduct_by_catid($cat_id);
		 if(!empty($data['product_list']))
		 {
			 foreach($data['product_list'] as $pli)
			 {
				 $vari_del=$this->ci_admin_model->deleteData('product_id',$pli['id'],'ci_variations');
				 $dir = "uploads/product_image/"; // Your Path to folder
				 $map = directory_map($dir); 
				 foreach($map as $li)
				 { 
					 if($li==$pli['product_image'])
					 {
					  unlink("uploads/product_image/".$pli['product_image']);
					 }
				 }
			 }
		 }
		$product_del=$this->ci_admin_model->deleteProduct_bycatid($cat_id);
		$cat_del=$this->ci_admin_model->deleteCategory($cat_id);
         
		  $this->session->set_flashdata('message', 'Category deleted Successfully...');
		
		       redirect('manager/terms');
			
			
	}

	public function getSubCatAjax()
	{

	if(isset($_POST['c_id'])&&!empty($_POST['c_id']))
	   {
	   
			$data['list']= $this->comman_model->getAll(array('parent_id'=>$_POST['c_id']),'','ci_terms');
			if(!empty($data['list'])){
				$str='<option value="">Choose Sub Category</option>';
					foreach($data['list'] as $list)
					{
						$str.="<option value=".$list['id'].">".$list['title']."</option>"; 
					}
				}else{
					$str='<option value="">Not Available</option>';
				}
	   
	   }else{
		$str='error';
	   }
	   echo $str;
	}
	
	
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */