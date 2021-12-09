<?php

class Shipay_Magento19_Resource_GetTaxVat {

  /**
   * Function to get tax vat
   * @param array $data
   * @param string $customerId
   * @return string 
   */
  public function getTaxVat($data, $customerId): string {
    $storeId = Mage::app()->getStore()->getStoreId();
    $taxDocument = Mage::getStoreConfig('payment/shipay_payments/capture_tax', $storeId);
    if ($taxDocument) {
      $document = $data['client_document'];
      return preg_replace('/[^0-9]/is', '', $document);
    } else {
      $customer = Mage::getModel('customer/customer')->load($customerId);
      $document = $customer->getData('taxvat');
      return preg_replace('/[^0-9]/is', '', $document);
    }
  }
}
