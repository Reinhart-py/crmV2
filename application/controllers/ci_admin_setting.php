<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Ci_admin_setting extends CI_Controller {
     function __construct() 
	{
    parent::__construct();
	$this->load->library(array('Session'));
	$this->load->helper(array('form','url','directory','text','price'));
	$this->load->model(array('comman_model','ci_admin_model','mail_model'));
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
            $data['logo'] = $this->comman_model->getpostdata2(array('id'=>1),'','ci_logo');
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
	
  public function logo_edit()
 {
  $data=array();
		$data['info']=array(
			'title'=>'Manager Logo update'
			);
		$data['info2']=$this->if_not_login();
		
		   $data['logo'] = $this->comman_model->getpostdata2(array('id'=>1),'','ci_logo');
          
			if($this->input->server('REQUEST_METHOD')==='POST')
			{
				extract($_POST);
				if(!empty($_FILES['logo_image']['name']))
		     	{
					$config['upload_path']='uploads/logo/';
					$config['allowed_types']='jpg|jpeg|png|gif';
					//$config['max_size']=0; 
                  	$config['file_name']=$_FILES['logo_image']['name'];
					$this->load->library('upload',$config);
					$this->upload->initialize($config);
					
					if($this->upload->do_upload('logo_image'))
					{
						$uploadData=$this->upload->data();
						//print_r( $uploadData);
						$picture=$uploadData['file_name'];
					} 
	     		else
				{
				   $picture=$data['admin_logo']['logo_image'];
				}
			}
			else
			{
				$picture= $data['admin_logo']['logo_image'];
			}
			if(!empty($_FILES['logo_image']['name']))
		 	{	
			$save= array(
            'logo_image' => $picture,
			'logo_title'=>$logo_title,
			'status'=>$status,
			'created'=>date('Y-m-d H:i:s')
            );
			}
			else
			{
					$save= array( 
                'logo_title'=>$logo_title,
				'status'=>$status,
		    	'created'=>date('Y-m-d H:i:s')
			);
			}
			$response= $this->comman_model->updateData('id',1,$save,'ci_logo');
				if($response)
				{
					$this->session->set_flashdata('success_message', 'Logo is updated successfully...');
					redirect('ci_admin_setting/logo_edit');
					
				}
			
			}
    		
		$this->load->view('manager/setting/editlogo',$data);
 	}
		
}