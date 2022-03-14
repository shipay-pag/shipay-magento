let arrayWallets;

setInterval(function () {
    var baseUrl = document.getElementById("shipay-base-url").value ? document.getElementById("shipay-base-url").value : window.origin;
    var orderId = document.getElementById('orderid').value;

    if (orderId) {
        let url = `${baseUrl}/shipaymagento19/query?orderid=${orderId}`;

        fetch(url)
            .then(response => response.json())
            .then((data) => {
                if (data == 'yes') {
                    try {
                        document.getElementById('qrcode-text').style.display = "none";
                        document.getElementById('button-copy').style.display = "none";
                    } catch (error) {
                    }
                    document.getElementById('qrcode-base64').style.display = "none";
                    try {
                        document.getElementById('button-deeplink').style.display = "none";
                    } catch (error) {
                    }
                    document.getElementById("h2-text").style.display = "";
                }
            });
    }

}, 60000);

function copyTextSucess() {
    navigator.clipboard.writeText(document.getElementById('qrcode-text').value).then(function () {
    }, function () {
    });
}

function getWallets() {
    var baseUrl = document.getElementById("shipay-base-url").value ? document.getElementById("shipay-base-url").value : window.origin;

    fetch(`${baseUrl}/shipaymagento19/wallets`)
        .then(response => response.json())
        .then((data) => {
            this.arrayWallets = data;
        });
}

function typeMethodSelected(imageElement) {
    this.arrayWallets.forEach(wallet => {
        if (wallet.active == true) {
            if (wallet.wallet == imageElement.id) {
                imageElement.style.opacity = 1;
                document.getElementById('wallet-name').value = wallet.wallet;
                document.getElementById('pix-dict-key').value = wallet.pix_dict_key;
            } else {
                document.getElementById(wallet.wallet).style.opacity = 0.30;
            }
        }
    });
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

Validation.addAllThese([
    ['validate-document', 'Documento inválido. Verifique por favor.', function (v) {
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

    ['validate-method-payment', 'Por favor, selecione uma forma de pagamento.', function (v) {
        let selected = document.getElementById('wallet-name');
        if (selected.value == undefined || selected.value == "") {
            return false;
        } else {
            return true;
        }
    }]
]);