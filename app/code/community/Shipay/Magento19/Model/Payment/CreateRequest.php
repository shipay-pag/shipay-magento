<?php

class Shipay_Magento19_Model_Payment_CreateRequest {

  /**
   * Function to create request
   * @param $data
   * @param $info
   * @return string
   */
  public function createRequest($data, $indo) {
    $quote = $this->getQuote();
    $customerId = $quote->getCustomerId();
    $address = $quote->getShippingAddress();
    $pixDictKey = $data['pix-dict-key'];

    $paymentShipayRequest = new Shipay_Magento19_Model_Payment_PaymentShipayRequest(
      $this->getTaxVat($data, $customerId),
      $address->getEmail(),
      $address->getName(),
      $address->getTelephone(),
      Mage::getBaseUrl() . 'shipaymagento19/callback',
      $quote->getReservedOrderId(),
      $quote->getBaseGrandTotal(),
      $data['wallet-name'],
      $this->calculateCart(round($this->getShippingValue(),2)),
      $this->getExpiration(),
      $pixDictKey
    );

    return $paymentShipayRequest->getRequest();
  }

  /**
   * Function to get shipping value
   * @return string
   */
  public function getShippingValue() {
    $quote = $this->getQuote();
    $grandTotal = $quote->getBaseGrandTotal();
    $subTotal = $quote->getSubtotal();
    return ($grandTotal - $subTotal);
  }

  /**
   * Function to get expiration time
   * @return string
   */
  public function getExpiration() {
    $storeId = Mage::app()->getStore()->getStoreId();
    return Mage::getStoreConfig('payment/shipay_payments/pix_expiration', $storeId);
  }

  /**
   * Function to calculate cart
   * @param $shippingValue
   * @return array
   */
  public function calculateCart($shippingValue) {
    $quote = $this->getQuote();
    $array = [];

    $items = $quote->getAllItems();
    foreach ($items as $item) {
      if ($item->getProductType() == 'simple' || $item->getProductType() == 'grouped') {
        if ($item->getPrice() == 0) {
          $parentItem = $item->getParentItem();
          $price = $parentItem->getPrice();
        } else {
          $price = $item->getPrice();
        }
        $cartRequest = new Shipay_Magento19_Model_CartRequest($item->getName(), $item->getQty(), $item->getSku(), $price);
        $array[] = $cartRequest;
      }
    }

    if ($shippingValue != "0") {
      $cartRequest = new Shipay_Magento19_Model_CartRequest("Shipping", "1", "Shipping", $shippingValue);
      $array[] = $cartRequest;
    }

    return $array;
  }

  /**
   * Function to get tax vat
   * @param $data
   * @param $customerId
   * @return string
   */
  public function getTaxVat($data, $customerId) {
    $getTaxVatClass = new Shipay_Magento19_Resource_GetTaxVat();
    return $getTaxVatClass->getTaxVat($data, $customerId);
  }

  /**
   * Get current checkout session
   * @return Mage_Checkout_Model_Session
   */
  public function getCheckout() {
    return Mage::getSingleton('checkout/session');
  }

  /**
   * Get current quote
   * @return Mage_Sales_Model_Quote
   */
  public function getQuote() {
    return $this->getCheckout()->getQuote();
  }
}
