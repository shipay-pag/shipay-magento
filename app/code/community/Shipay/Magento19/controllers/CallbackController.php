<?php

class Shipay_Magento19_CallbackController extends Mage_Core_Controller_Front_Action {

  const APPROVED = 'Pedido aprovado';
  const CANCELLED = 'Pedido cancelado';
  const PENDING = 'Pedido pendente';

  /**
   * Function to receive callback notification form shypay
   */
  public function indexAction() {
    $debug = $this->getDebug();
    $post = new Zend_Controller_Request_Http();
    $data = json_decode($post->getRawBody(), true);

    $orderId = $data['order_id'];

    $connector = $this->getConnector();
    $response = $connector->doRequest("0", "GET", "/order/$orderId");

    $internalOrderId = $this->getInternalOrderId($response);
    $status = $this->getStatus($response);

    if ($status == 'approved') {
      $order = Mage::getModel('sales/order')->loadByIncrementId($internalOrderId);

      $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
      $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
      $invoice->register();
      $invoice->getOrder()->setIsInProcess(true);
      $transactionSave = Mage::getModel('core/resource_transaction')
        ->addObject($invoice)
        ->addObject($invoice->getOrder());
      $transactionSave->save();

      $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true);
      $order->setStatus(Mage_Sales_Model_Order::STATE_PROCESSING, true);
      $order->save();
      if ($debug) {
        $this->writeMessage(self::APPROVED, $internalOrderId);
      }
    } else if ($status == 'cancelled' || $status == 'expired') {
      $order = Mage::getModel('sales/order')->loadByIncrementId($internalOrderId);
      $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
      $order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED, true);
      $order->cancel();
      $order->save();
      if ($debug) {
        $this->writeMessage(self::CANCELLED, $internalOrderId);
      }
    } else if ($status == 'pending') {
      if ($debug) {
        $this->writeMessage(self::PENDING, $internalOrderId);
      }
    }
  }

  /**
   * Function get class connector
   * @return Shipay_Magento19_Service_Connector
   */
  public function getConnector() {
    return new Shipay_Magento19_Service_Connector();
  }

  /**
   * Function to get status from reponse
   * @param string $response
   * @return string
   */
  public function getStatus($response) {
    $arrayResponse = json_decode($response, true);
    $status = $arrayResponse['status'];
    return $status;
  }

  /**
   * Function to get internal order id from response
   * @param string $response
   * @return string
   */
  public function getInternalOrderId($response) {
    $arrayResponse = json_decode($response, true);
    $internalOrderId = $arrayResponse['external_id'];
    return $internalOrderId;
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
   * Function to write log shipay
   * @param string $message
   * @param string $orderId
   */
  public function writeMessage($message, $orderId) {
    Mage::log("[CALLBACK SHIPAY - Order Id = $orderId] $message", null, 'shipay.log', true);
  }
}
