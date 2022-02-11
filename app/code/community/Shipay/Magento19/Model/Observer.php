<?php

class Shipay_Magento19_Model_Observer {

  /**
   * Function to refresh token
   */
  public function refreshToken() {
    $url = Mage::getBaseUrl() . 'shipaymagento19/refreshtoken';
  
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 60,
      CURLOPT_FOLLOWLOCATION => TRUE,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET"
    ));
    curl_exec($curl);
  }

  /**
   * Function to verify order status
   */
  public function verifyOrderStatus() {
    $url = Mage::getBaseUrl() . 'shipaymagento19/pendingorder';
  
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 60,
      CURLOPT_FOLLOWLOCATION => TRUE,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET"
    ));
    curl_exec($curl);  
  }

  /**
   * Function to update order status
   * @param Varien_Event_Observer $observer
   */
  public function updateStatusOrder(Varien_Event_Observer $observer) {
    $order = $observer->getOrder();
    $payment = $order->getPayment();
    $method = $payment->getMethod();

    if ($method == 'shipay_payments') {
      $status = $this->getOrderStatus($payment);
      if ($status == 'pending') {
        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
        $order->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true);
        $order->save();
      } else if ($status == 'cancelled') {
        $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
        $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED, true);
        $order->save();
      }
    }
  }

  /**
   * Function to generate order status
   * @param Varien_Event_Observer $observer
   */
  public function generateAccessToken(Varien_Event_Observer $observer) {
    try {
      $getAccessTokenClass = new Shipay_Magento19_Resource_GetAccessToken();
      $getAccessTokenClass->generateAccessToken();
    } catch (\Throwable $th) {
    }
  }

  /**
   * Function to get status from additional information
   * @param Mage_Sales_Model_Order_Payment $payment
   * @return string
   */
  protected function getOrderStatus($payment): string
  {
    $status = '';
    $status = $payment->getAdditionalInformation('status');
    if ($status == null || $status == '') {
      $status = 'pending';
    }
    return $status;
  }
}
