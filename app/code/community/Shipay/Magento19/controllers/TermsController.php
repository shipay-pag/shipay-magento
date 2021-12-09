<?php

class Shipay_Magento19_TermsController extends Mage_Core_Controller_Front_Action
{
  const URI = '/contract/version';

  /**
   * Function to get html of terms and conditions
   */
  public function indexAction() {
    $responseHtml = $this->doRequest();
    $html = '';
    $html = $html.$responseHtml;
    echo $html;
  }

  /**
   * Function to do request for get html terms and conditions
   * @return string
   */
  public function doRequest(): string {
    $environment = $this->getEnvironment() . self::URI;
    $token = $this->getAccessTokenPdv();

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => ($environment),
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 60,
      CURLOPT_FOLLOWLOCATION => TRUE,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $token",
      ],
    ));

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
  }

  /**
   * Function to get url environment
   * @return string
   */
  public function getEnvironment(): string {
    $getEnvironmentClass = new Shipay_Magento19_Resource_GetEnvironment();
    return $getEnvironmentClass->getEnvironment();
  }

  /**
   * Function to get access token pdv
   * @return string
   */
  public function getAccessTokenPdv(): string {
    $getAccessTokenPdvClass = new Shipay_Magento19_Resource_GetAccessTokenPdv();
    return $getAccessTokenPdvClass->getAccessTokenPdv();
  }
}