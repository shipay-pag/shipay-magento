<?php

class Shipay_Magento19_Model_Payment_Refund {

  /**
   * Function to refund order
   * @param $creditmemo
   * @param $payment
   */
  public function refund($creditmemo, $payment) {
    $debug = $this->getDebug();
    $orderId = $payment->getCcTransId();

    $response = $this->doRequest($orderId);

    if ($debug) {
      Mage::log("[CANCEL ORDER SHIPAY] - Response: $response", null, 'shipay.log', true);
    }

    $status = $this->getStatus($response);
    if ($status == 'pending' || $status == 'approved') {
      Mage::throwException("A requisição de cancelamento falhou. O pedido continua pendente.");
    }
  }

  /**
   * Function do request refund
   * @param string $orderId
   * @return string
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
      CURLOPT_CUSTOMREQUEST => 'DELETE',
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $accessToken",
      ],
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
  }

  /**
   * Function to get if debug are on or off
   * @return bool
   */
  public function getDebug(): bool {
    $getDebugClass = new Shipay_Magento19_Resource_GetDebug();
    return $getDebugClass->getDebug();
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
   * Function to get environment
   * @return string
   */
  public function getEnvironment(): string {
    $classGetEnvironment = new Shipay_Magento19_Resource_GetEnvironment();
    return $classGetEnvironment->getEnvironment();
  }

  /**
   * Function to get access token
   * @return string
   */
  public function getAccessToken() {
    $classGetAccessToken = new Shipay_Magento19_Resource_GetAccessToken();
    return $classGetAccessToken->getAccessToken();
  }
}
