<?php

class Shipay_Magento19_Helper_Data extends Mage_Core_Helper_Abstract {

   /**
   * Function to return if is automaticinvoice
   * @return bool
   */
  public function isAutomaticInvoice()
  {
    $storeId = Mage::app()->getStore()->getStoreId();
    return Mage::getStoreConfig('payment/shipay_payments/automatic_invoice', $storeId);
  }

}
