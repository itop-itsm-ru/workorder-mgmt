<?php

require_once('../../approot.inc.php');

try
{
  require_once(APPROOT.'/application/startup.inc.php');
  require_once(APPROOT.'/application/loginwebpage.class.inc.php');
  LoginWebPage::DoLogin(); // Check user rights and prompt if needed

  $sStartDate = utils::ReadParam('start', '');
  $sEndDate = utils::ReadParam('end', '');
  $sQuery = "SELECT WorkOrder WHERE start_date > '$sStartDate' AND start_date < '$sEndDate'";
  $oObjectSet = new DBObjectSet(DBObjectSearch::FromOQL($sQuery));
  $aEvents = array();
  while ($oObj = $oObjectSet->Fetch()) {
    $aEvent = array();
    $aEvent['title'] = $oObj->Get('name') ."\n".$oObj->Get('team_id_friendlyname')."\n".$oObj->Get('agent_id_friendlyname');
    $aEvent['start'] = $oObj->Get('start_date');
    $aEvent['end'] = $oObj->Get('end_date');
    $aEvent['url'] = ApplicationContext::MakeObjectUrl(get_class($oObj), $oObj->GetKey());
    $aEvents[] = $aEvent;
  }
  $jsonEvents = json_encode($aEvents);
  echo $jsonEvents;
}
catch (Exception $e)
{
  echo $e->getMessage();
}

?>