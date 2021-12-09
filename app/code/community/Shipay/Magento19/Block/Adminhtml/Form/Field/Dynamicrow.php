<?php

class Shipay_Magento19_Block_Adminhtml_Form_Field_Dynamicrow extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract {

  /**
   * Prepare rendering the new field by adding all the needed columns
   */
  protected function _prepareToRender() {
    $this->addColumn('from_qty', ['label' => __('Nomes'), 'class' => 'required-entry pos-names-class']);
    $this->_addAfter = false;
    $this->_addButtonLabel = __('Adicionar');
  }
}
