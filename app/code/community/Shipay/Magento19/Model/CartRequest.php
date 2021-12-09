<?php

class Shipay_Magento19_Model_CartRequest {

  const ITEM_TITLE = 'item_title';
  const QUANTITY = 'quantity';
  const SKU = 'sku';
  const UNIT_PRICE = 'unit_price';

  /** @var string */
  protected $_itemTitle;

  /** @var string */
  protected $_quantity;

  /** @var string */
  protected $_sku;

  /** @var string */
  protected $_unitPrice;

  /**
   * @param $itemTitle
   * @param $quantity
   * @param $sku
   * @param $unitPrice
   */
  public function __construct(
    $itemTitle,
    $quantity,
    $sku,
    $unitPrice
  ) {
    $this->_itemTitle = $itemTitle;
    $this->_quantity = $quantity;
    $this->_sku = $sku;
    $this->_unitPrice = $unitPrice;
  }

  public function getItemTitle() {
    return $this->_itemTitle;
  }

  public function getQuantity() {
    return $this->_quantity;
  }

  public function getSku() {
    return $this->_sku;
  }

  public function getUnitPrice() {
    return $this->_unitPrice;
  }
}
