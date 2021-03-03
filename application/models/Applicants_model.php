<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Applicants_model extends Common_model
{
	public function __construct()
	{
	}
	
	public function getApplicantList($start_date,$end_date) {
		$sql = "SELECT  ad.firstname,ad.lastname,ad.contact_no,ad.email_id,ad.no_of_experience,ad.address,j.name as job_applied FROM job_applications jp
		LEFT JOIN  applicant_details ad ON jp.applicant_id = ad.id
		LEFT JOIN  jobs j ON jp.job_id = j.id
		WHERE date(jp.added_date) <= '$start_date' AND date(jp.added_date) >= '$end_date' ";
		$result = $this->getByQuery($sql); 
		return $result;
	}

	public function insertApplicant($data) {
		if(!empty($data)) {
			$result = $this->insert('applicant_details',$data); 
			return $result;
		}
		return false;
	}
    
    public function getApplicantByEmailId($email) {
    	$result = [];
    	if(!empty($email)) {
    		$result = $this->getByField('applicant_details','email_id',$email); 
    	}
		
		return $result;
	}
}
?>