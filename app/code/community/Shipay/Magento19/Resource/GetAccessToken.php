<?php

class Shipay_Magento19_Resource_GetAccessToken {

  const URI = '/pdvauth';

  /**
   * Function to get access token
   * @return string
   */
  public function getAccessToken(): string {
    return $this->generateAccessToken();
  }

  /**
   * Function to generate access token
   * @return string
   */
  public function generateAccessToken(): string {
    $request = [
      'access_key' => Mage::helper('core')->decrypt(Mage::getStoreConfig('payment/shipay_keys/access_key')),
      'client_id' => Mage::helper('core')->decrypt(Mage::getStoreConfig('payment/shipay_keys/client_id')),
      'secret_key' => Mage::helper('core')->decrypt(Mage::getStoreConfig('payment/shipay_keys/secret_key'))
    ];

    $response = $this->doRquest(json_encode($request));

    Mage::getModel('core/config')->saveConfig('payment/shipay_keys/access_token', $response['access_token']);
    Mage::getModel('core/config')->saveConfig('payment/shipay_keys/refresh_token', $response['refresh_token']);
    
    return $response['access_token'];
  }

  /**
   * Function to do request
   * @param string $request
   * @return array
   */
  protected function doRquest($request): array {
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
  protected function getEnvironment(): string {
    $classGetEnvironment = new Shipay_Magento19_Resource_GetEnvironment();
    return $classGetEnvironment->getEnvironment();
  }

  /**
   * Function to validade if generate new token or no
   * @return bool
   */
  protected function validateIfGenerateNewToken(): bool {
    $model = new Mage_Core_Model_Config_Data();
    $collection = $model->getCollection()->addFieldToFilter('path', ['eq' => 'payment/shipay_keys/access_token'])->getItems();

    foreach ($collection as $item) {
      $dateToken = date($item->getUpdatedAt());
      $dateValidate = date('Y-m-d h:m:s');
      $dateValidate = date('Y-m-d h:m:s', strtotime($dateValidate . ' - 1 days'));

      if ($dateToken < $dateValidate) {
        return true;
      } else {
        return false;
      }
    }
    return false;
  }
}
