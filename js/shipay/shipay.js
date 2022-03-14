var terms = false;

function copyTextSucess() {
  navigator.clipboard.writeText(document.getElementById('qrcode-text').value).then(function () {
  }, function () {
  });
}

function termsAccepted() {
  (terms == false) ? terms = true : terms = false;
}

function maskDocument(document) {
  const i = document.value.length;
  if (i == 11) {
    document.value = maskCpf(document.value);
  }
  if (i == 15) {
    document.value = document.value.replace(/[\.-]/g, "");
  }
  if (i == 14) {
    document.value = maskCnpj(document.value);
  }
  if (i < 14 && i > 11) {
    document.value = document.value.replace(/[\.-]/g, "");
  }
}

function maskCpf(cpf) {
  cpf = cpf.replace(/\D/g, "")
  cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2")
  cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2")
  cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2")
  return cpf
}

function maskCnpj(cnpj) {
  cnpj = (cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, "$1.$2.$3/$4-$5"));
  return cnpj;
}

function maskPostalCode(cep) {
  let n = cep.value.length;
  if (n == 5) {
    cep.value += '-';
  }
}

function openTermsPopup() {
  var baseUrl = document.getElementById("shipay-base-url").value ? document.getElementById("shipay-base-url").value : window.origin;
  var dataHtml = '';

  fetch(`${baseUrl}/shipaymagento19/terms`)
    .then(response => response.text())
    .then((data) => {
      this.dataHtml = data;
    });

  var win = window.open("", "Termos e Condições", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=1000,height=800,top=center" + (screen.height - 400) + ",left=" + (screen.width - 840));
  setTimeout(() => {
    win.document.body.innerHTML = this.dataHtml;
  }, 2500);
}

Validation.addAllThese([
  ['validate-terms-accepted', 'É necessário ler e aceitar os termos para prosseguir', function (v) {
    if (terms) {
      return true;
    } else {
      return false;
    }
  }],

  ['validate-email', 'Email inválido, verifique por favor', function (v) {
    const regexEmail = /\S+@\S+\.\S+/;
    let email = document.getElementById('user_email').value;
    return regexEmail.test(email);
  }],

  ['validate-name', 'Por favor insira o seu nome completo', function (v) {
    let name = document.getElementById('user_full_name').value;
    if (name.includes(' ')) {
      if (name.length > 6) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }],

  ['validate-pos-names', 'Por favor insira o nome do(s) caixa(s)', function (v) {
    if (document.getElementsByClassName('required-entry pos-names-class').length > 0) {
      return true;
    } else {
      return false;
    }
  }],

  ['validate-document', 'Documento inválido. Verifique por favor', function (v) {
    const tamDocument = v.length;
    if (tamDocument == 14) {
      v = v.replace(/\D/g, '');
      if (v.toString().length != 11 || /^(\d)\1{10}$/.test(v)) return false;
      var result = true;
      [9, 10].forEach(function (j) {
        var soma = 0, r;
        v.split(/(?=)/).splice(0, j).forEach(function (e, i) {
          soma += parseInt(e) * ((j + 2) - (i + 1));
        });
        r = soma % 11;
        r = (r < 2) ? 0 : 11 - r;
        if (r != v.substring(j, j + 1)) result = false;
      });
      return result;
    }
    else if (tamDocument == 18) {
      var cnpj = v.trim();

      cnpj = cnpj.replace(/\./g, '');
      cnpj = cnpj.replace('-', '');
      cnpj = cnpj.replace('/', '');
      cnpj = cnpj.split('');

      var v1 = 0;
      var v2 = 0;
      var aux = false;

      for (var i = 1; cnpj.length > i; i++) {
        if (cnpj[i - 1] != cnpj[i]) {
          aux = true;
        }
      }

      if (aux == false) {
        return false;
      }

      for (var i = 0, p1 = 5, p2 = 13; (cnpj.length - 2) > i; i++, p1--, p2--) {
        if (p1 >= 2) {
          v1 += cnpj[i] * p1;
        } else {
          v1 += cnpj[i] * p2;
        }
      }

      v1 = (v1 % 11);

      if (v1 < 2) {
        v1 = 0;
      } else {
        v1 = (11 - v1);
      }

      if (v1 != cnpj[12]) {
        return false;
      }

      for (var i = 0, p1 = 6, p2 = 14; (cnpj.length - 1) > i; i++, p1--, p2--) {
        if (p1 >= 2) {
          v2 += cnpj[i] * p1;
        } else {
          v2 += cnpj[i] * p2;
        }
      }

      v2 = (v2 % 11);

      if (v2 < 2) {
        v2 = 0;
      } else {
        v2 = (11 - v2);
      }

      if (v2 != cnpj[13]) {
        return false;
      } else {
        return true;
      }

    }
    else {
      return false;
    }
  }],

  ['validate-postal-code', 'Código postal inválido. Verifique por favor', function (v) {
    if (document.getElementById('store_postal_code').value.length != 9) {
      return false;
    } else {
      return true;
    }
  }],
]);
