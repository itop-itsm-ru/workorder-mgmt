<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2015 Vladimir Kunin <v.b.kunin@gmail.com>
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

Dict::Add('EN US', 'English', 'English', array(

    'Class:ScheduledActivity' => 'Scheduled Activity',
    'Class:ScheduledActivity+' => 'Scheduled, periodic actions and tasks',
    'Class:ScheduledActivity/Name' => '%1$s',
    'Class:ScheduledActivity/Attribute:status' => 'Status',
    'Class:ScheduledActivity/Attribute:status/Value:inactive' => 'Inactive',
    'Class:ScheduledActivity/Attribute:status/Value:active' => 'Active',

    'Class:ScheduledActivity/Attribute:category_id' => 'Category',
    'Class:ScheduledActivity/Attribute:category_id+' => 'Category of activity',
    'Class:ScheduledActivity/Attribute:category_name' => 'Category',
    'Class:ScheduledActivity/Attribute:start_date' => 'Start date',
    'Class:ScheduledActivity/Attribute:start_date+' => 'Start date',
    'Class:ScheduledActivity/Attribute:end_date' => 'End date',
    'Class:ScheduledActivity/Attribute:end_date+' => 'End date',
    'Class:ScheduledActivity/Attribute:action_date' => 'Scheduled for',
    'Class:ScheduledActivity/Attribute:action_date+' => 'Date of next scheduled action',

    'Class:ScheduledActivity/Attribute:pre_action_date' => 'Pre-action date',
    'Class:ScheduledActivity/Attribute:pre_action_date+' => 'Date of next preliminary action',
    'Class:ScheduledActivity/Attribute:pre_action_enabled' => 'Pre-action enabled',
    'Class:ScheduledActivity/Attribute:pre_action_enabled/Value:0' => 'No',
    'Class:ScheduledActivity/Attribute:pre_action_enabled/Value:1' => 'Yes',
    'Class:ScheduledActivity/Attribute:pre_action_enabled+' => 'Switch for preliminary action',
    'Class:ScheduledActivity/Attribute:pre_action_interval' => 'Pre-action interval',
    'Class:ScheduledActivity/Attribute:pre_action_interval+' => 'The interval relative on the scheduled action',

    'Class:ScheduledActivity/Attribute:wo_name' => 'Work order name',
    'Class:ScheduledActivity/Attribute:wo_name+' => 'Work order name',
    'Class:ScheduledActivity/Attribute:wo_description' => 'Work order description',
    'Class:ScheduledActivity/Attribute:wo_description+' => 'Work order description',
    'Class:ScheduledActivity/Attribute:wo_team_id' => 'Work order team',
    'Class:ScheduledActivity/Attribute:wo_team_id+' => 'Work order team',
    'Class:ScheduledActivity/Attribute:wo_agent_id' => 'Work order agent',
    'Class:ScheduledActivity/Attribute:wo_agent_id+' => 'Work order agent',
    'Class:ScheduledActivity/Attribute:wo_duration' => 'Work duration',
    'Class:ScheduledActivity/Attribute:wo_duration+' => 'Work duration',

    'Class:ScheduledActivity/Attribute:document_list' => 'Documents',
    'Class:ScheduledActivity/Attribute:document_list+' => 'Documents',
    'Class:ScheduledActivity/Attribute:periodicity' => 'Periodicity',
    'Class:ScheduledActivity/Attribute:periodicity+' => 'Crontab-like pattern check on http://cronchecker.net/',
    'Class:ScheduledActivity/Attribute:periodicity?' => 'Check your pattern on http://cronchecker.net/
*    *    *    *    *    *
-    -    -    -    -    -
|    |    |    |    |    |
|    |    |    |    |    + year [optional]
|    |    |    |    +----- day of week (0 - 7) (Sunday=0 or 7)
|    |    |    +---------- month (1 - 12)
|    |    +--------------- day of month (1 - 31)
|    +-------------------- hour (0 - 23)
+------------------------- min (0 - 59)',

    'Class:ScheduledActivity/Stimulus:ev_activate' => 'Activate',
    'Class:ScheduledActivity/Stimulus:ev_activate+' => '',
    'Class:ScheduledActivity/Stimulus:ev_deactivate' => 'Deactivate',
    'Class:ScheduledActivity/Stimulus:ev_deactivate+' => '',

    'Class:lnkDocumentToTicket' => 'Link Document/Ticket',
    'Class:lnkDocumentToTicket+' => 'Link Document/Ticket',
    'Class:lnkDocumentToTicket/Name' => '%1$s - %2$s',
    'Class:lnkDocumentToTicket/Attribute:ticket_id' => 'Ticket',
    'Class:lnkDocumentToTicket/Attribute:document_id' => 'Document',
    'Class:lnkDocumentToTicket/Attribute:document_type' => 'Document type',

    'Class:ScheduledActivityCategory' => 'Scheduled Activity Category',
    'Class:ScheduledActivityCategory+' => 'Scheduled Activity Category',

    'ScheduledActivity:baseinfo' => 'General Information',
    'ScheduledActivity:preaction' => 'Preliminary action',
    'ScheduledActivity:template' => 'Work order template',

    'Menu:WorkOrderManagement' => 'Work Order Management',
    'Menu:WorkOrderManagement+' => 'Work Order Management',
    'UI:WorkOrderMgmtMenuOverview:Title' => 'Work Order Management',
    'UI:WorkOrderMgmtMenuOverview:Title+' => 'Work Order Management',
    'UI:WorkOrderCalendar:Title' => 'Work Order Calendar',
    'UI:WorkOrderCalendar:Title+' => 'Work Order Calendar',
    'Menu:WorkOrderMgmt:Shortcuts' => 'Work Orders',
    'Menu:Calendar:Overview' => 'Calendar',
    'Menu:Calendar:Overview+' => 'Calendar',

    'Menu:WorkOrderMgmt:OpenWorkOrders' => 'Open work orders',
    'Menu:WorkOrderMgmt:OpenWorkOrders+' => 'Open work orders',
    'Menu:WorkOrderMgmt:SearchWorkOrder' => 'Search work orders',
    'Menu:WorkOrderMgmt:SearchWorkOrder+' => 'Search work orders',

    'Menu:ScheduledActivity:Shortcuts' => 'Scheduled Activity',
    'Menu:ScheduledActivity:Shortcuts+' => 'Scheduled Activity',
    'Menu:ScheduledActivity:NewScheduledActivity' => 'New scheduled activity',
    'Menu:ScheduledActivity:NewScheduledActivity+' => 'New scheduled activity',
    'Menu:ScheduledActivity:AllScheduledActivity' => 'All scheduled activities',
    'Menu:ScheduledActivity:AllScheduledActivity+' => 'All scheduled activities',

    'Class:TriggerOnScheduledActivity' => 'Trigger (on scheduled activity)',
    'Class:TriggerOnScheduledActivity+' => 'Trigger (on scheduled activity)',
    'Class:TriggerOnScheduledActivity/Attribute:target_class' => 'Target class (select Scheduled Activity)',
    'Class:TriggerOnScheduledActivity/Attribute:action_type' => 'Action type',
    'Class:TriggerOnScheduledActivity/Attribute:action_type+' => 'The type of action scheduled activity',
    'Class:TriggerOnScheduledActivity/Attribute:action_type/Value:main' => 'Main',
    'Class:TriggerOnScheduledActivity/Attribute:action_type/Value:preliminary' => 'Preliminary',

    'Menu:NotificationsMenu' => 'Triggers and actions',
    'Menu:NotificationsMenu+' => 'Triggers and actions configuration',

    'UI:DashletCalendar:Label' => 'Work Order Calendar',
    'UI:DashletCalendar:Description' => 'Work Order Calendar',

));
