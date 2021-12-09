<?php

class Shipay_Magento19_PendingorderController extends Mage_Core_Controller_Front_Action {

  /**
   * Function to verify pending orders
   */
  public function indexAction() {
    $orderCollection = Mage::getModel('sales/order')
      ->getCollection()
      ->addFieldToFilter('status', ['eq' => 'pending_payment']);

    foreach ($orderCollection as $order) {
      $method = $order->getPayment()->getMethod();

      if ($method == 'shipay_payments') {
        $createdAt = $order->getCreatedAt();
        $createdAt = $this->sumDateToCompareShipay($createdAt);
        $currentDate = date("Y-m-d h:i:s");

        if ($createdAt < $currentDate) {
          $orderTransId = $order->getPayment()->getCcTransId();
          $response = $this->doRequest($orderTransId);
          $status = $this->getStatus($response);

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
  }

  /**
   * Function to sum 3 days in createdAt date order
   * @param string $createdAt
   * @return string
   */
  public function sumDateToCompareShipay($createdAt): string {
    return date("Y-m-d h:i:s", strtotime('+3 days', strtotime($createdAt)));
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
  public function doRequest($orderId) {
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
   * Function to get environment
   * @return string
   */
  public function getEnvironment() {
    $classGetEnvironment = new Shipay_Magento19_Resource_GetEnvironment();
    return $classGetEnvironment->getEnvironment();
  }
}
