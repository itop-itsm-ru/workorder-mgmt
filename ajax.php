<?php

require_once('../../approot.inc.php');

try {
    require_once(APPROOT . '/application/startup.inc.php');
    require_once(APPROOT . '/application/loginwebpage.class.inc.php');
    LoginWebPage::DoLogin(); // Check user rights and prompt if needed
    $sStartDateValue = utils::ReadParam('start', '');
    $sStartDateAttr = utils::ReadParam('start_attr', '');
    $sEndDateValue = utils::ReadParam('end', '');
    $sEndDateAttr = utils::ReadParam('end_attr', null);
    $sTitleAttr = utils::ReadParam('title_attr', '');
    $sDescriptionAttr = utils::ReadParam('description_attr', null);
    $sFilter = utils::ReadParam('filter', '');
    $oFilter = DBObjectSearch::unserialize($sFilter);
    $oFilter->AddCondition($sStartDateAttr, $sStartDateValue, '>');
    // TODO: end_attr передается всегда, нужно сравнивать с пустой строкой?
    $sEndDateAttr && $oFilter->AddCondition($sEndDateAttr, $sEndDateValue, '<');
    $oObjectSet = new DBObjectSet($oFilter);
    $aEvents = array();
    while ($oObj = $oObjectSet->Fetch()) {
        $aEvent = array();
        $aEvent['title'] = utils::HtmlToText($oObj->GetAsHTML($sTitleAttr)) . ($sDescriptionAttr ? "\n" . utils::HtmlToText($oObj->GetAsHTML($sDescriptionAttr)) : '');
        $aEvent['start'] = $oObj->Get($sStartDateAttr);
        $aEvent['end'] = $sEndDateAttr ? $oObj->Get($sEndDateAttr) : '';
        $aEvent['url'] = ApplicationContext::MakeObjectUrl(get_class($oObj), $oObj->GetKey());
        $aEvents[] = $aEvent;
    }
    $jsonEvents = json_encode($aEvents);
    echo $jsonEvents;
} catch (Exception $e) {
    echo $e->getMessage();
}