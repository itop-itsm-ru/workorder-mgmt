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

abstract class ActionCreateObject extends Action
{
  public static function Init()
  {
    $aParams = array
    (
      "category" => "core/cmdb",
      "key_type" => "autoincrement",
      "name_attcode" => "name",
      "state_attcode" => "",
      "reconc_keys" => array('name'),
      "db_table" => "priv_action_notification",
      "db_key_field" => "id",
      "db_finalclass_field" => "",
      "display_template" => "",
    );
    MetaModel::Init_Params($aParams);
    MetaModel::Init_InheritAttributes();

    // Display lists
    MetaModel::Init_SetZListItems('details', array('name', 'description', 'status', 'trigger_list')); // Attributes to be displayed for the complete details
    MetaModel::Init_SetZListItems('list', array('finalclass', 'name', 'description', 'status')); // Attributes to be displayed for a list
    // Search criteria
//    MetaModel::Init_SetZListItems('standard_search', array('name')); // Criteria of the std search form
//    MetaModel::Init_SetZListItems('advanced_search', array('name')); // Criteria of the advanced search form
  }
}

class ActionCreateFromTemplate extends ActionCreateObject
{
  public static function Init()
  {
    $aParams = array
    (
      "category" => "core/cmdb",
      "key_type" => "autoincrement",
      "name_attcode" => "name",
      "state_attcode" => "",
      "reconc_keys" => array('name'),
      "db_table" => "priv_action_notification",
      "db_key_field" => "id",
      "db_finalclass_field" => "",
      "display_template" => "",
    );
    MetaModel::Init_Params($aParams);
    MetaModel::Init_InheritAttributes();

    // Display lists
    MetaModel::Init_SetZListItems('details', array('name', 'description', 'status', 'trigger_list')); // Attributes to be displayed for the complete details
    MetaModel::Init_SetZListItems('list', array('finalclass', 'name', 'description', 'status')); // Attributes to be displayed for a list
    // Search criteria
//    MetaModel::Init_SetZListItems('standard_search', array('name')); // Criteria of the std search form
//    MetaModel::Init_SetZListItems('advanced_search', array('name')); // Criteria of the advanced search form
  }

  public function DoExecute($oTrigger, $aContextArgs)
  {
    if (MetaModel::IsLogEnabledNotification())
    {
      $oLog = new EventNotificationEmail();
      if ($this->IsBeingTested())
      {
        $oLog->Set('message', 'TEST - Notification sent ('.$this->Get('test_recipient').')');
      }
      else
      {
        $oLog->Set('message', 'Notification pending');
      }
      $oLog->Set('userinfo', UserRights::GetUser());
      $oLog->Set('trigger_id', $oTrigger->GetKey());
      $oLog->Set('action_id', $this->GetKey());
      $oLog->Set('object_id', $aContextArgs['this->object()']->GetKey());
      // Must be inserted now so that it gets a valid id that will make the link
      // between an eventual asynchronous task (queued) and the log
      $oLog->DBInsertNoReload();
    }
    else
    {
      $oLog = null;
    }

    try
    {
      $sRes = $this->_DoExecute($oTrigger, $aContextArgs, $oLog);

      if ($this->IsBeingTested())
      {
        $sPrefix = 'TEST ('.$this->Get('test_recipient').') - ';
      }
      else
      {
        $sPrefix = '';
      }
      $oLog->Set('message', $sPrefix.$sRes);

    }
    catch (Exception $e)
    {
      if ($oLog)
      {
        $oLog->Set('message', 'Error: '.$e->getMessage());
      }
    }
    if ($oLog)
    {
      $oLog->DBUpdate();
    }
  }

  protected function _DoExecute($oTrigger, $aContextArgs, &$oLog)
  {
    $sPreviousUrlMaker = ApplicationContext::SetUrlMakerClass();
    try
    {
      $this->m_iRecipients = 0;
      $this->m_aMailErrors = array();
      $bRes = false; // until we do succeed in sending the email

      // Determine recicipients
      //
      $sTo = $this->FindRecipients('to', $aContextArgs);
      $sCC = $this->FindRecipients('cc', $aContextArgs);
      $sBCC = $this->FindRecipients('bcc', $aContextArgs);

      $sFrom = MetaModel::ApplyParams($this->Get('from'), $aContextArgs);
      $sReplyTo = MetaModel::ApplyParams($this->Get('reply_to'), $aContextArgs);

      $sSubject = MetaModel::ApplyParams($this->Get('subject'), $aContextArgs);
      $sBody = MetaModel::ApplyParams($this->Get('body'), $aContextArgs);

      $oObj = $aContextArgs['this->object()'];
      $sMessageId = sprintf('iTop_%s_%d_%f@%s.openitop.org', get_class($oObj), $oObj->GetKey(), microtime(true /* get as float*/), MetaModel::GetEnvironmentId());
      $sReference = '<'.$sMessageId.'>';
    }
    catch(Exception $e)
    {
        ApplicationContext::SetUrlMakerClass($sPreviousUrlMaker);
        throw $e;
      }
    ApplicationContext::SetUrlMakerClass($sPreviousUrlMaker);

    if (!is_null($oLog))
    {
      // Note: we have to secure this because those values are calculated
      // inside the try statement, and we would like to keep track of as
      // many data as we could while some variables may still be undefined
      if (isset($sTo))       $oLog->Set('to', $sTo);
      if (isset($sCC))       $oLog->Set('cc', $sCC);
      if (isset($sBCC))      $oLog->Set('bcc', $sBCC);
      if (isset($sFrom))     $oLog->Set('from', $sFrom);
      if (isset($sSubject))  $oLog->Set('subject', $sSubject);
      if (isset($sBody))     $oLog->Set('body', $sBody);
    }

    $oEmail = new EMail();

    if ($this->IsBeingTested())
    {
      $oEmail->SetSubject('TEST['.$sSubject.']');
      $sTestBody = $sBody;
      $sTestBody .= "<div style=\"border: dashed;\">\n";
      $sTestBody .= "<h1>Testing email notification ".$this->GetHyperlink()."</h1>\n";
      $sTestBody .= "<p>The email should be sent with the following properties\n";
      $sTestBody .= "<ul>\n";
      $sTestBody .= "<li>TO: $sTo</li>\n";
      $sTestBody .= "<li>CC: $sCC</li>\n";
      $sTestBody .= "<li>BCC: $sBCC</li>\n";
      $sTestBody .= "<li>From: $sFrom</li>\n";
      $sTestBody .= "<li>Reply-To: $sReplyTo</li>\n";
      $sTestBody .= "<li>References: $sReference</li>\n";
      $sTestBody .= "</ul>\n";
      $sTestBody .= "</p>\n";
      $sTestBody .= "</div>\n";
      $oEmail->SetBody($sTestBody);
      $oEmail->SetRecipientTO($this->Get('test_recipient'));
      $oEmail->SetRecipientFrom($this->Get('test_recipient'));
      $oEmail->SetReferences($sReference);
      $oEmail->SetMessageId($sMessageId);
    }
    else
    {
      $oEmail->SetSubject($sSubject);
      $oEmail->SetBody($sBody);
      $oEmail->SetRecipientTO($sTo);
      $oEmail->SetRecipientCC($sCC);
      $oEmail->SetRecipientBCC($sBCC);
      $oEmail->SetRecipientFrom($sFrom);
      $oEmail->SetRecipientReplyTo($sReplyTo);
      $oEmail->SetReferences($sReference);
      $oEmail->SetMessageId($sMessageId);
    }

    if (isset($aContextArgs['attachments']))
    {
      $aAttachmentReport = array();
      foreach($aContextArgs['attachments'] as $oDocument)
      {
        $oEmail->AddAttachment($oDocument->GetData(), $oDocument->GetFileName(), $oDocument->GetMimeType());
        $aAttachmentReport[] = array($oDocument->GetFileName(), $oDocument->GetMimeType(), strlen($oDocument->GetData()));
      }
      $oLog->Set('attachments', $aAttachmentReport);
    }

    if (empty($this->m_aMailErrors))
    {
      if ($this->m_iRecipients == 0)
      {
        return 'No recipient';
      }
      else
      {
        $iRes = $oEmail->Send($aErrors, false, $oLog); // allow asynchronous mode
        switch ($iRes)
        {
          case EMAIL_SEND_OK:
            return "Sent";

          case EMAIL_SEND_PENDING:
            return "Pending";

          case EMAIL_SEND_ERROR:
            return "Errors: ".implode(', ', $aErrors);
        }
      }
    }
    else
    {
      if (is_array($this->m_aMailErrors) && count($this->m_aMailErrors) > 0)
      {
        $sError = implode(', ', $this->m_aMailErrors);
      }
      else
      {
        $sError = 'Unknown reason';
      }
      return 'Notification was not sent: '.$sError;
    }
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