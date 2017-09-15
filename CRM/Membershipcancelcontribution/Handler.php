<?php

class CRM_Membershipcancelcontribution_Handler {

    public static function post($op, $objectName, $id, &$objectRef) {
        $cancelled_status_id = civicrm_api3('OptionValue', 'getvalue', array('return' => 'value', 'name' => 'Cancelled', 'option_group_name' => 'contribution_status'));
        $refunded_status_id = civicrm_api3('OptionValue', 'getvalue', array('return' => 'value', 'name' => 'Refunded', 'option_group_name' => 'contribution_status'));
        $pending_status_id = civicrm_api3('OptionValue', 'getvalue', array('return' => 'value', 'name' => 'Pending', 'option_group_name' => 'contribution_status'));
				$in_progress_status_id = civicrm_api3('OptionValue', 'getvalue', array('return' => 'value', 'name' => 'In Progress', 'option_group_name' => 'contribution_status'));
        if ($objectName != 'Membership') {
            return;
        }

        $status = CRM_Member_BAO_MembershipStatus::getMembershipStatus($objectRef->status_id);
        if ($status['is_current_member'] || $status['name'] == 'Pending') {
            return;
        }
        $endDate = new DateTime($objectRef->end_date);

        //find contributions with status pending (2)
        $sql = "SELECT c.id, c.contribution_status_id FROM `civicrm_contribution` c
                INNER JOIN `civicrm_membership_payment` `mp` ON `c`.`id` = `mp`.`contribution_id`
                where `mp`.`membership_id` = %1
                and DATE(`c`.`receive_date`) >= DATE(%2) and DATE(`c`.`receive_date`) >= NOW()
                  ";
        $params = array();
        $params[1] = array($id, 'Integer');
        $params[2] = array($endDate->format("Y-m-d"), 'String');
        $dao = CRM_Core_DAO::executeQuery($sql, $params);
        while ($dao->fetch()) {
            // Only cancel the contribution when status is not cancelled or refunded.
            if ($dao->contribution_status_id != $cancelled_status_id && $dao->contribution_status_id != $refunded_status_id) {
                civicrm_api3('Contribution', 'Create', array('id' => $dao->id, 'contribution_status_id' => $cancelled_status_id));
            }
        }

      //find contributions with status In Progress (5)
      $sql = "SELECT c.id, c.contribution_status_id FROM `civicrm_contribution` c
                INNER JOIN `civicrm_membership_payment` `mp` ON `c`.`id` = `mp`.`contribution_id`
                where `mp`.`membership_id` = %1
                and c.contribution_status_id = %2
                  ";
      $params = array();
      $params[1] = array($id, 'Integer');
      $params[2] = array($in_progress_status_id, 'Integer');
      $dao = CRM_Core_DAO::executeQuery($sql, $params);
      while ($dao->fetch()) {
        $failedContribionParams = array('id' => $dao->id, 'contribution_status_id' => $cancelled_status_id);
        CRM_Contribute_BAO_Contribution::create($failedContribionParams);
        //civicrm_api3('Contribution', 'Create', array('id' => $dao->id, 'contribution_status_id' => $cancelled_status_id));
      }
    }

}