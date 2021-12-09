<?php

class Shipay_Magento19_Adminhtml_RegisterController extends Mage_Adminhtml_Controller_Action {

  const URI = '/registration/pub';

  /**
   * Function to show form
   */
  public function indexAction() {
    $this->loadLayout();
    $this->_setActiveMenu('shipay/shipay_form');
    $this->renderLayout();
  }

  /**
   * Function to save/send registration shipay
   */
  public function saveAction() {
    $data = $this->getRequest()->getPost();

    $arrayPosNames = $this->getArrayStorePosNames($data['store_pos_names']);
    $postalCode = preg_replace('/[^0-9]/is', '', $data['store_postal_code']);

    $registerRequestClass = new Shipay_Magento19_Model_RegisterRequest(
      $data['customer_email'],
      $data['customer_name'],
      $data['store_cnpj_cpf'],
      $data['store_name'],
      $arrayPosNames,
      $data['user_email'],
      $data['user_full_name'],
      true,
      $postalCode
    );

    $request = $registerRequestClass->getRequest();
    $response = json_decode($this->doRequest($request), true);

    $accessKey = $response['customer_access_key'];
    $secretKey = $response['customer_secret_key'];
    $clientId = $response['client_ids']['0']['client_id'];
    $this->saveKeyShipay($accessKey, $secretKey, $clientId);

    $urlShipayLinkResetPass = $response['link_reset_password'];
    header("Location: $urlShipayLinkResetPass");
    exit;
  }

  /**
   * Function to get array store pos names
   * @param array $dynamicRowPosNames
   * @return array
   */
  public function getArrayStorePosNames($dynamicRowPosNames): array {
    $array = [];
    foreach ($dynamicRowPosNames as $names) {
      $array[] = $names['from_qty'];
    }
    //elimina a última posição do array
    array_pop($array);
    return $array;
  }

  /**
   * Function to save keys from shipay
   * @param string $accessKey
   * @param string $secretKey
   * @param string $clientId
   */
  public function saveKeyShipay($accessKey, $secretKey, $clientId) {
    $coreConfig = Mage::getModel('core/config');

    $coreConfig->saveConfig('payment/shipay_keys/access_key', $accessKey);
    $coreConfig->saveConfig('payment/shipay_keys/secret_key', $secretKey);
    $coreConfig->saveConfig('payment/shipay_keys/client_id', $clientId);
  }

  /**
   * Function to do request shipay registration
   * @param string $request
   */
  public function doRequest($request) {
    $accessToken = $this->getAccessTokenPdv();
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
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
      ],
    ));

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    if ($httpCode < 200 || $httpCode > 299) {
      $message = json_decode($response, true)['message'];
      $this->showErrorShipay($message, $httpCode);
    }

    return $response;
  }

  /**
   * Function to get access token
   * @return string
   */
  public function getAccessTokenPdv() {
    $classGetAccessToken = new Shipay_Magento19_Resource_GetAccessTokenPdv();
    return $classGetAccessToken->getAccessTokenPdv();
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
   * Function to show "page/print error"
   * @param string $message
   * @param string $httpCode
   */
  public function showErrorShipay($message, $httpCode) {
    $urlMagentoAdmin = Mage::getBaseUrl() . "admin";
    print_r("<center>");
    print_r("<h1 style='color:#d44b05;'>O seu Registro na Shipay falhou!</h1>");
    print_r("<h2 style='color:#314e58;'>Erro: [$httpCode] $message</h2>");
    print_r("<h3><a href=" . $urlMagentoAdmin . ">Voltar</a></h3>");
    print_r("</center>");
    exit;
  }
}
