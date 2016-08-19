<?php

class DashletCalendar extends Dashlet
{
    protected $sModuleUrlBase;

    public function __construct($oModelReflection, $sId)
    {
        parent::__construct($oModelReflection, $sId);
        $this->aProperties['title'] = Dict::S('UI:WorkOrderCalendar:Title');
        $this->aProperties['query'] = 'SELECT WorkOrder';
        $this->aProperties['start'] = 'start_date';
        $this->aProperties['end'] = 'end_date';
        $this->aProperties['color'] = 'blue';
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

        $oPage->add_linked_stylesheet('../' . $this->sModuleUrlBase . 'fullcalendar/custom.css');
        $oPage->add_linked_stylesheet('../' . $this->sModuleUrlBase . 'fullcalendar/fullcalendar.min.css');
        // $oPage->add_linked_script('../'.$this->sModuleUrlBase.'fullcalendar/lib/jquery.min.js');
        $oPage->add_linked_script('../' . $this->sModuleUrlBase . 'fullcalendar/lib/moment.min.js');
        $oPage->add_linked_script('../' . $this->sModuleUrlBase . 'fullcalendar/fullcalendar.min.js');
        $oPage->add_linked_script('../' . $this->sModuleUrlBase . 'fullcalendar/lang/ru.js');

//        $sQuery = $this->aProperties['query'];
//        $oFilter = DBObjectSearch::FromOQL($sQuery);
        $sColor = $this->aProperties['color'];

        $sDashletId = 'dashlet-calendar_'.($bEditMode? 'edit_' : '').$this->sId;
        $sCalendarId = 'calendar_'.($bEditMode? 'edit_' : '').$this->sId;
        $oPage->add('<div id="'.$sDashletId.'" class="dashlet-content">');

        $sHtmlTitle = htmlentities(Dict::S($this->aProperties['title']), ENT_QUOTES, 'UTF-8'); // done in the itop block
        if ($sHtmlTitle != '') {
            $oPage->add('<h1>' . $sHtmlTitle . '</h1>');
        }
        // TODO: Язык
        $sLanguage = substr(strtolower(trim(UserRights::GetUserLanguage())), 0, 2);
        $sURL = '../' . $this->sModuleUrlBase . 'ajax.php';

        $oPage->add_ready_script(
            <<<EOF
                $('#$sCalendarId').fullCalendar({
        lang: '$sLanguage',
        events: '$sURL',
        timeFormat: 'H:mm',
        header: {
            left:   'title',
            center: 'prev, today, next',
            right:  'month, agendaWeek, agendaDay'
        },
        eventColor: '$sColor',
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

    public function GetPropertiesFields(DesignerForm $oForm)
    {
        $oField = new DesignerTextField('title', Dict::S('UI:DashletCalendar:Prop-Title'), $this->aProperties['title']);
        $oForm->AddField($oField);

        $oField = new DesignerLongTextField('query', Dict::S('UI:DashletCalendar:Prop-Query'), $this->aProperties['query']);
        $oField->SetMandatory();
        $oForm->AddField($oField);

        try
        {
            // build the list of possible values (attribute codes + ...)
            $aDateAttCodes = $this->GetDateAttributes($this->aProperties['query']);

            $oFieldStart = new DesignerComboField('start', Dict::S('UI:DashletCalendar:Prop-Start'), $this->aProperties['start']);
            $oFieldStart->SetMandatory();
            $oFieldStart->SetAllowedValues($aDateAttCodes);

            $oFieldEnd = new DesignerComboField('end', Dict::S('UI:DashletCalendar:Prop-End'), $this->aProperties['end']);
            $oFieldEnd->SetMandatory();
            $oFieldEnd->SetAllowedValues($aDateAttCodes);
        }
        catch(Exception $e)
        {
            $oFieldStart = new DesignerTextField('start', Dict::S('UI:DashletCalendar:Prop-Start'), $this->aProperties['start']);
            $oFieldStart->SetReadOnly();
            $oFieldEnd = new DesignerTextField('end', Dict::S('UI:DashletCalendar:Prop-End'), $this->aProperties['end']);
            $oFieldEnd->SetReadOnly();
        }
        $oForm->AddField($oFieldStart);
        $oForm->AddField($oFieldEnd);

        // TODO: цвета в конфиг
        $aColors = array(
            'blue' => Dict::S('UI:DashletGroupBy:Prop-Color:Blue'),
            'green' => Dict::S('UI:DashletGroupBy:Prop-Color:Green'),
            'red' => Dict::S('UI:DashletGroupBy:Prop-Color:Red'),
        );

        $oField = new DesignerComboField('color', Dict::S('UI:DashletGroupBy:Prop-Color'), $this->aProperties['color']);
        $oField->SetMandatory();
        $oField->SetAllowedValues($aColors);
        $oForm->AddField($oField);
    }
}
