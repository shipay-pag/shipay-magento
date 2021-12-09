<?php

class Shipay_Magento19_Service_Connector {

  /**
   * Function to get url by environment
   * @return string
   */
  protected function getUrlEnvironment(): string {
    $getEnvironmentClass = new Shipay_Magento19_Resource_GetEnvironment();
    return $getEnvironmentClass->getEnvironment();
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
   * Function to get access token
   * @param string $uri
   * @return string
   */
  protected function getAccessToken($uri): string {
    if ($uri == '/registration/pub') {
      $getAccessTokenPdvClass = new Shipay_Magento19_Resource_GetAccessTokenPdv();
      return $getAccessTokenPdvClass->getAccessTokenPdv();
    } else {
      $getAccessTokenClass = new Shipay_Magento19_Resource_GetAccessToken();
      return $getAccessTokenClass->getAccessToken();
    }
  }

  /**
   * Function to do request
   * @param string $data
   * @param string $method
   * @param string $uri
   * @return string
   */
  public function doRequest($data, $method, $uri): string {
    $debug = $this->getDebug();
    $token = $this->getAccessToken($uri);
    $url = $this->getUrlEnvironment() . $uri;

    if ($debug) {
      Mage::log("[-- REQUEST SHIPAY --] $data", null, 'shipay.log', true);
    }

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "$url");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    if ($method == "POST") {
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_POSTFIELDS, "$data");
    }
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      "Content-Type: application/json",
      "Authorization: Bearer $token",
      "x-shipay-order-type: e-order"
    ));
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($debug) {
      Mage::log("[-- RESPONSE SHIPAY --] $response", null, 'shipay.log', true);
    }

    if ($httpCode > 299 || $httpCode < 200) {
      if ($httpCode == 403) {
        Mage::throwException("[403] - Usuário não autorizado. Por favor verifique suas informações.");
      } else if ($httpCode == 500) {
        Mage::throwException("[500]- Shipay - Erro ao criar o pedido.");
      } else {
        Mage::throwException("Algo não ocorreu bem. Por favor verifique suas informações.");
      }
    }

    return $response;
  }
}
