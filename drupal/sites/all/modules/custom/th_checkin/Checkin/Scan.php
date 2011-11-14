<?php

require_once 'Checkin/bao.php';

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
		$checkin = new Checkin_bao;
		$checkin->contact_id=$contact_id;
		if($checkin->is_in_building()){		
			$checkin->end_visit();
		} else {
			$checkin->start_visit($location);
		}
		if($redirect_to_contact){
			CRM_Core_Session::setStatus(implode($checkin->messages));
			CRM_Utils_System::redirect('/civicrm/contact/view?reset=1&cid='.$contact_id);
		}else{
			echo implode($checkin->messages);
		}
	}
	
}