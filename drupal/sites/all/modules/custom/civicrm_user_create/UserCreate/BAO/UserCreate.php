<?php
class UserCreate_BAO_UserCreate{
	function process_application($contact_id, $decision){
		$activity_query=CRM_Core_DAO::executeQuery("SELECT id FROM civicrm_activity WHERE activity_type_id=33 AND status_id=7 AND source_contact_id=".$contact_id);
		$activity_query->fetch();
		$activity_id=$activity_query->id;
		$activity=civicrm_api('activity', 'getsingle', array('version'=>3, 'id'=>$activity_id));
		$activity['version']=3;
		$activity['subject']=$activity['subject']. " ({$decision})";
		$activity['details']=$activity['details']. " {$decision}";
		$activity['status_id']=2;
		civicrm_api('activity', 'update', $activity);
		if($decision=='approve'){
			
		}
	}
}