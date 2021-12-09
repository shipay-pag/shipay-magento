<?php

class Shipay_Magento19_Resource_GetWallets {

  const URI = '/v1/wallets';

  /**
   * Function get wallets
   * @return string
   */
  public function getWallets() {
    $response = $this->doRequest();
    return json_decode($response, true);
  }

  /**
   * Function to do request
   */
  public function doRequest() {
    $accessToken = $this->getAccessToken();
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
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
      ],
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

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
