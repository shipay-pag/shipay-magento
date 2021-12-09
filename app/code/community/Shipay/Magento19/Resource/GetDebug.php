<?php

class Shipay_Magento19_Resource_GetDebug {

  /**
   * Function to get if debug are on or off
   * @return bool
   */
  public function getDebug(): bool {
    $storeId = Mage::app()->getStore()->getStoreId();
    $debug = Mage::getStoreConfig('payment/shipay_payments/debug', $storeId);
    return $debug;
  }
}
