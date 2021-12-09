<?php

class Shipay_Magento19_Block_Form_Payment extends Mage_Payment_Block_Form
{

  /**
   * Construct to do reference model, block and phtml 
   */
  protected function _construct()
  {
    parent::_construct();
    $this->setTemplate('shipay/magento19/form/payment.phtml');
  }

  protected function _getConfig()
  {
    return Mage::getSingleton('payment/config');
  }

  /**
   * Function to get wallets
   * @return array
   */
  public function getWallets()
  {
    $classGetWallets = new Shipay_Magento19_Resource_GetWallets();
    $wallets = $classGetWallets->getWallets();
    return $wallets;
  }

  /**
   * Function to get field tax document
   * @return bool
   */
  public function getFieldCaptureTax()
  {
    $storeId = Mage::app()->getStore()->getStoreId();
    return Mage::getStoreConfig('payment/shipay_payments/capture_tax', $storeId);
  }
}
