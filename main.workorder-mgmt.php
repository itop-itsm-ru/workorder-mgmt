<?php

require_once 'vendor/autoload.php';

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


    // Display lists
    MetaModel::Init_SetZListItems('details', array('description', 'target_class', 'action_type', 'filter', 'action_list')); // Attributes to be displayed for the complete details
    MetaModel::Init_SetZListItems('list', array('finalclass', 'target_class', 'action_type', 'description')); // Attributes to be displayed for a list
    // Search criteria
  }
}

class DashletWorkOrderCalendar extends Dashlet
{
  protected $sModuleUrlBase;

  public function __construct($oModelReflection, $sId)
  {
    $this->sModuleUrlBase = 'env-'.Utils::GetCurrentEnvironment().'/workorder-mgmt/';

    parent::__construct($oModelReflection, $sId);
    $this->aProperties['title'] = '';
    $this->aProperties['query'] = 'SELECT WorkOrder';
  }

  static public function GetInfo()
  {
    return array(
      'label' => Dict::S('UI:DashletWorkOrderCalendar:Label'),
      'icon' => 'env-'.Utils::GetCurrentEnvironment().'/workorder-mgmt/images/calendar.png',
      'description' => Dict::S('UI:DashletWorkOrderCalendar:Description'),
    );
  }

  public function Render($oPage, $bEditMode = false, $aExtraParams = array())
  {

    $sId = 'calendar';

    $oPage->add_linked_stylesheet('../'.$this->sModuleUrlBase.'fullcalendar/fullcalendar.min.css');
    // $oPage->add_linked_stylesheet('../'.$this->sModuleUrlBase.'style.css');
    // $oPage->add_linked_script('../'.$this->sModuleUrlBase.'fullcalendar/lib/jquery.min.js');
    $oPage->add_linked_script('../'.$this->sModuleUrlBase.'fullcalendar/lib/moment.min.js');
    $oPage->add_linked_script('../'.$this->sModuleUrlBase.'fullcalendar/fullcalendar.min.js');
    $oPage->add_linked_script('../'.$this->sModuleUrlBase.'fullcalendar/lang/ru.js');

    $sTitle = $this->aProperties['title'];
    $sQuery = $this->aProperties['query'];

    $oPage->add('<div id="dashlet-calendar" class="dashlet-content">');

    $sHtmlTitle = htmlentities(Dict::S($sTitle), ENT_QUOTES, 'UTF-8'); // done in the itop block
    if ($sHtmlTitle != '')
    {
      $oPage->add('<h1>'.$sHtmlTitle.'</h1>');
    }
    $oObjectSet = new DBObjectSet(DBObjectSearch::FromOQL($sQuery));
    $aEvents = array();
    while ($oObj = $oObjectSet->Fetch()) {

      $aEvent = array();
      $aEvent['title'] = $oObj->Get('name');
      $aEvent['start'] = $oObj->Get('start_date');
      $aEvent['end'] = $oObj->Get('end_date');
      $aEvent['url'] = ApplicationContext::MakeObjectUrl(get_class($oObj), $oObj->GetKey());
      $aEvents[] = $aEvent;
    }
    $jsonEvents = json_encode($aEvents);
    $oPage->add_ready_script(
<<<EOF
    $('#calendar').fullCalendar({
      lang: 'ru',
      events: $jsonEvents,
    })
EOF
    );
    $oPage->add('<div id="calendar"></div>');
    $oPage->add('</div>');
  }

  public function GetPropertiesFields(DesignerForm $oForm)
  {
    $oField = new DesignerTextField('title', Dict::S('UI:DashletObjectList:Prop-Title'), $this->aProperties['title']);
    $oForm->AddField($oField);

    $oField = new DesignerLongTextField('query', Dict::S('UI:DashletObjectList:Prop-Query'), $this->aProperties['query']);
    $oField->SetMandatory();
    $oForm->AddField($oField);
  }

}

?>