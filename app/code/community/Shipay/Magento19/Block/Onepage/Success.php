<?php

class Shipay_Magento19_Block_Onepage_Success extends Mage_Core_Block_Template {

  /**
   * Function to get deep link
   * @return string
   */
  public function getDeepLink() {
    return $this->getPayment()->getAdditionalInformation('deep_link');
  }

  /**
   * Function to get wallet name
   * @return string
   */ 
  public function getWalletName() {
    return $this->getPayment()->getAdditionalInformation('wallet');
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
   * Function to get order id
   * @return string
   */
  public function getOrderId() {
    return $this->getPayment()->getCcTransId();
  }

  /**
   * Function to get payment
   */
  public function getPayment() {
    return $this->getOrder()->getPayment();
  }

  /**
   * Function to get last real order
   */
  public function getOrder() {
    $checkout = $this->getCheckout();
    return $checkout->getLastRealOrder();
  }

  /**
   * Function to get checkout session
   * @return Mage_Checkout_Model_Session
   */
  public function getCheckout() {
    return Mage::getSingleton('checkout/session');
  }
}
