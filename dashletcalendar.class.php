<?php

class DashletCalendar extends Dashlet
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
      'label' => Dict::S('UI:DashletCalendar:Label'),
      'icon' => 'env-'.Utils::GetCurrentEnvironment().'/workorder-mgmt/images/calendar.png',
      'description' => Dict::S('UI:DashletCalendar:Description'),
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