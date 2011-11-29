<?php
require_once('CRM/Core/Form.php');
require_once('CRM/Utils/Request.php');
require_once('CRM/Utils/Token.php');
require_once('UserCreate/BAO/UserCreate.php');


class UserCreate_Form_Reject extends CRM_Core_Form{
	
	var $contact_id = NULL;
	
	
	public function preProcess(){
		
		$this->contact_id=CRM_Utils_Request::retrieve('cid', 'Integer', $this );
		$this->message( "looking for contact with ID $contact_id...");
		$contact=civicrm_api('Contact', 'getsingle', array('version'=>3, 'id'=> $this->contact_id));
		
		if(!is_numeric($contact['contact_id'])){
			$this->message( 'Not a valid contact') ;
			$this->cannot_reject_user = TRUE;
			return;	
		}
		
		//does this contact have an email?
		if(!strlen($contact['email'])){
			$this->message( 'No email addresss') ;
			$this->cannot_reject_user = TRUE;		
			return;	
		}
		
		$this->email = $contact['email'];
		
		$this->message( "Going to reject user with the email address $this->email" ) ;
		
		//is there an account that is using this email address already?				
		
	}
	
	public function BuildQuickForm(){
		if($this->cannot_reject_user){
			$this->assign('message', "<b>Note:</b> Cannot reject this user.  Check that they have an email address.");
			return;
		}
		$this->add('textarea', 'extra_text', ts('Extra text to include in email'),'rows=10, cols=100');        
		$this->addButtons(array(
			array (
				'type' => 'submit',
				'name' => ts('Reject application'),
				'isDefault' => true),
			)
		);
		
		$this->assign('messages',$this->messages);

	}

	
	
	public function postProcess(){
		global $base_url;
		//create user
		
		require_once('CRM/Core/BAO/MessageTemplates.php');
		$params=array('id'=>47); //TODO
		$template = CRM_Core_BAO_MessageTemplates::retrieve($params);
		
		//do token replacement on the email message
		$body=$template->msg_html;
		CRM_Utils_Token::token_replace('user', 'username', $this->username, $template->msg_html);
		CRM_Utils_Token::token_replace('user', 'password', $this->password, $template->msg_html);
		CRM_Utils_Token::token_replace('domain', 'base_url', $base_url, $template->msg_html);
		CRM_Utils_Token::token_replace('message', 'extra_text', $this->getSubmitValue('extra_text'), $template->msg_html);
		$mail_params=array(
			'toEmail'=>$this->email,
			'subject'=>$template->msg_subject,
			'html'=>$template->msg_html,
			'from'=>'TechHub <info@techub.com>',
			
     	);
		UserCreate_BAO_UserCreate::process_application($this->contact_id, 'rejected');
		CRM_Utils_Mail::send($mail_params);
		CRM_Core_Session::setStatus('Sent rejection email to '.$this->email);
		CRM_Utils_System::redirect('/civicrm/contact/view?cid='.$this->contact_id);
		
	}
		
	function message($text){
		$this->messages[]=$text;
		
	}
	
	
}

