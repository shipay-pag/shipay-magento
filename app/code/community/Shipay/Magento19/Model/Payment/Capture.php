<?php

class Shipay_Magento19_Model_Payment_Capture {

  /**
   * Function authorize of method payment
   * @param Varien_Object $payment
   * @param $amount
   * @param $info
   */
  public function authorize($payment, $amount, $info) {
    $data = json_decode($info->getAdditionalInformation('data'), true);
    $quote = $this->getQuote();
    $createRequestClass = $this->getCreateRequestClass();
    $request = $createRequestClass->createRequest($data, $info);
    $requestArray = json_decode($request, true);
    $connector = new Shipay_Magento19_Service_Connector();

    if (!empty($requestArray['expiration'])) {
      $response = $connector->doRequest($request, 'POST', '/orderv');
    } else {
      $response = $connector->doRequest($request, 'POST', '/order');
    }

    $this->saveAdditionalInformations($info, json_decode($response, true));

    $payment->setTransactionId($quote->getReservedOrderId());
    $payment->setIsTransactionClosed(false);
    $payment->setShouldCloseParentTransaction(false);
  }

  /**
   * Function to save response informations in additional information
   * @param array $response
   * @param $info
   */
  public function saveAdditionalInformations($info, $response) {
    $info->setAdditionalInformation("deep_link", $response['deep_link']);
    $info->setAdditionalInformation("order_id", $response['order_id']);
    $info->setAdditionalInformation("qr_code", $response['qr_code']);
    $info->setAdditionalInformation("qr_code_text", $response['qr_code_text']);
    $info->setAdditionalInformation("status", $response['status']);
    $info->setAdditionalInformation("wallet", $response['wallet']);
    $info->setAdditionalInformation("fullresponse", json_encode($response));
    $info->setCcTransId($response['order_id']);
  }

  /**
   * Get class create request
   * @return Shipay_Magento19_Model_Payment_CreateRequest
   */
  public function getCreateRequestClass() {
    return new Shipay_Magento19_Model_Payment_CreateRequest();
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
