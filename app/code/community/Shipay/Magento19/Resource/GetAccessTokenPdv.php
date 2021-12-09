<?php

class Shipay_Magento19_Resource_GetAccessTokenPdv {

  const URI = '/pdvsysauth';

  /**
   * Function to get access token of system pdv
   * @return string
   */
  public function getAccessTokenPdv(): string {
    $accessTokenPdv = $this->generateAccessTokenPdv();
    return $accessTokenPdv;
  }

  /**
   * Function to generate access token of system pdv
   * @return string
   */
  public function generateAccessTokenPdv(): string {
    $request = [
      'access_key' => Mage::getStoreConfig('payment/shipay_keys/access_key_pdv'),
      'pos_product_id' => Mage::getStoreConfig('payment/shipay_keys/pos_product_id')
    ];

    $response = $this->doRequest(json_encode($request));
    Mage::log("[-- ACCESS TOKEN PDV RESPONSE --] " . json_encode($response), null, 'shipay.log', true);

    Mage::getModel('core/config')->saveConfig('payment/shipay_keys/access_token_pdv', $response['access_token'] ?? '');
    Mage::getModel('core/config')->saveConfig('payment/shipay_keys/refresh_token_pdv', $response['refresh_token'] ?? '');

    return $response['access_token'] ?? '';
  }

  /**
   * Function to do request
   * @param string $request
   * @return array
   */
  public function doRequest($request): array {
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
      CURLOPT_POSTFIELDS => $request,
      CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
      ],
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return json_decode($response, true);
  }

  /**
   * Function to get environment
   * @return string
   */
  public function getEnvironment(): string {
    $classGetEnvironment = new Shipay_Magento19_Resource_GetEnvironment();
    return $classGetEnvironment->getEnvironment();
  }
}
