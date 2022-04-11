<?php

class Shipay_Magento19_Model_Observer {

  const URI = '/refresh-token';
  
  /**
   * Function to refresh token
   */
  public function refreshToken() {
    $storeId = Mage::app()->getStore()->getStoreId();

    $response = $this->doRefreshRequest();
    if ($response != false) {
      Mage::getModel('core/config')->saveConfig('payment/shipay_keys/access_token', $response['access_token'], $storeId);
      Mage::getModel('core/config')->saveConfig('payment/shipay_keys/refresh_token', $response['refresh_token'], $storeId);
    }
  }

  /**
   * Function to do request
   * @return mixed
   */
  public function doRefreshRequest() {
    $refreshToken = $this->getRefreshToken();
    if ($refreshToken == false) {
      return false;
    }
    $environment = $this->getEnvironment();

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => ($environment . self::URI),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 60,
      CURLOPT_FOLLOWLOCATION => TRUE,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $refreshToken",
        "Content-Type: application/json"
      ],
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($httpCode != 200) {
      return false;
    }

    return json_decode($response, true);
  }

  /**
   * Function to get refresh token
   * @return mixed
   */
  public function getRefreshToken() {
    $storeId = Mage::app()->getStore()->getStoreId();

    try {
      $refreshToken = Mage::getStoreConfig('payment/shipay_keys/refresh_token', $storeId);
      if (isset($refreshToken) && !empty($refreshToken)) {
        return $refreshToken;
      } else {
        return false;
      }
    } catch (\Throwable $th) {
      return false;
    }
  }

  /**
   * Function to get environment
   * @return string
   */
  public function getEnvironment() {
    $classGetEnvironment = new Shipay_Magento19_Resource_GetEnvironment();
    return $classGetEnvironment->getEnvironment();
  }

  /**
   * Function to verify order status
   */
  public function verifyOrderStatus() {
    $orderCollection = Mage::getModel('sales/order')
      ->getCollection()
      ->addFieldToFilter('status', ['eq' => 'pending_payment']);

    foreach ($orderCollection as $order) {
      $method = $order->getPayment()->getMethod();

      if ($method == 'shipay_payments') {
        $orderTransId = $order->getPayment()->getCcTransId();
        $response     = $this->doVerifyRequest($orderTransId);
        $status       = $this->getStatus($response);

        if ($status == 'approved') {
          $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
          $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
          $order->save();
        } else if ($status == 'cancelled' || $status == 'expired') {
          $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
          $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED, true);
          $order->cancel();
          $order->save();
        }
      }
    }
  }

  /**
   * Function to get status from reponse
   * @param string $response
   * @return string
   */
  public function getStatus($response) {
    $arrayResponse = json_decode($response, true);
    $status = $arrayResponse['status'];
    return $status;
  }

  /**
   * Function to do request query
   * @param string $orderId
   */
  public function doVerifyRequest($orderId) {
    $accessToken = $this->getAccessToken();
    $environment = $this->getEnvironment();

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => ($environment . "/order/$orderId"),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 60,
      CURLOPT_FOLLOWLOCATION => TRUE,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
      ],
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($httpCode > 299 || $httpCode < 200) {
      $arrayFake = [
        'status' => 'not found'
      ];
      return json_encode($arrayFake);
    }

    return $response;
  }

  /**
   * Function to get access token
   * @return string
   */
  public function getAccessToken() {
    $classGetAccessToken = new Shipay_Magento19_Resource_GetAccessToken();
    return $classGetAccessToken->getAccessToken();
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
