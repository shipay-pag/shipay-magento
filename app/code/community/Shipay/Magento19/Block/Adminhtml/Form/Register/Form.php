<?php

class Shipay_Magento19_Block_Adminhtml_Form_Register_Form extends Mage_Adminhtml_Block_Widget_Form {

  /**
   * Function to render html
   * @return Mage_Adminhtml_Block_Widget_Form
   */
  protected function _prepareForm() {
    $form = new Varien_Data_Form(
      array(
        'id'     => 'edit_form',
        'action'  => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
        'method' => 'post',
        'enctype' => 'multipart/form-data'
      )
    );

    //fieldset for store infos
    $fieldsetStore = $form->addFieldset(
      'store_fieldset',
      [
        'legend' => Mage::helper('shipay_magento19')->__('Registro da Loja')
      ]
    );

    $fieldsetStore->addField(
      'customer_email',
      'text',
      [
        'label' => 'Email',
        'name' => 'customer_email',
        'class' => 'validate-email',
        'style' => 'width:300px; height:20px;',
        'required' => true
      ]
    );

    $fieldsetStore->addField(
      'customer_name',
      'text',
      [
        'label' => 'Nome',
        'name' => 'customer_name',
        'style' => 'width:300px; height:20px;',
        'required' => true
      ]
    );

    $fieldsetStore->addField(
      'store_cnpj_cpf',
      'text',
      [
        'label' => 'CNPJ/CPF da Loja',
        'name' => 'store_cnpj_cpf',
        'style' => 'width:300px; height:20px;',
        'class' => 'validate-document',
        'onkeyup' => 'maskDocument(this)',
        'required' => true
      ]
    );

    $fieldsetStore->addField(
      'store_name',
      'text',
      [
        'label' => 'Nome da Loja',
        'name' => 'store_name',
        'style' => 'width:300px; height:20px;',
        'required' => true
      ]
    );

    $fieldsetStore->addField(
      'store_postal_code',
      'text',
      [
        'label' => 'Código postal (CEP)',
        'name' => 'store_postal_code',
        'style' => 'width:300px; height:20px;',
        'onkeyup' => 'maskPostalCode(this)',
        'class' => 'validate-postal-code',
        'required' => true
      ]
    );

    /** caixas da loja aqui */
    $fieldsetPosNames = $form->addFieldset(
      'fieldset_store_pos_names',
      [
        'legend' => Mage::helper('shipay_magento19')->__('Lista de Caixas da Loja')
      ]
    );

    $storePosNames = $fieldsetPosNames->addField('store_pos_names', 'editor', array(
      'name'      => 'store_pos_names',
      'label'     => 'Caixas',
      'required' => true,
    ));

    $dynamicRow = $this->getLayout()
      ->createBlock('shipay_magento19/adminhtml_form_field_dynamicrow')
      ->setData(array(
        'name'      => 'store_pos_names',
        'label'     => 'Caixas'
      ));
    $storePosNames->setRenderer($dynamicRow);

    $fieldsetPosNames->addField(
      'validate_fill_pos_names',
      'text',
      [
        'name' => 'validate_fill_pos_names',
        'class' => 'validate-pos-names',
        'style' => 'display:none'
      ]
    );

    //fieldset for customer infos
    $fieldsetCustomer = $form->addFieldset(
      'customer_fieldset',
      [
        'legend' => Mage::helper('shipay_magento19')->__('Registro do Usuário')
      ]
    );

    $fieldsetCustomer->addField(
      'user_email',
      'text',
      [
        'label' => 'Email',
        'name' => 'user_email',
        'style' => 'width:300px; height:20px;',
        'class' => 'validate-email',
        'required' => true
      ]
    );

    $fieldsetCustomer->addField(
      'user_full_name',
      'text',
      [
        'label' => 'Nome Completo',
        'name' => 'user_full_name',
        'class' => 'validate-name',
        'style' => 'width:300px; height:20px;',
        'required' => true
      ]
    );

    //fieldset for terms and conditions
    $fieldsetTermsAndConditions = $form->addFieldset(
      'terms_fieldset',
      [
        'legend' => Mage::helper('shipay_magento19')->__('Termos e Condições')
      ]
    );

    $fieldsetTermsAndConditions->addField(
      'terms_button',
      'button',
      [
        'name' => 'terms_button',
        'label' => 'Termos e Condições',
        'value' => 'Termos e Condições',
        'class' => 'form-button',
        'onclick' => 'openTermsPopup()'
      ]
    );

    $fieldsetTermsAndConditions->addField(
      'terms_accepted',
      'checkbox',
      [
        'label' => 'Li e aceito os termos e condições da Shipay',
        'class' => 'validate-terms-accepted',
        'name' => 'terms_accepted',
        'onclick' => 'termsAccepted()'
      ]
    );

    $form->setUseContainer(true);
    $this->setForm($form);
    return parent::_prepareForm();
  }
}
