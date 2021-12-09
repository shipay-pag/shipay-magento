<?php

class Shipay_Magento19_Resource_GetEnvironment {

  const PRODUCTION = 'https://api.shipay.com.br';
  const SANDBOX = 'https://api-staging.shipay.com.br';

  /**
   * Function to get environment
   * @return string
   */
  public function getEnvironment(): string {
    $storeId = Mage::app()->getStore()->getStoreId();

    $environment = Mage::getStoreConfig('payment/shipay_payments/environment', $storeId);
    if ($environment == 'production') {
      return self::PRODUCTION;
    } else {
      return self::SANDBOX;
    }
  }
}
