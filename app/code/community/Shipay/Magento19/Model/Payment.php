<?php

class Shipay_Magento19_Model_Payment extends Mage_Payment_Model_Method_Abstract {

  protected $_canCapture = true;
  protected $_canRefund = true;
  protected $_isGateway = true;
  protected $_canCancelInvoice = true;
  protected $_canUseCheckout = true;
  protected $_canUseInternal = true;
  protected $_canReviewPayment = false;
  protected $_code = 'shipay_payments';
  protected $_formBlockType = 'shipay_magento19/form_payment';

  /**
   * Function to get data form frontend
   */
  public function assignData($data) {
    if (!($data instanceof Varien_Object)) {
      $data = new Varien_Object($data);
    }
    $info = $this->getInfoInstance();

    $this->validateParams($data);
    $assignData = $this->prepareAssignDataToSave($data);
    $info->setAdditionalInformation('data', $assignData);

    return $this;
  }

  /**
   * Function refund (credit memo) of method payment
   * @param $creditmemo
   * @param $payment
   */
  public function processCreditmemo($creditmemo, $payment) {
    $refundClass = new Shipay_Magento19_Model_Payment_Refund();
    $refundClass->refund($creditmemo, $payment);
  }

  /**
   * Function authorize of method payment
   * @param Varien_Object $payment
   * @param $amount
   */
  public function authorize(Varien_Object $payment, $amount) {
    $info = $this->getInfoInstance();
    $captureClass = new Shipay_Magento19_Model_Payment_Capture();
    $captureClass->authorize($payment, $amount, $info);
  }

  /**
   * Function to validate params
   * @param Varien_Object $data
   */
  public function validateParams($data) {
    $quote = $this->getQuote();
    $customerId = $quote->getCustomerId();
    $address = $quote->getShippingAddress();

    $document = $this->getTaxVat($data, $customerId);
    $numberPhone = $address->getTelephone();
    $name = $address->getFirstname() . ' ' . $address->getLastname();
    $email = $address->getEmail();
    $walletName = $data->getData('wallet-name');

    //validate customer infos
    $customerValidator = $this->getCustomerValidatorClass();
    $isValid = $customerValidator->validateNumberPhone($numberPhone);
    if ($isValid == false) {
      Mage::throwException("Número de telefone inválido. Verifique por favor.");
    }
    $isValid = $customerValidator->validateEmail($email);
    if ($isValid == false) {
      Mage::throwException("Email inválido. Verifique por favor.");
    }
    $isValid = $customerValidator->validateName($name);
    if ($isValid == false) {
      Mage::throwException("Nome informado inválido. Verifique por favor.");
    }

    //validate if select method payment
    if ($walletName == "" || !isset($walletName)) {
      Mage::throwException("Por favor, selecione uma forma de pagamento.");
    }

    //validate document
    $documentValidator = $this->getDocumentsValidatorClass();
    $isValid = $documentValidator->validateDocument($document);
    if ($isValid == false) {
      Mage::throwException("Documento (CPF ou CNPJ) inválido. Verifique por favor.");
    }
  }

  /**
   * Function to prepare array for save assign data
   * @param Varien_Object $data
   * @return string
   */
  public function prepareAssignDataToSave($data) {
    $customerId = $this->getQuote()->getCustomerId();
    $array = [
      'method' => $data->getData('shipay_payments'),
      'wallet-name' => $data->getData('wallet-name'),
      'pix-dict-key' => $data->getData('pix-dict-key'),
      'client_document' => $this->getTaxVat($data, $customerId)
    ];

    return json_encode($array);
  }

  /**
   * Function to get tax vat
   * @param $data
   * @param $customerId
   * @return string
   */
  public function getTaxVat($data, $customerId) {
    $classGetTaxVat = new Shipay_Magento19_Resource_GetTaxVat();
    $document = $classGetTaxVat->getTaxVat($data, $customerId);
    return $document;
  }

  /**
   * Get checkout session
   * @return Mage_Checkout_Session
   */
  public function getCheckout() {
    return Mage::getSingleton('checkout/session');
  }

  /**
   * Get current quote
   * @return Mage_Sales_Model_Quote
   */
  public function getQuote() {
    return $this->getCheckout()->getQuote();
  }

  /**
   * Get class customer validator
   * @return Shipay_Magento19_Validation_CustomerValidator
   */
  public function getCustomerValidatorClass() {
    return new Shipay_Magento19_Validation_CustomerValidator();
  }

  /**
   * Get class documents validator
   * @return Shipay_Magento19_Validation_DocumentValidator
   */
  public function getDocumentsValidatorClass() {
    return new Shipay_Magento19_Validation_DocumentValidator();
  }
}
