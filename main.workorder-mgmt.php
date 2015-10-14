<?php

require_once 'vendor/autoload.php';

// TODO: iBackgroundProcess переделать в iScheduledProcess
class CheckScheduledActivity implements iBackgroundProcess
{
  public function GetPeriodicity()
  {
    return 10; // seconds
  }

  public function Process($iTimeLimit)
  {
    $aList = array();
    $sNow = date('Y-m-d H:i:s');
    $aActionAttCodes = array('main' => 'action_date', 'preliminary' => 'pre_action_date');
    $sOQL = "SELECT ScheduledActivity WHERE status = 'active' AND (action_date < '$sNow' OR (pre_action_enabled = '1' AND pre_action_date < '$sNow'))";
    $oFilter = DBObjectSearch::FromOQL($sOQL);
    $oSet = new DBObjectSet($oFilter);
    while ((time() < $iTimeLimit) && ($oObj = $oSet->Fetch())) {
      foreach ($aActionAttCodes as $sActionType => $sAttCode) {
        if ($oObj->Get($sAttCode) < $sNow) {
          $aList[] = $oObj->Get('name').': ['.$sAttCode.']';
          // TODO: Делать отдельный триггер на каждую активность?
          $oTriggerSet = new DBObjectSet(
            DBObjectSearch::FromOQL("SELECT TriggerOnScheduledActivity WHERE target_class = 'ScheduledActivity' AND action_type = :action_type"),
            array(), // order by
            array('activity_id' => $oObj->GetKey(), 'action_type' => $sActionType)
          );
          while ($oTrigger = $oTriggerSet->Fetch())
          {
            // TODO: Дополнительные аргументы для создания нарядов по шаблону
            $oTrigger->DoActivate($oObj->ToArgs('this'));
          }
          // TODO: Если время текущего основного действия и следующего предварительного
          // совпадают, то в контекст передаются обе настоящие даты. Предварительная дата
          // должна за собой тянуть соответствующую основную, о которой напоминаем.
          $oObj->Reset($sAttCode);
        }
      }
      if($oObj->IsModified())
      {
        CMDBObject::SetTrackInfo("Automatic - Scheduled Activity");
        $oMyChange = CMDBObject::GetCurrentChange();
        $oObj->DBUpdateTracked($oMyChange, true /*skip security*/);
      }
    }
    $iProcessed = count($aList);
    return "processed scheduled activity: $iProcessed, " .implode(", ", $aList);
  }
}

class TriggerOnScheduledActivity extends TriggerOnObject
{
  public static function Init()
  {
    $aParams = array
    (
      "category" => "core/cmdb,application",
      "key_type" => "autoincrement",
      "name_attcode" => "description",
      "state_attcode" => "",
      "reconc_keys" => array('description'),
      "db_table" => "priv_trigger_onscheduledactivity",
      "db_key_field" => "id",
      "db_finalclass_field" => "",
      "display_template" => "",
    );
    MetaModel::Init_Params($aParams);
    MetaModel::Init_InheritAttributes();
    MetaModel::Init_AddAttribute(new AttributeEnum("action_type", array("allowed_values"=>new ValueSetEnum('main, preliminary'), "sql"=>"action_type", "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array())));

    MetaModel::Init_SetZListItems('details', array('description', 'target_class', 'action_type', 'filter', 'action_list'));
    MetaModel::Init_SetZListItems('list', array('finalclass', 'target_class', 'action_type', 'description'));
  }
}

?>