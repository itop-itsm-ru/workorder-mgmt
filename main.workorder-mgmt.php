<?php

require_once 'vendor/autoload.php';

// TODO: iBackgroundProcess переделать в iScheduledProcess?
class CheckScheduledActivity implements iBackgroundProcess
{
  public function GetPeriodicity()
  {
    return 60; // seconds
  }

  public function Process($iTimeLimit)
  {
    $aList = array();
    $sNow = date(AttributeDateTime::GetDateFormat());
    $aActionAttCodes = array('main' => 'action_date', 'preliminary' => 'pre_action_date');
    $sOQL = "SELECT ScheduledActivity WHERE status = 'active' AND ((action_date != '' AND action_date < '$sNow') OR (pre_action_enabled = '1' AND pre_action_date != '' AND pre_action_date < '$sNow'))";
    $oFilter = DBObjectSearch::FromOQL($sOQL);
    $oSet = new DBObjectSet($oFilter);
    while ((time() < $iTimeLimit) && ($oObj = $oSet->Fetch()))
    {
      foreach ($aActionAttCodes as $sActionType => $sAttCode)
      {
        if ($oObj->Get($sAttCode) != '' && $oObj->Get($sAttCode) < $sNow)
        {
          $aList[] = $oObj->Get('title').': ['.$sAttCode.']';
          // TODO: Делать отдельный триггер на каждую активность?
          $oTriggerSet = new DBObjectSet(
            DBObjectSearch::FromOQL("SELECT TriggerOnScheduledActivity WHERE target_class = 'ScheduledActivity' AND action_type = :action_type"),
            array(), // order by
            array('action_type' => $sActionType)
          );
          $aContextArgs = $oTriggerSet->Count() > 0 ? $this->GetContextArgs($oObj, $sActionType, $sAttCode) : array();
          while ($oTrigger = $oTriggerSet->Fetch())
          {
            $oTrigger->DoActivate(array_merge($oObj->ToArgs('this'), $aContextArgs));
          }
        }
      }
      // TODO: Заменить Reset на обновлнение соответствующей даты.
      $oObj->Reset($aActionAttCodes['main']);
      $oObj->Reset($aActionAttCodes['preliminary']);
      // Обновление объекта должно выполниться, чтобы пересчитались даты
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

  protected function GetContextArgs($oObj, $sActionType, $sAttCode)
  {
    // wo_start_date - время начала для наряда на работу
    // wo_end_date - время окончания наряда (если wo_duration == 0, wo_end_date = wo_start_date)
    //
    // action_date - время основного действия, установленного в активности (м.б. текущая дата)
    // next_action_date - время следующего основного действия в контексте текущего основного
    // main_action_date - время основгого дейтсвия  контексте предварительного
    $sFormat = AttributeDateTime::GetDateFormat();
    $aContextArgs = array();
    if ($sActionType == 'main')
    {
      $aContextArgs['this->next_action_date'] = $oObj->GetActionDate() ?: '-' ; // Текущее время не учитывается
      $aContextArgs['this->wo_start_date'] = $oObj->Get($sAttCode);
    }
    elseif ($sActionType == 'preliminary')
    {
      $iActionDate = AttributeDateTime::GetAsUnixSeconds($oObj->Get($sAttCode)) + $oObj->GetPreActionInterval();
      $aContextArgs['this->main_action_date'] = date($sFormat, $iActionDate);
      $aContextArgs['this->wo_start_date'] = $aContextArgs['this->main_action_date'];
    }
    $iWOEndDate = AttributeDateTime::GetAsUnixSeconds($aContextArgs['this->wo_start_date']) + $oObj->Get('wo_duration');
    $aContextArgs['this->wo_end_date'] = date($sFormat, $iWOEndDate);
    return $aContextArgs;
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
