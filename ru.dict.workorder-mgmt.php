<?php
/**
 * Localized data
 *
 * @copyright   Copyright (C) 2015 Vladimir Kunin <v.b.kunin@gmail.com>
 * @license     http://opensource.org/licenses/AGPL-3.0
 */

Dict::Add('RU RU', 'Russian', 'Russian', array(

    'Class:ScheduledActivity' => 'Плановая активность',
    'Class:ScheduledActivity+' => 'Плановые периодичекие действия и задачи',
    'Class:ScheduledActivity/Name' => '%1$s',
    'Class:ScheduledActivity/Attribute:status' => 'Статус',
    'Class:ScheduledActivity/Attribute:status/Value:inactive' => 'Неактивно',
    'Class:ScheduledActivity/Attribute:status/Value:active' => 'Активно',

    'Class:ScheduledActivity/Attribute:category_id' => 'Категория',
    'Class:ScheduledActivity/Attribute:category_id+' => 'Категория активности',
    'Class:ScheduledActivity/Attribute:category_name' => 'Категория',
    'Class:ScheduledActivity/Attribute:start_date' => 'Начало в',
    'Class:ScheduledActivity/Attribute:start_date+' => 'Дата начала активности',
    'Class:ScheduledActivity/Attribute:end_date' => 'Окончание в',
    'Class:ScheduledActivity/Attribute:end_date+' => 'Дата окончания активности',
    'Class:ScheduledActivity/Attribute:action_date' => 'Запланировано на',
    'Class:ScheduledActivity/Attribute:action_date+' => 'Дата следующего запланированного действия',

    'Class:ScheduledActivity/Attribute:pre_action_date' => 'Дата предв. действия',
    'Class:ScheduledActivity/Attribute:pre_action_date+' => 'Дата следующего предварительного действия',
    'Class:ScheduledActivity/Attribute:pre_action_enabled' => 'Вкл. предв. действие',
    'Class:ScheduledActivity/Attribute:pre_action_enabled/Value:0' => 'Нет',
    'Class:ScheduledActivity/Attribute:pre_action_enabled/Value:1' => 'Да',
    'Class:ScheduledActivity/Attribute:pre_action_enabled+' => 'Выключатель предварительного действия',
    'Class:ScheduledActivity/Attribute:pre_action_interval' => 'Интервал предв. действия',
    'Class:ScheduledActivity/Attribute:pre_action_interval+' => 'Интервал запуска предварительного действия',

    'Class:ScheduledActivity/Attribute:wo_name' => 'Название наряда',
    'Class:ScheduledActivity/Attribute:wo_name+' => 'Название наряда на работу',
    'Class:ScheduledActivity/Attribute:wo_description' => 'Описание наряда',
    'Class:ScheduledActivity/Attribute:wo_description+' => 'Описание наряда на работу',
    'Class:ScheduledActivity/Attribute:wo_team_id' => 'Команда наряда',
    'Class:ScheduledActivity/Attribute:wo_team_id+' => 'Команда наряда',
    'Class:ScheduledActivity/Attribute:wo_agent_id' => 'Агент наряда',
    'Class:ScheduledActivity/Attribute:wo_agent_id+' => 'Агент наряда',
    'Class:ScheduledActivity/Attribute:wo_duration' => 'Продолжительность',
    'Class:ScheduledActivity/Attribute:wo_duration+' => 'Продолжительность работы',

    'Class:ScheduledActivity/Attribute:document_list' => 'Документы',
    'Class:ScheduledActivity/Attribute:document_list+' => 'Документы',
    'Class:ScheduledActivity/Attribute:periodicity' => 'Периодичность',
    'Class:ScheduledActivity/Attribute:periodicity+' => 'Шаблон периодичности формата Crontab, можно проверить здесь http://cronchecker.net/',
    'Class:ScheduledActivity/Attribute:periodicity?' => 'Проверить можно здесь: http://cronchecker.net/
*    *    *    *    *    *
-    -    -    -    -    -
|    |    |    |    |    |
|    |    |    |    |    + Год [опционально]
|    |    |    |    +----- День недели (0 - 7) (Воскресенье =0 или =7)
|    |    |    +---------- Месяц (1 - 12)
|    |    +--------------- День месяца (1 - 31)
|    +-------------------- Час (0 - 23)
+------------------------- Минута (0 - 59)',

    'Class:ScheduledActivity/Stimulus:ev_activate' => 'Активировать',
    'Class:ScheduledActivity/Stimulus:ev_activate+' => '',
    'Class:ScheduledActivity/Stimulus:ev_deactivate' => 'Деактивировать',
    'Class:ScheduledActivity/Stimulus:ev_deactivate+' => '',

    'Class:lnkDocumentToTicket' => 'Связь Документ/Тикет',
    'Class:lnkDocumentToTicket+' => 'Связь Документ/Тикет',
    'Class:lnkDocumentToTicket/Name' => '%1$s - %2$s',
    'Class:lnkDocumentToTicket/Attribute:ticket_id' => 'Тикет',
    'Class:lnkDocumentToTicket/Attribute:document_id' => 'Документ',
    'Class:lnkDocumentToTicket/Attribute:document_type' => 'Тип документа',

    'Class:ScheduledActivityCategory' => 'Категория плановой активности',
    'Class:ScheduledActivityCategory+' => 'Категория плановой активности',

    'ScheduledActivity:baseinfo' => 'Основное',
    'ScheduledActivity:preaction' => 'Предварительное действие',
    'ScheduledActivity:template' => 'Шаблон наряда',

    'Menu:WorkOrderManagement' => 'Управление работами',
    'Menu:WorkOrderManagement+' => 'Управление работами',
    'UI:WorkOrderMgmtMenuOverview:Title' => 'Управление работами',
    'UI:WorkOrderMgmtMenuOverview:Title+' => 'Управление работами',
    'UI:WorkOrderCalendar:Title' => 'Календарь работ',
    'UI:WorkOrderCalendar:Title+' => 'Календарь работ',
    'Menu:WorkOrderMgmt:Shortcuts' => 'Наряды на работу',
    'Menu:Calendar:Overview' => 'Календарь работ',
    'Menu:Calendar:Overview+' => 'Календарь работ',

    'Menu:WorkOrderMgmt:OpenWorkOrders' => 'Открытые наряды',
    'Menu:WorkOrderMgmt:OpenWorkOrders+' => 'Открытые наряды на работу',
    'Menu:WorkOrderMgmt:SearchWorkOrder' => 'Поиск нарядов',
    'Menu:WorkOrderMgmt:SearchWorkOrder+' => 'Найти наряды на работу',

    'Menu:ScheduledActivity:Shortcuts' => 'Плановая активность',
    'Menu:ScheduledActivity:Shortcuts+' => 'Плановая активность',
    'Menu:ScheduledActivity:NewScheduledActivity' => 'Новая плановая активность',
    'Menu:ScheduledActivity:NewScheduledActivity+' => 'Создать плановую активность',
    'Menu:ScheduledActivity:AllScheduledActivity' => 'Все плановые активности',
    'Menu:ScheduledActivity:AllScheduledActivity+' => 'Все плановые активности',

    'Class:TriggerOnScheduledActivity' => 'Триггер (на плановую активность)',
    'Class:TriggerOnScheduledActivity+' => 'Триггер (на плановую активность)',
    'Class:TriggerOnScheduledActivity/Attribute:target_class' => 'Целевой класс (выберите "Плановая активность")',
    'Class:TriggerOnScheduledActivity/Attribute:action_type' => 'Тип действия',
    'Class:TriggerOnScheduledActivity/Attribute:action_type+' => 'Тип действия плановой активности',
    'Class:TriggerOnScheduledActivity/Attribute:action_type/Value:main' => 'Основное',
    'Class:TriggerOnScheduledActivity/Attribute:action_type/Value:preliminary' => 'Предварительное',

    'Menu:NotificationsMenu' => 'Триггеры и действия',
    'Menu:NotificationsMenu+' => 'Настройка триггеров и действий',


    'UI:DashletCalendar:Label' => 'Календарь',
    'UI:DashletCalendar:Description' => 'Календарь',

    'UI:WorkOrderCalendar:Title' => 'Календарь работ',
    'UI:WorkOrderCalendar:Title+' => 'Календарь работ',

    'UI:DashletCalendar:Prop-Title' => 'Название',
    'UI:DashletCalendar:Prop-Default-View' => 'Период по умолчанию',
    'UI:DashletCalendar:Prop-Default-View:Month' => 'Месяц',
    'UI:DashletCalendar:Prop-Default-View:Week' => 'Неделя',
    'UI:DashletCalendar:Prop-Default-View:Day' => 'День',
    'UI:DashletCalendar:Prop-Agenda-Week' => 'Неделя по часам',
    'UI:DashletCalendar:Prop-Agenda-Day' => 'День по часам',

    'UI:DashletCalendar:EventSet' => 'Набор объектов %1$d',
    'UI:DashletCalendar:Event:Prop-Enabled' => 'Вкл/Выкл',
    'UI:DashletCalendar:Event:Prop-Query' => 'Запрос',
    'UI:DashletCalendar:Event:Prop-Start' => 'Начало',
    'UI:DashletCalendar:Event:Prop-Start+' => 'Начало события',
    'UI:DashletCalendar:Event:Prop-End' => 'Окончание',
    'UI:DashletCalendar:Event:Prop-End+' => 'Окончание события',
    'UI:DashletCalendar:Event:Prop-Unfinished' => 'Незавершенные события',
    'UI:DashletCalendar:Event:Prop-Title' => 'Заголовок события',
    'UI:DashletCalendar:Event:Prop-Desc' => 'Описание события',
    'UI:DashletCalendar:Event:Prop-Color' => 'Цвет',
    'UI:DashletCalendar:Event:Prop-Color:blue' => 'Синий',
    'UI:DashletCalendar:Event:Prop-Color:green' => 'Зеленый',
    'UI:DashletCalendar:Event:Prop-Color:red' => 'Красный',
    'UI:DashletCalendar:Event:Prop-Color:brown' => 'Коричневый',
    'UI:DashletCalendar:Event:Prop-Color:gray' => 'Серый',


    // TODO: перенести в itop-rus
    'Ticket:ImpactAnalysis' => 'Анализ влияния',
    'Class:TriggerOnObject/Attribute:filter' => 'OQL фильтр',

));