<?php

abstract class ActionCreateObject extends Action
{
  public static function Init()
  {
    $aParams = array
    (
      "category" => "core/cmdb,application",
      "key_type" => "autoincrement",
      "name_attcode" => "name",
      "state_attcode" => "",
      "reconc_keys" => array('name'),
      "db_table" => "priv_action_createobject",
      "db_key_field" => "id",
      "db_finalclass_field" => "",
      "display_template" => "",
    );
    MetaModel::Init_Params($aParams);
    MetaModel::Init_InheritAttributes();
    // TODO: class_category = autocreate, more_values = WorkOrder - только Наряд на работу можно создать автоматически. Пока... :-)
    MetaModel::Init_AddAttribute(new AttributeClass("obj_class", array("class_category"=>"autocreate", "more_values"=>"WorkOrder", "sql"=>"obj_class", "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array())));
    MetaModel::Init_SetZListItems('details', array('name', 'description', 'status', 'obj_class', 'trigger_list'));
    MetaModel::Init_SetZListItems('list', array('finalclass', 'name', 'description', 'status', 'obj_class'));
  // MetaModel::Init_SetZListItems('standard_search', array('name')); // Criteria of the std search form
  // MetaModel::Init_SetZListItems('advanced_search', array('name')); // Criteria of the advanced search form
  }
}

class ActionCreateFromTemplate extends ActionCreateObject
{
  public static function Init()
  {
    $aParams = array
    (
      "category" => "core/cmdb,application",
      "key_type" => "autoincrement",
      "name_attcode" => "name",
      "state_attcode" => "",
      "reconc_keys" => array('name'),
      "db_table" => "priv_action_createfromtemplate",
      "db_key_field" => "id",
      "db_finalclass_field" => "",
      "display_template" => "",
    );
    MetaModel::Init_Params($aParams);
    MetaModel::Init_InheritAttributes();


    MetaModel::Init_AddAttribute(new AttributeLinkedSet("mapped_fields_list", array("linked_class"=>"MappedField", "ext_key_to_me"=>"action_id", "allowed_values"=>null, "count_min"=>0, "count_max"=>0, "depends_on"=>array(), "edit_mode" => LINKSET_EDITMODE_INPLACE, "tracking_level" => LINKSET_TRACKING_ALL)));

    MetaModel::Init_SetZListItems('details', array('name', 'description', 'status', 'obj_class', 'trigger_list', 'mapped_fields_list'));
    MetaModel::Init_SetZListItems('list', array('finalclass', 'name', 'description', 'status', 'obj_class'));
    // MetaModel::Init_SetZListItems('standard_search', array('name')); // Criteria of the std search form
    // MetaModel::Init_SetZListItems('advanced_search', array('name')); // Criteria of the advanced search form
  }

  public function DoExecute($oTrigger, $aContextArgs)
  {
    if (MetaModel::IsLogEnabledNotification())
    {
      $oLog = new EventNotificationCreateObject();
      if ($this->IsBeingTested())
      {
        $oLog->Set('message', 'TEST - Object created ('.$this->Get('obj_class').')');
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
        $sPrefix = 'TEST ('.$this->Get('obj_class').') - ';
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

      // 1. Получить класс создаваемого объекта
      // 2. Получить набор MappedFields
      // 3. Создать объект
      // 4. Поочередно установить mapped_value в поля объекта
      // 5. Сохранить объект
      // 6. Вернуть id объекта в журнал

      $bRes = false; // Пока не создадим объект и не получим его id
      $this->m_aErrors = array();

      $sClass = $this->Get('obj_class');
      $sOQL = "SELECT MappedField WHERE action_id = ".$this->GetKey();
      $oMappedFieldSet = new DBObjectSet(DBObjectSearch::FromOQL($sOQL));
      if ($oMappedFieldSet->Count() == 0) {
        return "No fields to create new object $sClass";
      }
      $oNewObject = MetaModel::NewObject($sClass);
      while ($oMappedField = $oMappedFieldSet->Fetch())
      {
        $sAttCode = $oMappedField->Get('mapped_attcode');
        $sValue = MetaModel::ApplyParams($oMappedField->Get('mapped_value'), $aContextArgs);
        $oNewObject->Set($sAttCode, $sValue);
      }

      $oNewObject->DBInsertNoReload();

      $oObj = $aContextArgs['this->object()']; // Объект!!!, на котором сработал триггер

      // Determine recicipients
      //
      // $sTo = $this->FindRecipients('to', $aContextArgs);
      // $sCC = $this->FindRecipients('cc', $aContextArgs);
      // $sBCC = $this->FindRecipients('bcc', $aContextArgs);

      // $sFrom = MetaModel::ApplyParams($this->Get('from'), $aContextArgs);
      // $sReplyTo = MetaModel::ApplyParams($this->Get('reply_to'), $aContextArgs);

      // $sSubject = MetaModel::ApplyParams($this->Get('subject'), $aContextArgs);
      // $sBody = MetaModel::ApplyParams($this->Get('body'), $aContextArgs);


    }
    catch(Exception $e)
    {
      ApplicationContext::SetUrlMakerClass($sPreviousUrlMaker);
      throw $e;
    }
    ApplicationContext::SetUrlMakerClass($sPreviousUrlMaker);

    return "Done";

    // if (!is_null($oLog))
    // {
    //   if (isset($oNewObject))       $oLog->Set('to', $sTo);
    // }

    // $oEmail = new EMail();

    // if ($this->IsBeingTested())
    // {
    //   $oEmail->SetSubject('TEST['.$sSubject.']');
    //   $sTestBody = $sBody;
    //   $sTestBody .= "<div style=\"border: dashed;\">\n";
    //   $sTestBody .= "<h1>Testing email notification ".$this->GetHyperlink()."</h1>\n";
    //   $sTestBody .= "<p>The email should be sent with the following properties\n";
    //   $sTestBody .= "<ul>\n";
    //   $sTestBody .= "<li>TO: $sTo</li>\n";
    //   $sTestBody .= "<li>CC: $sCC</li>\n";
    //   $sTestBody .= "<li>BCC: $sBCC</li>\n";
    //   $sTestBody .= "<li>From: $sFrom</li>\n";
    //   $sTestBody .= "<li>Reply-To: $sReplyTo</li>\n";
    //   $sTestBody .= "<li>References: $sReference</li>\n";
    //   $sTestBody .= "</ul>\n";
    //   $sTestBody .= "</p>\n";
    //   $sTestBody .= "</div>\n";
    //   $oEmail->SetBody($sTestBody);
    //   $oEmail->SetRecipientTO($this->Get('test_recipient'));
    //   $oEmail->SetRecipientFrom($this->Get('test_recipient'));
    //   $oEmail->SetReferences($sReference);
    //   $oEmail->SetMessageId($sMessageId);
    // }
    // else
    // {
    //   $oEmail->SetSubject($sSubject);
    //   $oEmail->SetBody($sBody);
    //   $oEmail->SetRecipientTO($sTo);
    //   $oEmail->SetRecipientCC($sCC);
    //   $oEmail->SetRecipientBCC($sBCC);
    //   $oEmail->SetRecipientFrom($sFrom);
    //   $oEmail->SetRecipientReplyTo($sReplyTo);
    //   $oEmail->SetReferences($sReference);
    //   $oEmail->SetMessageId($sMessageId);
    // }

    // if (empty($this->m_aMailErrors))
    // {
    //   if ($this->m_iRecipients == 0)
    //   {
    //     return 'No recipient';
    //   }
    //   else
    //   {
    //     $iRes = $oEmail->Send($aErrors, false, $oLog); // allow asynchronous mode
    //     switch ($iRes)
    //     {
    //       case EMAIL_SEND_OK:
    //         return "Sent";

    //       case EMAIL_SEND_PENDING:
    //         return "Pending";

    //       case EMAIL_SEND_ERROR:
    //         return "Errors: ".implode(', ', $aErrors);
    //     }
    //   }
    // }
    // else
    // {
    //   if (is_array($this->m_aMailErrors) && count($this->m_aMailErrors) > 0)
    //   {
    //     $sError = implode(', ', $this->m_aMailErrors);
    //   }
    //   else
    //   {
    //     $sError = 'Unknown reason';
    //   }
    //   return 'Notification was not sent: '.$sError;
    // }
  }
}

class MappedField extends cmdbAbstractObject
{
  public static function Init()
  {
    $aParams = array
    (
      'category' => 'core/cmdb,application',
      'key_type' => 'autoincrement',
      'name_attcode' => array('action_id_friendlyname', 'mapped_attcode'),
      'state_attcode' => '',
      'reconc_keys' => array('action_id_friendlyname', 'mapped_attcode'),
      'db_table' => 'mappedfield',
      'db_key_field' => 'id',
      'db_finalclass_field' => '',
    );
    MetaModel::Init_Params($aParams);
    MetaModel::Init_InheritAttributes();
    MetaModel::Init_AddAttribute(new AttributeExternalKey("action_id", array("targetclass"=>'ActionCreateFromTemplate', "allowed_values"=>null, "sql"=>'org_id', "is_null_allowed"=>false, "on_target_delete"=>DEL_AUTO, "depends_on"=>array(), "display_style"=>'select', "always_load_in_tables"=>false)));
    MetaModel::Init_AddAttribute(new AttributeExternalField("action_class", array("allowed_values"=>null, "extkey_attcode"=>'action_id', "target_attcode"=>'obj_class', "always_load_in_tables"=>false)));
    MetaModel::Init_AddAttribute(new AttributeString("mapped_attcode", array("allowed_values"=>null, "sql"=>'mapped_attcode', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
    MetaModel::Init_AddAttribute(new AttributeString("mapped_value", array("allowed_values"=>null, "sql"=>'mapped_value', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
    // TODO: Выпадающий список из редактируемых полей класса
    // MetaModel::Init_AddAttribute(new AttributeClassAttcode("mapped_attcode", array("class" => 'Incident', "sql"=>'mapped_attcode', "default_value"=>null, "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));

    MetaModel::Init_SetZListItems('details', array('action_id', 'mapped_attcode', 'mapped_value'));
    MetaModel::Init_SetZListItems('list', array('mapped_attcode', 'mapped_value'));
  }
}

// TODO: Выпадающий список из редактируемых полей класса
// class AttributeClassAttcode extends AttributeString
  // {
  //   static public function ListExpectedParams()
  //   {
  //     return array_merge(parent::ListExpectedParams(), array('depends_on'));
  //   }

  //   public function __construct($sCode, $aParams)
  //   {
  //     $this->m_sCode = $sCode;
  //     $sClass = $aParams['class'];
  //     error_log($sClass);
  //     if (MetaModel::IsValidClass($sClass))
  //     {
  //       $sState = MetaModel::GetDefaultState($sClass);
  //       error_log("Статус: ");
  //       error_log($sState);
  //       $aAttCodes = MetaModel::GetAttributesList($sClass);

  //       if ($sState)
  //       {
  //         foreach ($aAttCodes as $sAttCode) {
  //           $iFlag = MetaModel::GetInitialStateAttributeFlags($sClass, $sState, $sAttCode);
  //           error_log($iFlag);
  //           if ($iFlag == OPT_ATT_HIDDEN || $iFlag == OPT_ATT_READONLY || $iFlag == OPT_ATT_SLAVE)
  //           {
  //             unset($aAttCodes[$sAttCode]);
  //           }
  //         }
  //       }

  //       $aAttCodeLabels = array();
  //       foreach ($aAttCodes as $sAttCode) {
  //         $aAttCodeLabels[] = MetaModel::GetLabel($sClass, $sAttCode, true /* show mandatory as star character */);
  //       }
  //       $aParams["allowed_values"] = new ValueSetEnumAttCodes($aAttCodeLabels);
  //       error_log(var_dump($aParams["allowed_values"]));
  //     }
  //     else
  //     {
  //       error_log('False');
  //       $aParams["allowed_values"] = array();
  //     }
  //     parent::__construct($sCode, $aParams);
  //   }

  //   public function GetDefaultValue()
  //   {
  //     $sDefault = parent::GetDefaultValue();
  //     if (!$this->IsNullAllowed() && $this->IsNull($sDefault))
  //     {
  //       // For this kind of attribute specifying null as default value
  //       // is authorized even if null is not allowed

  //       // Pick the first one...
  //       $aAttCodes = $this->GetAllowedValues();
  //       error_log(var_dump($aAttCodes));
  //       $sDefault = key($aAttCodes);
  //     }
  //     return $sDefault;
  //   }

  //   public function GetAsHTML($sValue, $oHostObject = null, $bLocalize = true)
  //   {
  //     if (empty($sValue)) return '';
  //     return MetaModel::GetLabel($sValue);
  //   }

  //   public function RequiresIndex()
  //   {
  //     return true;
  //   }

  //   public function GetBasicFilterLooseOperator()
  //   {
  //     return '=';
  //   }
// }

// class ValueSetEnumAttCodes extends ValueSetEnum
  // {
  //   // protected $m_sCategories;

  //   // public function __construct($sCategories = '', $sAdditionalValues = '')
  //   // {
  //   //   $this->m_sCategories = $sCategories;
  //   //   parent::__construct($sAdditionalValues);
  //   // }

  //   protected function LoadValues($aArgs)
  //   {
  //     // Call the parent to parse the additional values...
  //     parent::LoadValues($aArgs);

  //     // Translate the labels of the additional values
  //     foreach($this->m_aValues as $sAttCode => $void)
  //     {
  //       if (MetaModel::IsValidClass($sClass))
  //       {
  //         $this->m_aValues[$sClass] = MetaModel::GetName($sClass);
  //       }
  //       else
  //       {
  //         unset($this->m_aValues[$sClass]);
  //       }
  //     }

  //     // Then, add the classes from the category definition
  //     foreach (MetaModel::GetClasses($this->m_sCategories) as $sClass)
  //     {
  //       if (MetaModel::IsValidClass($sClass))
  //       {
  //         $this->m_aValues[$sClass] = MetaModel::GetName($sClass);
  //       }
  //       else
  //       {
  //         unset($this->m_aValues[$sClass]);
  //       }
  //     }

  //     return true;
  //   }
// }

class EventNotificationCreateObject extends EventNotification
{
  public static function Init()
  {
    $aParams = array
    (
      "category" => "core/cmdb,view_in_gui",
      "key_type" => "autoincrement",
      "name_attcode" => "",
      "state_attcode" => "",
      "reconc_keys" => array(),
      "db_table" => "priv_event_createobject",
      "db_key_field" => "id",
      "db_finalclass_field" => "",
      "display_template" => "",
      "order_by_default" => array('date' => false)
    );
    MetaModel::Init_Params($aParams);
    MetaModel::Init_InheritAttributes();
    // MetaModel::Init_AddAttribute(new AttributeText("to", array("allowed_values"=>null, "sql"=>"to", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
    // MetaModel::Init_AddAttribute(new AttributeText("cc", array("allowed_values"=>null, "sql"=>"cc", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
    // MetaModel::Init_AddAttribute(new AttributeText("bcc", array("allowed_values"=>null, "sql"=>"bcc", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
    // MetaModel::Init_AddAttribute(new AttributeText("from", array("allowed_values"=>null, "sql"=>"from", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
    // MetaModel::Init_AddAttribute(new AttributeText("subject", array("allowed_values"=>null, "sql"=>"subject", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
    // MetaModel::Init_AddAttribute(new AttributeHTML("body", array("allowed_values"=>null, "sql"=>"body", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));
    // MetaModel::Init_AddAttribute(new AttributeTable("attachments", array("allowed_values"=>null, "sql"=>"attachments", "default_value"=>null, "is_null_allowed"=>true, "depends_on"=>array())));

    // Display lists
    MetaModel::Init_SetZListItems('details', array('date', 'message', 'userinfo', 'trigger_id', 'action_id', 'object_id')); // Attributes to be displayed for the complete details
    MetaModel::Init_SetZListItems('list', array('date', 'message')); // Attributes to be displayed for a list

    // Search criteria
    //    MetaModel::Init_SetZListItems('standard_search', array('name')); // Criteria of the std search form
    //    MetaModel::Init_SetZListItems('advanced_search', array('name')); // Criteria of the advanced search form
  }
}

?>