<?php

class Shipay_Magento19_QueryController extends Mage_Core_Controller_Front_Action {
  
  /**
   * Function to verify if order was paid
   */
  public function indexAction() {
    $post = new Zend_Controller_Request_Http();
    $orderId = $post->getParam('orderid');

    $connector = $this->getConnector();
    $response = $connector->doRequest("0", "GET", "/order/$orderId");

    $status = json_decode($response, true)['status'] ?? '';

    if ($status == 'approved') {
      echo json_encode('yes');
    } else {
      echo json_encode('no');
    }
  }
  
  /**
   * Function get class connector
   * @return Shipay_Magento19_Service_Connector
   */
  public function getConnector() {
    return new Shipay_Magento19_Service_Connector();
  }

}