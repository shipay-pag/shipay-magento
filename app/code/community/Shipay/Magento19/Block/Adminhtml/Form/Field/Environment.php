<?php

class Shipay_Magento19_Block_Adminhtml_Form_Field_Environment {

  /**
   * Funtion to get options environment
   * @return array
   */
  public function toOptionArray() {
    $array = [
      [
        'value' => 'sandbox',
        'label' => 'Sandbox - Ambiente para Testes'
      ],
      [
        'value' => 'production',
        'label' => 'Produção'
      ]
    ];

    return $array;
  }
}
