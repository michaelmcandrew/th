<?php


define('TH_VISIT_ACTIVITY_TYPE_ID',34);
define('TH_ACTIVITY_STATUS_IN_PROGRESS',7);
define('TH_ACTIVITY_STATUS_COMPLETE',2);

class Checkin_bao{
	
	var $contact_id=NULL;
	
	var $messages=array();
	
	function start_visit($location) {
	
		//create activity: type=visit, status=in progress
		$params = array( 
	      'source_contact_id' => $this->contact_id,
	      //checkin activity
	      'activity_type_id' => TH_VISIT_ACTIVITY_TYPE_ID,
	      'subject' => $location, // TODO: probably actually want to record this as a custom data field contact reference for Tech Hub organisations
	      'activity_date_time' => date("Y-m-d H:i:s"),
	      'status_id' => TH_ACTIVITY_STATUS_IN_PROGRESS,
	      'version' => 3,
	      );

	    $result=civicrm_api("Activity","create",$params);
		if($result['is_error']==0){
			$this->messages[] = "checked in.";
		}
	
	}

	function end_visit() {
		//get all open visits for this contact
		$params = array( 
			'contact_id' => $this->contact_id,
			'activity_type_id' => TH_VISIT_ACTIVITY_TYPE_ID,
			'status_id' => TH_ACTIVITY_STATUS_IN_PROGRESS,
			'version' => 3
		);

	    $result = civicrm_api( 'activity','get',$params );

		// Bug in API.  Peter - please report and record issue number here
		foreach ($result['values'] as $key => $activity){  
			if($activity['activity_type_id']!=TH_VISIT_ACTIVITY_TYPE_ID OR $activity['status_id']!=TH_ACTIVITY_STATUS_IN_PROGRESS){
				unset($result['values'][$key]);
			}
		}
       
      
		foreach ($result['values'] as $visit_params){
			$visit_params['version']=3;
			$visit_params['status_id']=TH_ACTIVITY_STATUS_COMPLETE;
		
			$start = new DateTime($visit_params['activity_date_time']);
			$end = new DateTime();
			$seconds = $end->getTimestamp()-$start->getTimestamp();
			$visit_params['duration']=ceil($seconds/60);	
		
		
			$result=civicrm_api("Activity","update",$visit_params);
			if($result['is_error']==0){
				$this->messages[] = "checked out.";
			}
		}
	}


	function end_all_visits() {
		//find all in visits where status!='complete' and close them
	}

	function get_all_visitors($location) {
		//returns all people in the current $location
	}

	function get_all_members($location) {
		//returns all people in the current $location
	}

	function is_in_building(){
		//if this contact is in the building, return true, else return false
		//if this contact has an activity type=visit and status = in progress return true, else return false
		$params = array( 
	      'contact_id' => $this->contact_id,
	      //visit activity
	      'activity_type_id' => TH_VISIT_ACTIVITY_TYPE_ID,
	      //status 'in progress'
	      'status_id' => TH_ACTIVITY_STATUS_IN_PROGRESS,
	      'version' => 3,
	      );

	  $result = civicrm_api( 'activity','get',$params );

		// Bug in API.  Peter - please report and record issue number here
		foreach ($result['values'] as $key => $activity){  
			if($activity['activity_type_id']!=TH_VISIT_ACTIVITY_TYPE_ID OR $activity['status_id']!=TH_ACTIVITY_STATUS_IN_PROGRESS){
				unset($result['values'][$key]);
			}
		}

		$number_of_in_progress_visits=count($result['values']);
		if ($number_of_in_progress_visits > 0) {
		    return True;
		}else {
			return False;
		}
	}
}
