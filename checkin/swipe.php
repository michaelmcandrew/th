<?php
include 'bao.php';

function scan($contact_id) {
	
	
	if(is_in_building($contact_id)){
		end_visit($contact_id)
	} else
		start_visit($contact_id);
	}
}
