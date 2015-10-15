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
  'Class:ScheduledActivity/Attribute:title' => 'Название',
  'Class:ScheduledActivity/Attribute:description' => 'Описание',

  'Class:ScheduledActivity/Attribute:team_id' => 'Команда',
  'Class:ScheduledActivity/Attribute:team_name' => 'Команда',
  'Class:ScheduledActivity/Attribute:agent_id' => 'Агент',
  'Class:ScheduledActivity/Attribute:agent_name' => 'Агент',
  'Class:ScheduledActivity/Attribute:status' => 'Статус',
  'Class:ScheduledActivity/Attribute:status/Value:inactive' => 'Неактивно',
  'Class:ScheduledActivity/Attribute:status/Value:active' => 'Активно',

  'Class:ScheduledActivity/Attribute:category_id' => 'Категория',
  'Class:ScheduledActivity/Attribute:category_name' => 'Категория',
  'Class:ScheduledActivity/Attribute:start_date' => 'Начало в',
  'Class:ScheduledActivity/Attribute:start_date+' => 'Дата начала цикла',
  'Class:ScheduledActivity/Attribute:end_date' => 'Окончание в',
  'Class:ScheduledActivity/Attribute:end_date+' => 'Дата окончания цикла',
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
  'Class:ScheduledActivity/Attribute:wo_description' => 'Описание наряда на работу',
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
  'Menu:Calendar:Overview' => 'Календарь работ',

  'Menu:WorkOrderMgmt:OpenWorkOrders' => 'Открытые наряды',
  'Menu:WorkOrderMgmt:OpenWorkOrders+' => 'Открытые наряды на работу',
  'Menu:WorkOrderMgmt:SearchWorkOrder' => 'Поиск нарядов',
  'Menu:WorkOrderMgmt:SearchWorkOrder+' => 'Найти наряды на работу',

  'Menu:ScheduledActivity:Shortcuts' => 'Плановая активность',
  'Menu:ScheduledActivity:Shortcuts+' => 'Плановая активность',
  'Menu:ScheduledActivity:NewScheduledActivity' => 'Новая планова активность',
  'Menu:ScheduledActivity:NewScheduledActivity+' => 'Создать плановую активность',
  'Menu:ScheduledActivity:AllScheduledActivity' => 'Все плановые активности',
  'Menu:ScheduledActivity:AllScheduledActivity+' => 'Все плановые активности',

  'Ticket:ImpactAnalysis' => 'Анализ влияния',

  'Class:ActionCreateObject' => 'Создания объекта',
  'Class:ActionCreateObject+' => 'Действие создания объекта',
  'Class:ActionCreateObject/Attribute:obj_class' => 'Класс объекта',
  'Class:ActionCreateObject/Attribute:obj_class+' => 'Класс создаваемого объекта',

  'Class:ActionCreateFromTemplate' => 'Создания объекта из шаблона',
  'Class:ActionCreateFromTemplate+' => 'Действие создания объекта из шаблона',
  'Class:ActionCreateFromTemplate/Attribute:mapped_fields_list' => 'Сопоставление полей',
  'Class:ActionCreateFromTemplate/Attribute:mapped_fields_list+' => 'Сопоставление полей объекта и шаблона',

  'Class:MappedField' => 'Сопоставляемое поле',
  'Class:MappedField+' => 'Сопоставляемое поле (маппинг при создании объекта из шаблона)',
  'Class:MappedField/Attribute:action_id' => 'Действие',
  'Class:MappedField/Attribute:action_id+' => 'Действие создания объекта из шаблона',
  'Class:MappedField/Attribute:action_class' => 'Класс объекта',
  'Class:MappedField/Attribute:action_class+' => 'Класс создаваемого объекта',
  'Class:MappedField/Attribute:mapped_attcode' => 'Атрибут',
  'Class:MappedField/Attribute:mapped_attcode+' => 'Код атрибута создаваемого объекта (name, team_id и т.д.)',
  'Class:MappedField/Attribute:mapped_value' => 'Значение',
  'Class:MappedField/Attribute:mapped_value+' => 'Значение поля, можно использовать плейсхолдеры $this->att_code$',

  'Class:TriggerOnScheduledActivity' => 'Триггер (на плановую активность)',
  'Class:TriggerOnScheduledActivity+' => 'Триггер (на плановую активность)',
  'Class:TriggerOnScheduledActivity/Attribute:filter' => 'OQL фильтр',
  'Class:TriggerOnScheduledActivity/Attribute:action_type' => 'Тип действия',
  'Class:TriggerOnScheduledActivity/Attribute:action_type+' => 'Тип действия плановой активности',
  'Class:TriggerOnScheduledActivity/Attribute:action_type/Value:main' => 'Основное',
  'Class:TriggerOnScheduledActivity/Attribute:action_type/Value:preliminary' => 'Предварительное',

));

?>


