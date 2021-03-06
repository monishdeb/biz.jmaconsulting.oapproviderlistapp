<?php

class CRM_Oapproviderlistapp_Form_CustomDataByType extends CRM_Core_Form {

  /**
   * Preprocess function.
   */
  public function preProcess() {

    $this->_type = $this->_cdType = CRM_Utils_Request::retrieve('type', 'String', CRM_Core_DAO::$_nullObject, TRUE);
    $this->_subType = CRM_Utils_Request::retrieve('subType', 'String');
    $this->_subName = CRM_Utils_Request::retrieve('subName', 'String');
    $this->_groupCount = CRM_Utils_Request::retrieve('cgcount', 'Positive');
    $this->_entityId = CRM_Utils_Request::retrieve('entityID', 'Positive');
    $this->_groupID = CRM_Utils_Request::retrieve('groupID', 'Positive');
    $this->_onlySubtype = CRM_Utils_Request::retrieve('onlySubtype', 'Boolean');
    $this->_action = CRM_Utils_Request::retrieve('action', 'Alphanumeric');
    $this->assign('cdType', FALSE);
    $this->assign('cgCount', $this->_groupCount);

    $contactTypes = CRM_Contact_BAO_ContactType::contactTypeInfo();
    if (array_key_exists($this->_type, $contactTypes)) {
      $this->assign('contactId', $this->_entityId);
    }
    if (!is_array($this->_subType) && strstr($this->_subType, CRM_Core_DAO::VALUE_SEPARATOR)) {
      $this->_subType = str_replace(CRM_Core_DAO::VALUE_SEPARATOR, ',', trim($this->_subType, CRM_Core_DAO::VALUE_SEPARATOR));
    }
    CRM_Custom_Form_CustomData::setGroupTree($this, $this->_subType, $this->_groupID, $this->_onlySubtype);

    $this->assign('suppressForm', TRUE);
    $this->controller->_generateQFKey = FALSE;
  }

  /**
   * Set defaults.
   *
   * @return array
   */
  public function setDefaultValues() {
    $defaults = array();
    CRM_Core_BAO_CustomGroup::setDefaults($this->_groupTree, $defaults, FALSE, FALSE, $this->get('action'));
    $values = CRM_Core_BAO_Cache::getItem("custom params", CRM_Utils_Array::value('qf', $_GET));
    if (!empty($values)) {
      $defaults = array_merge($defaults, $values);
      CRM_Core_BAO_Cache::deleteGroup("custom params", CRM_Utils_Array::value('qf', $_GET));
    }
   return $defaults;
  }

  /**
   * Build quick form.
   */
  public function buildQuickForm() {
    $this->addElement('hidden', 'hidden_custom', 1);
    $this->addElement('hidden', "hidden_custom_group_count[{$this->_groupID}]", $this->_groupCount);
    CRM_Core_BAO_CustomGroup::buildQuickForm($this, $this->_groupTree);
  }

  public function getTemplateFileName() {
    return 'CRM/Custom/Form/CustomDataByType.tpl';
  }
}
