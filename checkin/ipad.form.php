<?php
include 'bao.php';
//show all checked in people
class FormIpadHome{
	
	function buildForm(){
		//gets all visitors
		get_all_visitors($location);
	
		//get checkout buttons for each visitor
		//assign jQueryMobile confirm checkout pop up for each vistor
	
		//get checkin button
	
	}
	
	function postProcess(){
		
		//work out who is leaving and check them out
		end_visit($contact_id)
		
	}
}

function FormCheckIn(){
	

		function buildForm(){

			//create all form elements
			
			//first name *
			//last name *
			//email *
			//org
			//visiting (autocomplete???) (org or ind)

		}

		function postProcess(){

			//check if they are in civicrm.  if so retreive them.  if not add them and retrieve their contact ID
			$contact_id = 
			//record visit (including assigning who they are visiting)
			start_visit($contact_id)
			
			//confirm and thankyou.  button to return to home page.  auto happens after 10 seconds
		}
	}

