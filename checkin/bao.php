<?php

function start_visit($contact_id) {
	//create activity: type=visit, status=in progress
}

function end_visit($contact_id) {
	//foreach all open visits for this contact	
	//update activity: type=visit, status=complete	
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
}