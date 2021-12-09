<?php

class Shipay_Magento19_Block_Adminhtml_Order_View_Tab_Contents
extends Mage_Adminhtml_Block_Template
implements Mage_Adminhtml_Block_Widget_Tab_Interface {

  /**
   * Construct
   */
  public function _construct() {
    parent::_construct();
    $this->setTemplate('shipay/magento19/order/view/tab/contents.phtml');
  }

  public function canShowTab() {
    return true;
  }

  public function isHidden() {
    return false;
  }

  /**
   * Function to get/set tab label
   */
  public function getTabLabel() {
    return "Detalhes do pagamento";
  }

  /**
   * Function to get/set tab title
   */
  public function getTabTitle() {
    return "Detalhes do pagamento";
  }

  /**
   * Function to get wallet name
   * @return string
   */ 
  public function getWalletName() {
    return $this->getPayment()->getAdditionalInformation('wallet');
  }

  /**
   * Function to get deep link
   * @return string
   */
  public function getDeepLink() {
    return $this->getPayment()->getAdditionalInformation('deep_link');
  }

  /**
   * Function to get qr code base 64
   * @return string
   */
  public function getQrCodeBase64() {
    return $this->getPayment()->getAdditionalInformation('qr_code');
  }

  /**
   * Function to get qr code text
   * @return string
   */
  public function getQrCodeText() {
    return $this->getPayment()->getAdditionalInformation('qr_code_text');
  }

  /**
   * Function to get payment
   */
  public function getPayment() {
    return $this->getOrder()->getPayment();
  }

  /**
   * Function to get order
   */
  public function getOrder() {
    $order_id = $this->getRequest()->getParam('order_id');
    $order = Mage::getModel('sales/order')->load($order_id);
    return $order;
  }
}
