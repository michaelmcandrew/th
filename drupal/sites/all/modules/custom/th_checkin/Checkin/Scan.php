<?php

require_once 'Checkin/BAO/Checkin.php';

class Checkin_Scan{
	
	function scan($contact_id = NULL, $location = 'London') {
		//get params from the URL if necessary
		$redirect_to_contact = CRM_Utils_Request::retrieve('redirect_to_contact', 'Boolean', $this );
		if($redirect_to_contact==''){
			$redirect_to_contact=1;
		};
		if(!is_numeric($contact_id)){
			$contact_id = CRM_Utils_Request::retrieve('cid', 'String', $this );			
		}
		$checkin = new Checkin_BAO_Checkin;
		$checkin->set_contact($contact_id);
		if($checkin->is_in_building($location)){		
			$checkin->end_visit($location);
		} else {
			$checkin->start_visit($location);
		}
		if($redirect_to_contact){
			if(count($checkin->messages)){
				CRM_Core_Session::setStatus(implode($checkin->messages));				
			}
			if(is_numeric($contact_id)){
				CRM_Utils_System::redirect('/civicrm/contact/view?reset=1&cid='.$contact_id);
			} else {
				CRM_Utils_System::redirect('/civicrm?reset=1');				
			}
		}else{
		}
	}

	function check_out($contact_id = NULL, $location = 'London') {
		
		//get params from the URL if necessary
		$redirect_to_contact = CRM_Utils_Request::retrieve('redirect_to_contact', 'Boolean', $this );
		if($redirect_to_contact==''){
			$redirect_to_contact=1;
		};
		if(!is_numeric($contact_id)){
			$contact_id = CRM_Utils_Request::retrieve('cid', 'String', $this );			
		}
		$checkin = new Checkin_BAO_Checkin;
		$checkin->set_contact($contact_id);
		if($checkin->is_in_building($location)){		
			$checkin->end_visit($location);
		} else {
			$checkin->start_visit($location);
		}
		if($redirect_to_contact){
			CRM_Core_Session::setStatus(implode($checkin->messages));
			CRM_Utils_System::redirect('/civicrm/checkin/checkedin');
		}else{
		}
	}
	
}