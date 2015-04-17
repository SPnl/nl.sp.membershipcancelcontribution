<?php

class CRM_Membershipcancelcontribution_Handler {

    public static function post($op, $objectName, $id, &$objectRef) {
        if ($objectName != 'Membership') {
            return;
        }

        $status = CRM_Member_BAO_MembershipStatus::getMembershipStatus($objectRef->status_id);
        if ($status['is_current_member'] || $status['name'] == 'Pending') {
            return;
        }
        $endDate = new DateTime($objectRef->end_date);

        //find contributions with status pending (2)
        $sql = "SELECT c.id FROM `civicrm_contribution` c
                INNER JOIN `civicrm_membership_payment` `mp` ON `c`.`id` = `mp`.`contribution_id`
                where `mp`.`membership_id` = %1
                and DATE(`c`.`receive_date`) >= DATE(%2)
                  ";
        $params = array();
        $params[1] = array($id, 'Integer');
        $params[2] = array($endDate->format("Y-m-d"), 'String');
        $dao = CRM_Core_DAO::executeQuery($sql, $params);
        while ($dao->fetch()) {
            civicrm_api3('Contribution', 'Create', array('id' => $dao->id, 'contribution_status_id' => 3));
        }
    }

}