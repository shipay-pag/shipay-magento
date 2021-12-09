<?php

class Shipay_Magento19_Block_Adminhtml_Form_Register extends Mage_Adminhtml_Block_Widget_Form_Container {

  /**
   * Function constructor
   */
  public function __construct() {
    parent::__construct();
    $this->_objectId = 'id';
    $this->_controller = 'adminhtml_register';
    $this->_blockGroup = 'shipay_magento19/adminhtml_form_register_form';
  }

  /**
   * Get header text
   * @return string
   */
  public function getHeaderText() {
    return 'Registro';
  }

  /**
   * Get header css class
   * @return string
   */
  public function getHeaderCssClass() {
    return 'icon-head head-cms-page';
  }
}
