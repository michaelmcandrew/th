<?php

require_once('CRM/Core/Form.php');
require_once('Checkin/BAO/Checkin.php');

class Checkin_Form_Checkedin extends CRM_Core_Form{
	// function buildQuickForm(){
	// 	
	// 	//         $this->add('email', 'Email', 'Contact ID');
	// 	//         $this->add('visiting_contact_id', 'Visiting', 'Contact ID');
	// 	// 	)
	// 	// );
	// }
	
    public function preProcess( ) {
		$checkin = new Checkin_BAO_Checkin;
		$this->assign('visitors', $checkin->get_all_visitors());
	}

    public function buildQuickForm( ) {

		$this->add('text', 'first_name', 'First name', array( 'size'=> 40 ));
		$this->add('text', 'last_name', 'Last name', array( 'size'=> 40 ));
        $this->add('text', 'email', 'Email', array( 'size'=> 60 ));
        $this->add('text', 'organization_name', 'Organisation', array( 'size'=> 60 ));
        $this->add('text', 'visiting_contact_id', 'Visiting');
        
		$this->addButtons(array(
			array (
				'type' => 'submit',
				'name' => ts('Check in'),
				'isDefault' => true),
			)
		);
		$this->addFormRule( array( 'Checkin_Form_Checkedin', 'formRule'), $this );
	}
    

    static function formRule( $fields, $files, $self ){
        $errors = array();

		$params = $fields;
		$params['version']=3;
		$params['contact_type']='Individual';

		$check = civicrm_api('Contact', 'get', $params);
		if($check['count']>1){
        	$errors['email'] = ts( 'Cannot check this person in.  Possible duplicates in database.  Please investigate before logging in.' );
        }
        // 
        return empty($errors) ? true : $errors;
    }

	public function postProcess( ) {
		$params = $this->getSubmitValues();
		
		$params['version']=3;
		$params['contact_type']='Individual';

		$contact = civicrm_api('Contact', 'get', $params);
		if($contact['count']==0){
			$contact = civicrm_api('Contact', 'create', $params);			
        }else{
		}

		$checkin = new Checkin_BAO_Checkin;
		$checkin->set_contact($contact['id']);
		if(!$checkin->is_in_building()){
			$checkin->start_visit('London');
			
		}
		CRM_Utils_System::redirect('/civicrm/checkin/checkedin');
		

	}		
}