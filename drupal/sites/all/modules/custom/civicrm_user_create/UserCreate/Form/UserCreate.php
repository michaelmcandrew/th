<?php
require_once('CRM/Core/Form.php');
require_once('CRM/Utils/Request.php');
require_once('CRM/Utils/Token.php');
require_once('UserCreate/BAO/UserCreate.php');


class UserCreate_Form_UserCreate extends CRM_Core_Form{
	
	var $contact_id = NULL;
	
	
	public function preProcess(){
		
		$this->contact_id=CRM_Utils_Request::retrieve('cid', 'Integer', $this );
		$this->message( "looking for contact with ID $contact_id...");
		$contact=civicrm_api('Contact', 'getsingle', array('version'=>3, 'id'=> $this->contact_id));
		
		if(!is_numeric($contact['contact_id'])){
			$this->message( 'Not a valid contact') ;
			$this->cannot_create_user = TRUE;
			return;	
		}
		
		//does this contact have an email?
		if(!strlen($contact['email'])){
			$this->message( 'No email addresss') ;
			$this->cannot_create_user = TRUE;		
			return;	
		}
		
		$this->email = $contact['email'];
		
		$this->message( "Going to create a user for the email address $this->email" ) ;
		
		//is there an account that is using this email address already?
		$result=db_query('SELECT mail FROM users WHERE mail = :mail', array(':mail'=>$this->email));
		
		if($result->rowCount()){
			$this->message( 'An account already exists for this user' ) ;
			$this->cannot_create_user = TRUE;		
			return;		
		}
		
		
		//get a username that hasn't been taken already
		$this->username = $original = "{$contact['first_name']} {$contact['last_name']}";
		do {
			if(isset($suffix)){
				$this->username = $original . ' ' . $suffix;
			} else {
				$this->username = $original;	
			}
			$result=db_query('SELECT mail FROM users WHERE name = :username', array(':username'=>$username));
			$suffix++;
		} while ($result->rowCount());
		$this->assign('messages',$this->messages);
		
		
	}
	
	public function BuildQuickForm(){
		if($this->cannot_create_user){
			$this->assign('message', "<b>Note:</b> Cannot create a user account for this contact.  Check that they 1) have an email address and 2) don't already have an account on the site.");
			return;
		}
        require_once 'CRM/Core/BAO/MessageTemplates.php';
        $this->templates = CRM_Core_BAO_MessageTemplates::getMessageTemplates( false );
        // $this->add('select', 'template', ts('Use Template'), array( '' => ts( '- select -' ) ) + $this->templates, false);
		
		$this->add('textarea', 'extra_text', ts('Extra text to include in email'),'rows=10, cols=100');        
		$this->addButtons(array(
			array (
				'type' => 'submit',
				'name' => ts('Approve application'),
				'isDefault' => true),
			)
		);
		
		$this->assign('messages',$this->messages);

	}

	
	
	public function postProcess(){
		global $base_url;
		$account->is_new=true;
		$this->set_user_password();
		$edit=array(
			'mail'=>$this->email,
			'name'=>$this->username,
			'pass'=>$this->password,
			'status'=>1,
		);
		
		require_once('CRM/Core/BAO/MessageTemplates.php');
		$params=array('id'=>46); //TODO
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
		UserCreate_BAO_UserCreate::process_application($this->contact_id, 'approved');
		user_save($account, $edit);
		CRM_Utils_Mail::send($mail_params);
		CRM_Core_Session::setStatus('Created user account (see user ID field below) and sent welcome email to '.$this->email);
		CRM_Utils_System::redirect('/civicrm/contact/view?cid='.$this->contact_id);
		
	}
		
	function message($text){
		$this->messages[]=$text;
		
	}
	
	private function set_user_password($length = 10) {

		// This variable contains the list of allowable characters for the
		// password. Note that the number 0 and the letter 'O' have been
		// removed to avoid confusion between the two. The same is true
		// of 'I', 1, and 'l'.
		$allowable_characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';

		// Zero-based count of characters in the allowable list:
		$len = strlen($allowable_characters) - 1;

		// Declare the password as a blank string.
		$pass = '';

		// Loop the number of times specified by $length.
		for ($i = 0; $i < $length; $i++) {

			// Each iteration, pick a random character from the
			// allowable string and append it to the password:
			$pass .= $allowable_characters[mt_rand(0, $len)];
		}

		$this->password = $pass;
	}
	
}

