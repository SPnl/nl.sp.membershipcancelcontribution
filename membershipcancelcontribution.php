<?php

require_once 'membershipcancelcontribution.civix.php';

function membershipcancelcontribution_civicrm_pre($op, $objectName, $id, &$data) {
	if ($objectName == 'Membership' && $op == 'edit') {
  	CRM_Membershipcancelcontribution_Handler::pre($op, $objectName, $id, $data);
	}
}

function membershipcancelcontribution_civicrm_post($op, $objectName, $id, &$objectRef) {
	if ($objectName == 'Membership' && $op == 'edit') {
  	CRM_Membershipcancelcontribution_Handler::post($op, $objectName, $id, $objectRef);
	}
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function membershipcancelcontribution_civicrm_config(&$config) {
  _membershipcancelcontribution_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function membershipcancelcontribution_civicrm_xmlMenu(&$files) {
  _membershipcancelcontribution_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function membershipcancelcontribution_civicrm_install() {
  _membershipcancelcontribution_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function membershipcancelcontribution_civicrm_uninstall() {
  _membershipcancelcontribution_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function membershipcancelcontribution_civicrm_enable() {
  _membershipcancelcontribution_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function membershipcancelcontribution_civicrm_disable() {
  _membershipcancelcontribution_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function membershipcancelcontribution_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _membershipcancelcontribution_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function membershipcancelcontribution_civicrm_managed(&$entities) {
  _membershipcancelcontribution_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function membershipcancelcontribution_civicrm_caseTypes(&$caseTypes) {
  _membershipcancelcontribution_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function membershipcancelcontribution_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _membershipcancelcontribution_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
