<?php

class Shipay_Magento19_Validation_CustomerValidator {

  /**
   * Function to validate number of cellphone
   * @param string $numberPhone
   * @return bool
   */
  public function validateNumberPhone($numberPhone) {
    if (strlen($numberPhone) < 9) {
      return false;
    } else {
      return true;
    }
  }

  /**
   * Function to validate email
   * @param string $email
   * @return bool
   */
  public function validateEmail($email) {
    $isValid = strstr($email, '@');
    (strlen($email) < 5) ? $isValid = false : $isValid = true;
    return $isValid;
  }

  /**
   * Function to validate name
   * @param string $name
   * @return bool
   */
  public function validateName($name) {
    (strlen($name) < 3) ? $isValid = false : $isValid = true;
    ($name == "" || $name == null) ? $isValid = false : $isValid = true;
    return $isValid;
  }
}
