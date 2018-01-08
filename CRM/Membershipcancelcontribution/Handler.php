<?php

class CRM_Membershipcancelcontribution_Handler {

	private static $processed_ids = array();
	
	private static $currentMembershipData = array();
	
	public static function pre($op, $objectName, $id, &$data) {
		if ($objectName != 'Membership') {
			return;
		}
		if ($op != 'edit') {
			return;
		}
		$dao = CRM_Core_DAO::executeQuery("SELECT end_date, status_id FROM civicrm_membership WHERE id = %1", array(1=>array($id, 'Integer')));
		if ($dao->fetch()) {
			$end_date = false;
			if ($dao->end_date) {
				$end_date = new DateTime($end_date);
				$end_date = $end_date->format('Ymd');
			}
			self::$currentMembershipData[$id] = array(
				'status_id' => $dao->status_id,
				'end_date' => $end_date,
			);
		}
	}

	public static function post($op, $objectName, $id, &$objectRef) {
		if ($objectName != 'Membership') {
			return;
		}
		if ($op != 'edit') {
			return;
		}
		// Check whether status or end date is changed
		if (!isset(self::$currentMembershipData[$id])) {
			return;
		}
		if ($objectRef->status_id == self::$currentMembershipData[$id]['status_id'] && self::$currentMembershipData[$id]['end_date'] == $end_date) {
			return; // No change in status change or end date.
		}
		
		// We check whether we already have processed this membership.
		// Because the cancelletion of contribution we also update the membership status and we
		// will probably end in a loop.
		if (in_array($id, self::$processed_ids)) {
			return;
		}
		self::$processed_ids[] = $id;

		$cancelled_status_id = civicrm_api3('OptionValue', 'getvalue', array('return' => 'value', 'name' => 'Cancelled', 'option_group_name' => 'contribution_status'));
		$refunded_status_id = civicrm_api3('OptionValue', 'getvalue', array('return' => 'value', 'name' => 'Refunded', 'option_group_name' => 'contribution_status'));
		$pending_status_id = civicrm_api3('OptionValue', 'getvalue', array('return' => 'value', 'name' => 'Pending', 'option_group_name' => 'contribution_status'));
		$in_progress_status_id = civicrm_api3('OptionValue', 'getvalue', array('return' => 'value', 'name' => 'In Progress', 'option_group_name' => 'contribution_status'));
		if ($objectName != 'Membership') {
			return;
		}

		$status = CRM_Member_BAO_MembershipStatus::getMembershipStatus($objectRef -> status_id);
		if ($status['is_current_member'] || $status['name'] == 'Pending' || $status['name'] == 'Correctie') {
			return;
		}

		$endDate = new DateTime($objectRef -> end_date);

		// Cancel contributions with status progress.
		// Also cancel contributions which are in the future and after the end date of the membership.
		$sql = "SELECT c.id FROM `civicrm_contribution` c
            INNER JOIN `civicrm_membership_payment` `mp` ON `c`.`id` = `mp`.`contribution_id`
          	WHERE `mp`.`membership_id` = %1
            AND (
            	(DATE(`c`.`receive_date`) >= DATE(%2) and DATE(`c`.`receive_date`) >= NOW() and c.contribution_status_id NOT IN (%3, %4))
            	OR
            	(c.contribution_status_id = %5)
          	) 
		";
		$params = array();
		$params[1] = array($id, 'Integer');
		$params[2] = array($endDate -> format("Y-m-d"), 'String');
		$params[3] = array($cancelled_status_id, 'Integer');
		$params[4] = array($refunded_status_id, 'Integer');
		$params[5] = array($in_progress_status_id, 'Integer');
		$dao = CRM_Core_DAO::executeQuery($sql, $params);
		while ($dao -> fetch()) {
			// Due to performance we set the cancel status directly in the database.
			// We add a pre and post hook on the contribution.
			$cancelledContribionParams = array('id' => $dao -> id, 'contribution_status_id' => $cancelled_status_id);
			CRM_Utils_Hook::pre('edit', 'Contribution', $dao -> id, $cancelledContribionParams);
			
			// Now update the contribution
			CRM_Core_DAO::singleValueQuery("UPDATE civicrm_contribution SET contribution_status_id = %1 WHERE id = %2", array(1 => array($cancelled_status_id, 'Integer'), 2 => array($dao -> id, 'Integer')));

			// Find the contribution and run a post hook.
			$contribution = new CRM_Contribute_BAO_Contribution();
			$contribution -> id = $dao -> id;
			$contribution -> find(TRUE);
			CRM_Utils_Hook::post('edit', 'Contribution', $contribution -> id, $contribution);
		}
	}

}
