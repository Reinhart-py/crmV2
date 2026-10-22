<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Ci_admin_meetings extends CI_Controller {
function __construct()
	{
    parent::__construct();
	$this->load->library(array('Session','Pagination'));
	$this->load->helper(array('form','url','text','date'));
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


     public function index()
	{

        $data=array();
		$data['info']=array(
			'title'=>'Manager - Meetings',
			'page_heading'=>'All Meetings'
		);
		$data['info2']=$this->if_not_login();		    
        $data['product_list']= $this->ci_admin_model->getMeetings();
		$this->load->view('manager/meetings/index',$data);
	}



	//to add category

	public function add()

	{



		$data=array();
		$data['info']=array(
			'title'=>'Manager- Meetings',
			'page_heading'=>'Add Meeting'
		);
		$data['info2']=$this->if_not_login();
		if($this->input->server('REQUEST_METHOD')==='POST')
		{
				extract($_POST);
				$list= array(				
				'lead_id'=>$lead_id,                
                'a1'=>$a1,
                'a2'=>$a2,
                'a3'=>$a3,
                'poc'=>$poc,
				'mobile'=>$mobile,				
                'm_time'=>$meeting_time,
                'm_date'=>db_date($meeting_date),
				'remark'=>$remark,			
				'address'=>$address,               			
                'created_by'=>$this->session->userdata('manager_id'),				
				'created'=>date('Y-m-d')
				);
				$this->session->set_flashdata('message', 'Meeting has been added successfully!');
				$this->comman_model->saveData('ci_meetings',$list);				
				redirect('manager/meetings');

       }

        $this->load->view('manager/meetings/add',$data);
	}

		//edit category

     public function edit($id='')

	 {



		$data=array();
		$data['info']=array(
			'title'=>'Manager- Meetings',
			'page_heading'=>'Edit Meeting'
		);
		$data['info2']=$this->if_not_login();
		$id= base64_decode($id);

		$data['list']=$this->comman_model->getAll(array('id'=>$id),'','ci_meetings','first');
		if(empty($data['list'])){
				$this->session->set_flashdata('error_message', 'Not allowed');
			   redirect('manager/meetings');
		}
		
			   
			 if($this->input->server('REQUEST_METHOD')==='POST')

			{

				extract($_POST);

				$list= array(					
					'lead_id'=>$lead_id,                
                    'a1'=>$a1,
                    'a2'=>$a2,
                    'a3'=>$a3,
                    'poc'=>$poc,
                    'mobile'=>$mobile,				
                    'm_time'=>$meeting_time,
                    'm_date'=>db_date($meeting_date),
                    'remark'=>$remark,			
                    'address'=>$address,               			
                    'created_by'=>$this->session->userdata('manager_id')	
										
					);

				$response= $this->comman_model->updateData('id',$id,$list,'ci_meetings');

				if($response)
					{
						$this->session->set_flashdata('message', 'Meeting has been updated successfully!');
						redirect('manager/meetings');
					}

			}

		    $this->load->view('manager/meetings/edit',$data);

	   }


	  


}



/* End of file welcome.php */

/* Location: ./application/controllers/welcome.php */