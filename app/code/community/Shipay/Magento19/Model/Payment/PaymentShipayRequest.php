<?php

class Shipay_Magento19_Model_Payment_PaymentShipayRequest {

  const BUYER = 'buyer';
  const CPF_CNPJ = 'cpf_cnpj';
  const EMAIL = 'email';
  const NAME = 'name';
  const PHONE = 'phone';
  const CALLBACK_URL = 'callback_url';
  const ITEMS = 'items';
  const ITEM_TITLE = 'item_title';
  const QUANTITY = 'quantity';
  const SKU = 'sku';
  const UNIT_PRICE = 'unit_price';
  const ORDER_REF = 'order_ref';
  const PIX_DICT_KEY = 'pix_dict_key';
  const TOTAL = 'total';
  const WALLET = 'wallet';
  const EXPIRATION = 'expiration';

  /** @var string */
  protected $_documentCpfCnpj;

  /** @var string */
  protected $_email;

  /** @var string */
  protected $_name;

  /** @var string */
  protected $_phone;

  /** @var string */
  protected $_callbackUrl;

  /** @var string */
  protected $_orderRef;

  /** @var string */
  protected $_pixDictKey;

  /** @var string */
  protected $_total;

  /** @var string */
  protected $_wallet;

  /** @var string */
  protected $_expiration;

  /** @var CartRequest */
  protected $_cart;

  /**
   * @param $documentCpfCnpj
   * @param $email
   * @param $name
   * @param $phone
   * @param $callbackUrl
   * @param $orderRef
   * @param $total
   * @param $wallet
   * @param $cart
   * @param $pixDictKey
   * @param $expiration
   */
  public function __construct(
    $documentCpfCnpj,
    $email,
    $name,
    $phone,
    $callbackUrl,
    $orderRef,
    $total,
    $wallet,
    $cart,
    $expiration = '0',
    $pixDictKey = '0'
  ) {
    $this->_documentCpfCnpj = $documentCpfCnpj;
    $this->_email = $email;
    $this->_name = $name;
    $this->_phone = $phone;
    $this->_callbackUrl = $callbackUrl;
    $this->_orderRef = $orderRef;
    $this->_total = $total;
    $this->_wallet = $wallet;
    $this->_cart = $cart;
    $this->_expiration = $expiration;
    $this->_pixDictKey = $pixDictKey;
  }

  /**
   * Function to get Request
   * @return string $request
   */
  public function getRequest() {
    //cart
    foreach ($this->getCart() as $modelCart) {
      $cart[] = [
        self::ITEM_TITLE => $modelCart->getItemTitle(),
        self::QUANTITY => $modelCart->getQuantity(),
        self::SKU => $modelCart->getSku(),
        self::UNIT_PRICE => $modelCart->getUnitPrice()
      ];
    }

    if ($this->getWallet() == 'pix') {
      if (intval($this->getExpiration()) > 3600) {
        $request = $this->getRequestPixWithExpirationTime($cart);
      } else {
        $request = $this->getRequestPixWithoutExpirationTime($cart);    
      }
    } else {
      if ($this->getPixDictKey() != '') {
        $request = $this->getRequestWithPixDictKey($cart);
      } else {
        $request = $this->getRequestWithoutPixDictKey($cart);
      } 
    }

    return json_encode($request);
  }

  /**
   * Function to create request PIX with Expiration Time
   * @param $cart
   * @return array
   */
  public function getRequestPixWithExpirationTime($cart) {
    $request = [
      self::BUYER => [
        self::CPF_CNPJ => $this->getDocumentCpfCnpj(),
        self::EMAIL => $this->getEmail(),
        self::NAME => $this->getName(),
        self::PHONE => $this->getPhone()
      ],
      self::CALLBACK_URL => $this->getCallbackUrl(),
      self::EXPIRATION => $this->getExpiration(),
      self::ITEMS => $cart,
      self::ORDER_REF => $this->getOrderRef(),
      self::TOTAL => $this->getTotal(),
      self::WALLET => $this->getWallet()
    ];

    return $request;
  }

  /**
   * Function to create request PIX without Expiration Time
   * @param $cart
   * @return array
   */
  public function getRequestPixWithoutExpirationTime($cart) {
    $request = [
      self::BUYER => [
        self::CPF_CNPJ => $this->getDocumentCpfCnpj(),
        self::EMAIL => $this->getEmail(),
        self::NAME => $this->getName(),
        self::PHONE => $this->getPhone()
      ],
      self::CALLBACK_URL => $this->getCallbackUrl(),
      self::ITEMS => $cart,
      self::ORDER_REF => $this->getOrderRef(),
      self::TOTAL => $this->getTotal(),
      self::WALLET => $this->getWallet()
    ];

    return $request;
  }

  /**
   * Function to create request with pix dict key
   * @param $cart
   * @return array
   */
  public function getRequestWithPixDictKey($cart) {
    $request = [
      self::BUYER => [
        self::CPF_CNPJ => $this->getDocumentCpfCnpj(),
        self::EMAIL => $this->getEmail(),
        self::NAME => $this->getName(),
        self::PHONE => $this->getPhone()
      ],
      self::CALLBACK_URL => $this->getCallbackUrl(),
      self::ITEMS => $cart,
      self::ORDER_REF => $this->getOrderRef(),
      self::TOTAL => $this->getTotal(),
      self::WALLET => $this->getWallet()
    ];

    return $request;
  }

  /**
   * Function to create request without pix dict key
   * @param $cart
   * @return array
   */
  public function getRequestWithoutPixDictKey($cart) {
    $request = [
      self::BUYER => [
        self::CPF_CNPJ => $this->getDocumentCpfCnpj(),
        self::EMAIL => $this->getEmail(),
        self::NAME => $this->getName(),
        self::PHONE => $this->getPhone()
      ],
      self::CALLBACK_URL => $this->getCallbackUrl(),
      self::ITEMS => $cart,
      self::ORDER_REF => $this->getOrderRef(),
      self::TOTAL => $this->getTotal(),
      self::WALLET => $this->getWallet()
    ];

    return $request;
  }

  public function getDocumentCpfCnpj() {
    return $this->_documentCpfCnpj;
  }

  public function getEmail() {
    return $this->_email;
  }

  public function getName() {
    return $this->_name;
  }

  public function getPhone() {
    return $this->_phone;
  }

  public function getCallbackUrl() {
    return $this->_callbackUrl;
  }

  public function getOrderRef() {
    return $this->_orderRef;
  }

  public function getPixDictKey() {
    return $this->_pixDictKey;
  }

  public function getTotal() {
    return $this->_total;
  }

  public function getWallet() {
    return $this->_wallet;
  }

  public function getCart() {
    return $this->_cart;
  }

  public function getExpiration() {
    return $this->_expiration;
  }
}
