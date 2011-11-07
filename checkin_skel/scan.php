<?php
include 'bao.php';


function scan($contact_id, $location) {
	
	if(is_in_building($contact_id)){
		end_visit($contact_id);
	} else
		start_visit($contact_id, $location);
}


scan(1710, 'London');  


// $start='2011-11-07 18:38:50';
// $end=;

// 
// 
// 
// echo "$start\n$end\n";
