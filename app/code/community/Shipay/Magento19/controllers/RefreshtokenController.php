<?php

class Shipay_Magento19_RefreshtokenController extends Mage_Core_Controller_Front_Action {

  const URI = '/refresh-token';

  /**
   * Function to refresh token
   */
  public function indexAction() {
    $storeId = Mage::app()->getStore()->getStoreId();

    $response = $this->doRequest();
    if ($response != false) {
      Mage::getModel('core/config')->saveConfig('payment/shipay_keys/access_token', $response['access_token'], $storeId);
      Mage::getModel('core/config')->saveConfig('payment/shipay_keys/refresh_token', $response['refresh_token'], $storeId);
    }
  }

  /**
   * Function to do request
   * @return mixed
   */
  public function doRequest() {
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
}
