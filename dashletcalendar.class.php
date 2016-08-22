<?php

class DashletCalendar extends Dashlet
{
    protected $sModuleUrlBase;
    protected $iEventResourcesCount = 3;

    public function __construct($oModelReflection, $sId)
    {
        parent::__construct($oModelReflection, $sId);
        $this->aProperties['title'] = Dict::S('UI:WorkOrderCalendar:Title');
        $this->aProperties['default_view'] = 'month';
        $this->aProperties['agenda_day'] = false;
        $this->aProperties['agenda_week'] = false;

        $this->aProperties['query'] = 'SELECT WorkOrder';
        $this->aProperties['start_attr'] = 'start_date';
        $this->aProperties['end_attr'] = 'end_date';
        $this->aProperties['title_attr'] = 'name';
        $this->aProperties['description_attr'] = '';
        $this->aProperties['color'] = 'blue';

//        $this->aProperties['event_resources'] = array();
//        $this->aProperties['event_resources'][] = array('query' => 'SELECT Incident', 'start_attr' => 'start_date', 'end_attr' => 'end_date', 'color' => 'green');

        $this->sModuleUrlBase = 'env-' . Utils::GetCurrentEnvironment() . '/workorder-mgmt/';
    }

    static public function GetInfo()
    {
        return array(
            'label' => Dict::S('UI:DashletCalendar:Label'),
            'icon' => 'env-' . Utils::GetCurrentEnvironment() . '/workorder-mgmt/images/timetable32.png',
            'description' => Dict::S('UI:DashletCalendar:Description'),
        );
    }

    public function Render($oPage, $bEditMode = false, $aExtraParams = array())
    {

        $oPage->add_linked_stylesheet('../' . $this->sModuleUrlBase . 'fullcalendar/fullcalendar.min.css');
        $oPage->add_linked_stylesheet('../' . $this->sModuleUrlBase . 'fullcalendar/custom.css');
        // $oPage->add_linked_script('../'.$this->sModuleUrlBase.'fullcalendar/lib/jquery.min.js');
        $oPage->add_linked_script('../' . $this->sModuleUrlBase . 'fullcalendar/lib/moment.min.js');
        $oPage->add_linked_script('../' . $this->sModuleUrlBase . 'fullcalendar/fullcalendar.min.js');
        $oPage->add_linked_script('../' . $this->sModuleUrlBase . 'fullcalendar/lang/ru.js');

        $oFilter = DBObjectSearch::FromOQL($this->aProperties['query']);
        $sFilter = $oFilter->serialize();
        $sStartAttr = $this->aProperties['start_attr'];
        $sEndAttr = $this->aProperties['end_attr'];
        $sTitleAttr = $this->aProperties['title_attr'];
        $sDescriptionAttr = $this->aProperties['description_attr'];

        $sColor = $this->aProperties['color'];

        $aViews = array(
            'month' => 'month',
            'week' => $this->aProperties['agenda_week'] ? 'agendaWeek' : 'basicWeek',
            'day' => $this->aProperties['agenda_day'] ? 'agendaDay' : 'basicDay'
        );
        $sDefaultView = $aViews[$this->aProperties['default_view']];
        $sViews = implode(', ', $aViews);

        $sDashletId = 'dashlet-calendar_'.($bEditMode? 'edit_' : '').$this->sId;
        $sCalendarId = 'calendar_'.($bEditMode? 'edit_' : '').$this->sId;
        $oPage->add('<div id="'.$sDashletId.'" class="dashlet-content">');
        $sHtmlTitle = htmlentities(Dict::S($this->aProperties['title']), ENT_QUOTES, 'UTF-8'); // done in the itop block
        if ($sHtmlTitle != '') {
            $oPage->add('<h1>' . $sHtmlTitle . '</h1>');
        }
        $sTimeFormat = 'H:mm'; // MetaModel::GetConfig()->Get();
//        $sDateFomat = '';
        // TODO: Язык
        $sLanguage = substr(strtolower(trim(UserRights::GetUserLanguage())), 0, 2);
        $sURL = '../' . $this->sModuleUrlBase . 'ajax.php';

        $oPage->add_ready_script(
            <<<EOF
                $('#$sCalendarId').fullCalendar({
        lang: '$sLanguage',
        eventSources: [
            {   
                url: '$sURL',
                color: '$sColor',
                data: { filter: '$sFilter', start_attr: '$sStartAttr', end_attr: '$sEndAttr', title_attr: '$sTitleAttr', description_attr: '$sDescriptionAttr' }
            }
        ],
        timeFormat: '$sTimeFormat',
        header: {
            left:   'title',
            center: 'prev, today, next',
            right:  '$sViews'
        },
        defaultView: '$sDefaultView',
        eventLimit: true, // for all non-agenda views
        views: {
            agenda: {
                eventLimit: 6 // adjust to 6 only for agendaWeek/agendaDay
            }
        }
    })
EOF
        );
        $oPage->add('<div id="'.$sCalendarId.'"></div>');
        $oPage->add('</div>');
    }

    protected function GetDateAttributes($sOql)
    {
        $oQuery = $this->oModelReflection->GetQuery($sOql);
        $sClass = $oQuery->GetClass();
        $aDateAttCodes = array();
        foreach($this->oModelReflection->ListAttributes($sClass) as $sAttCode => $sAttType)
        {
            if (is_subclass_of($sAttType, 'AttributeDateTime') || $sAttType == 'AttributeDateTime')
            {
                $sLabel = $this->oModelReflection->GetLabel($sClass, $sAttCode);
                $aDateAttCodes[$sAttCode] = $sLabel;
            }
        }
        asort($aDateAttCodes);
        return $aDateAttCodes;
    }

    protected function GetEventTextOptions($sOql)
    {
        $oQuery = $this->oModelReflection->GetQuery($sOql);
        $sClass = $oQuery->GetClass();
        $aTitleAttrs = array();
        foreach($this->oModelReflection->ListAttributes($sClass) as $sAttCode => $sAttType)
        {
            if ($sAttType == 'AttributeLinkedSet') continue;
            if (is_subclass_of($sAttType, 'AttributeLinkedSet')) continue;
            if ($sAttType == 'AttributeFriendlyName') continue;
            if (is_subclass_of($sAttType, 'AttributeFriendlyName')) continue;
            if ($sAttType == 'AttributeExternalField') continue;
            if (is_subclass_of($sAttType, 'AttributeExternalField')) continue;
            if ($sAttType == 'AttributeOneWayPassword') continue;

            $sLabel = $this->oModelReflection->GetLabel($sClass, $sAttCode);
            $aTitleAttrs[$sAttCode] = $sLabel;
        }
        asort($aTitleAttrs);
        return $aTitleAttrs;
    }

    public function GetPropertiesFields(DesignerForm $oForm)
    {
        $oField = new DesignerTextField('title', Dict::S('UI:DashletCalendar:Prop-Title'), $this->aProperties['title']);
        $oForm->AddField($oField);

        $aViews = array(
            'month' => Dict::S('UI:DashletCalendar:Prop-Default-View:Month'),
            'week' => Dict::S('UI:DashletCalendar:Prop-Default-View:Week'),
            'day' => Dict::S('UI:DashletCalendar:Prop-Default-View:Day'),
        );
        $oField = new DesignerComboField('default_view', Dict::S('UI:DashletCalendar:Prop-Default-View'), $this->aProperties['default_view']);
        $oField->SetMandatory();
        $oField->SetAllowedValues($aViews);
        $oForm->AddField($oField);

        $oField = new DesignerBooleanField('agenda_week', Dict::S('UI:DashletCalendar:Prop-Agenda-Week'), $this->aProperties['agenda_week']);
        $oForm->AddField($oField);

        $oField = new DesignerBooleanField('agenda_day', Dict::S('UI:DashletCalendar:Prop-Agenda-Day'), $this->aProperties['agenda_day']);
        $oForm->AddField($oField);

        $oField = new DesignerLongTextField('query', Dict::S('UI:DashletCalendar:Prop-Query'), $this->aProperties['query']);
        $oField->SetMandatory();
        $oForm->AddField($oField);

        try {
            // build the list of possible values (attribute codes + ...)
            $aDateAttCodes = $this->GetDateAttributes($this->aProperties['query']);
            $oFieldStart = new DesignerComboField('start_attr', Dict::S('UI:DashletCalendar:Prop-Start'), $this->aProperties['start_attr']);
            $oFieldStart->SetMandatory();
            $oFieldStart->SetAllowedValues($aDateAttCodes);

            $oFieldEnd = new DesignerComboField('end_attr', Dict::S('UI:DashletCalendar:Prop-End'), $this->aProperties['end_attr']);
            $oFieldEnd->SetAllowedValues($aDateAttCodes);
        } catch(Exception $e) {
            $oFieldStart = new DesignerTextField('start_attr', Dict::S('UI:DashletCalendar:Prop-Start'), $this->aProperties['start_attr']);
            $oFieldStart->SetReadOnly();
            $oFieldEnd = new DesignerTextField('end_attr', Dict::S('UI:DashletCalendar:Prop-End'), $this->aProperties['end_attr']);
            $oFieldEnd->SetReadOnly();
        }
        $oForm->AddField($oFieldStart);
        $oForm->AddField($oFieldEnd);

        try {
            // build the list of possible values (attribute codes + ...)
            $aAttcodes = $this->GetEventTextOptions($this->aProperties['query']);
            $oFieldTitle = new DesignerComboField('title_attr', Dict::S('UI:DashletCalendar:Event:Prop-Title'), $this->aProperties['title_attr']);
            $oFieldTitle->SetMandatory();
            $oFieldTitle->SetAllowedValues($aAttcodes);

            $oFieldDescription = new DesignerComboField('description_attr', Dict::S('UI:DashletCalendar:Event:Prop-Desc'), $this->aProperties['description_attr']);
            $oFieldDescription->SetAllowedValues($aAttcodes);
        } catch(Exception $e) {
            $oFieldTitle = new DesignerTextField('title_attr', Dict::S('UI:DashletCalendar:Event:Prop-Title'), $this->aProperties['title_attr']);
            $oFieldTitle->SetReadOnly();

            $oFieldDescription = new DesignerTextField('description_attr', Dict::S('UI:DashletCalendar:Event:Prop-Desc'), $this->aProperties['description_attr']);
            $oFieldDescription->SetReadOnly();
        }
        $oForm->AddField($oFieldTitle);
        $oForm->AddField($oFieldDescription);

        // TODO: цвета в конфиг
        $aColors = array(
            'blue' => Dict::S('UI:DashletCalendar:Prop-Color:Blue'),
            'green' => Dict::S('UI:DashletCalendar:Prop-Color:Green'),
            'red' => Dict::S('UI:DashletCalendar:Prop-Color:Red'),
        );

        $oField = new DesignerComboField('color', Dict::S('UI:DashletCalendar:Prop-Color'), $this->aProperties['color']);
        $oField->SetMandatory();
        $oField->SetAllowedValues($aColors);
        $oForm->AddField($oField);


    }

    public function Update($aValues, $aUpdatedFields)
    {
        if (in_array('query', $aUpdatedFields))
        {
            try
            {
                $sCurrQuery = $aValues['query'];
                $oCurrSearch = $this->oModelReflection->GetQuery($sCurrQuery);
                $sCurrClass = $oCurrSearch->GetClass();

                $sPrevQuery = $this->aProperties['query'];
                $oPrevSearch = $this->oModelReflection->GetQuery($sPrevQuery);
                $sPrevClass = $oPrevSearch->GetClass();

                if ($sCurrClass != $sPrevClass)
                {
                    $this->bFormRedrawNeeded = true;
                    // wrong but not necessary - unset($aUpdatedFields['group_by']);
                    $this->aProperties['start_attr'] = '';
                    $this->aProperties['end_attr'] = '';
                }
            }
            catch(Exception $e)
            {
                $this->bFormRedrawNeeded = true;
            }
        }
        $oDashlet = parent::Update($aValues, $aUpdatedFields);
        return $oDashlet;
    }
}
