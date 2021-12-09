<?php

class Shipay_Magento19_WalletsController extends Mage_Core_Controller_Front_Action {

  /**
   * Function to get wallets from api shipay
   */
  public function indexAction() {
    $classGetWallets = new Shipay_Magento19_Resource_GetWallets();
    $response = $classGetWallets->getWallets();

    echo json_encode($response);
  }
}
