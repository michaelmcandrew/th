<?php

require_once 'CRM/Core/form.php';
require_once 'Checkin/bao.php';
require_once 'Checkin/scan.php';
class Checkin_Form extends CRM_Core_Form{
    public function preProcess( ) {
	}
    public function buildQuickForm( ) {
        $this->add('text', 'contact_id', 'Contact ID');
        $this->addButtons(array(
			array (
				'type' => 'submit',
				'name' => ts('Scan'),
				'isDefault' => true),
			)
		);
	}

    public function formRule( ) {
		return TRUE;
		//TODO: check that this is a valid contact
	}

    public function postProcess( ) {
		Checkin_Scan::scan($this->getSubmitValue('contact_id'));
		}
}