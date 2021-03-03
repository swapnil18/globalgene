<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'libraries/REST_Controller.php');

class Processmanager extends REST_Controller {
    function __construct() {
        parent::__construct();
    }
    
    //list candidates with job applied on basis of date
    function list_candidates_post(){
    	$this->load->model('Applicants_model');
    	$start_date = $this->post('start_date');
    	$end_date = $this->post('end_date');
    	$data = [];
    	if ((strtotime($start_date)) > (strtotime($end_date)))
		{
		    $resposeData['status'] = 400;
		    $resposeData['message'] = "please enter valid date range";
		    $resposeData['result']  = [];
		} else {
			$resposeData['status'] = 200;
		    $resposeData['message'] = "success";
			$data = $this->Applicants_model->getApplicantList($start_date,$end_date);
			$resposeData['result'] = $data;
		}
    	
        $this->response($resposeData,200);
    }

    //add candidates
    function add_candidates_post(){
    	$this->load->model('Applicants_model');
    	$firstname = $this->post('firstname');
    	$lastname = $this->post('lastname');
    	$email_id = $this->post('email_id');
    	$contact_no = $this->post('contact_no');
    	$address = $this->post('address');
    	$no_of_experience = $this->post('no_of_experience');
    	try {
    		if(empty($firstname)) {
    			throw new Exception("Firstname should not empty");    			
    		} else if(empty($lastname)) {
    			throw new Exception("Lastname should not empty");    			
    		} else if(empty($no_of_experience)) {
    			throw new Exception("Please enter valid number of experience");    			
    		}else if(!filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
    			throw new Exception("Not valid email id");    			
    		} else if(!preg_match('/^[0-9]{10}+$/', $contact_no)) {
    			throw new Exception("Not valid contact no");    
    		}

	    	$data = [
	    		'firstname'=>$firstname,
	    		'lastname'=>$lastname,
	    		'email_id'=>$email_id,
	    		'contact_no'=>$contact_no,
	    		'address'=>$address,
	    		'no_of_experience'=>$no_of_experience
	    	];

    		//saving data into database
    		$isExisted = $this->Applicants_model->getApplicantByEmailId($email_id);
	    	if(!empty($isExisted)) {
	    		throw new Exception("user already existed");    		
	    	}
	   
	    	$insertId = $this->Applicants_model->insertApplicant($data);
	    	if($insertId > 0) {
	    		$status = "200";
	    		$message = "inserted successfully..!";
	    	} else {
	    		$status = "400";
	    		$message = "error in insert record";
	    	}
    	} catch (Exception $e) {
    		$status = "400";
	    	$message = $e->getMessage();
    	}

    	
    	$resposeData = ['status'=>$status,'message'=>$message];
        $this->response($resposeData,200);
    }
}
