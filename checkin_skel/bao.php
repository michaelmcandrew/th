<?php
require_once '/var/www/th/sites/default/civicrm.settings.php';
require_once 'CRM/Core/Config.php';
$config =& CRM_Core_Config::singleton( );

define('TH_VISIT_ACTIVITY_TYPE_ID',35);

function start_visit($contact_id) {
    
	//create activity: type=visit, status=in progress
	$params = array( 
      'source_contact_id' => $contact_id,
      //checkin activity
      'activity_type_id' => TH_VISIT_ACTIVITY_TYPE_ID,
      'subject' => 'London checkin  test',
      'activity_date_time' => date("Y-m-d H:i:s"),
      //status 'in progress'
      'status_id' => 7,
      //'normal' priority
      'priority_id' => 2,
      'version' => 3,
      );

    $result=civicrm_api("Activity","create",$params);
}

function end_visit($contact_id) {
	//foreach all open visits for this contact	
	//update activity: type=visit, status=complete
	$params = array( 
      'contact_id' => $contact_id,
      'activity_type_id' => TH_VISIT_ACTIVITY_TYPE_ID,
      'version' => 3
    );

      $result = civicrm_api( 'activity','get',$params );
      print_r($result);exit;
      foreach ($result['values'] as $key => $activity){  // Bug in API.  Peter - please report and record issue number here
          if($activity['activity_type_id']!=TH_VISIT_ACTIVITY_TYPE_ID){
               unset($result['values'][$key]);
           }
       }
       
      
     // foreach ($result['values'] as $key => $activity){
          
          
          //  $result=civicrm_api("Activity","update",$params);

         }
       
       // after the above foreach loop, the results array looks like it should look
       
       //cycke thorugh all results
       
       //if the status is in prgogress close and update
       
       
             // 
             //           $params = array( 
             //             'source_contact_id' => $contact_id,
             //             //checkin activity
             //             'activity_type_id' => 35,
             //             'subject' => 'London checkin  test',
             //             'activity_date_time' => date("Y-m-d H:i:s"),
             //             //status 'completed'
             //             'status_id' => 2,
             //             //'normal' priority
             //             'priority_id' => 2,
             //             'version' => 3,
             //             );
             // 
             //           $result=civicrm_api("Activity","create",$params);
             // }
          
      
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

function is_in_building($contact_id){
	//if this contact is in the building, return true, else return false
	//if this contact has an activity type=visit and status = in progress return true, else return false
	$params = array( 
      'contact_id' => $contact_id,
      //visit activity
      'activity_type_id' => 35,
      //status 'in progress'
      'status_id' => 7,
      'version' => 3,
      );
	$result=civicrm_api("Activity","getcount", $params);
    // echo $result;
	if ($result > 0) {
	    return True;
	}
	    else {
	        return False;
	    }
}
