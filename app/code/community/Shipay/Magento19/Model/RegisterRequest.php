<?php

class Shipay_Magento19_Model_RegisterRequest {

  const CUSTOMER_EMAIL = 'customer_email';
  const CUSTOMER_NAME = 'customer_name';
  const STORE_CNPJ_CPF = 'store_cnpj_cpf';
  const STORE_NAME = 'store_name';
  const STORE_POS_NAMES = 'store_pos_names';
  const USER_EMAIL = 'user_email';
  const USER_FULL_NAME = 'user_full_name';
  const TERMS_ACCEPTED = 'terms_accepted';
  const STORE_POSTAL_CODE = 'store_postal_code';

  /** @var string */
  protected $_customerEmail;

  /** @var string */
  protected $_customerName;

  /** @var string */
  protected $_storeCnpjCpf;

  /** @var string */
  protected $_storeName;

  /** @var array */
  protected $_storePosNames;

  /** @var string */
  protected $_userEmail;

  /** @var string */
  protected $_userFullName;

  /** @var string */
  protected $_termsAccepted;

  /** @var string */
  protected $_storePostalCode;

  /**
   * Function construct
   * @param string $customerEmail
   * @param string $customerName
   * @param string $storeCnpjCpf
   * @param string $storeName
   * @param array $storePosNames
   * @param string $userEmail
   * @param string $userFullName
   * @param string $termsAccepted
   * @param string $storePostalCode
   */
  public function __construct(
    $customerEmail,
    $customerName,
    $storeCnpjCpf,
    $storeName,
    $storePosNames,
    $userEmail,
    $userFullName,
    $termsAccepted,
    $storePostalCode
  ) {
    $this->_customerEmail = $customerEmail;
    $this->_customerName = $customerName;
    $this->_storeCnpjCpf = $storeCnpjCpf;
    $this->_storeName = $storeName;
    $this->_storePosNames = $storePosNames;
    $this->_userEmail = $userEmail;
    $this->_userFullName = $userFullName;
    $this->_termsAccepted = $termsAccepted;
    $this->_storePostalCode = $storePostalCode;
  }

  /**
   * Function to get request
   * @return string
   */
  public function getRequest(): string {
    $request = [
      self::CUSTOMER_EMAIL => $this->getCustomerEmail(),
      self::CUSTOMER_NAME => $this->getCustomerName(),
      self::STORE_CNPJ_CPF => $this->getStoreCnpjCpf(),
      self::STORE_NAME => $this->getStoreName(),
      self::STORE_POS_NAMES => $this->getStorePosNames(),
      self::STORE_POSTAL_CODE => $this->getStorePostalCode(),
      self::TERMS_ACCEPTED => $this->getTermsAccepted(),
      self::USER_EMAIL => $this->getUserEmail(),
      self::USER_FULL_NAME => $this->getUserFullName()
    ];

    return json_encode($request);
  }

  public function getCustomerEmail() {
    return $this->_customerEmail;
  }

  public function getCustomerName() {
    return $this->_customerName;
  }

  public function getStoreCnpjCpf() {
    return $this->_storeCnpjCpf;
  }

  public function getStoreName() {
    return $this->_storeName;
  }

  public function getStorePosNames() {
    return $this->_storePosNames;
  }

  public function getUserEmail() {
    return $this->_userEmail;
  }

  public function getUserFullName() {
    return $this->_userFullName;
  }

  public function getTermsAccepted() {
    return $this->_termsAccepted;
  }

  public function getStorePostalCode() {
    return $this->_storePostalCode;
  }
}
