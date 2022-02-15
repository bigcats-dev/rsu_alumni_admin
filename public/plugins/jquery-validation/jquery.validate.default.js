// set default
$.validator.setDefaults({
    // errorContainer: ".container-error",
    // errorLabelContainer: ".container-error ul",
    // wrapper: "li",
    errorClass: 'error',
    validClass: 'success',
    highlight: function (ele, errorClass, validClass) {
        // highlight text element
        // add class error
        $(ele).addClass(errorClass)//.removeClass(validClass)
    },
    unhighlight: function (ele, errorClass, validClass) {
        // remove class error
        $(ele).removeClass(errorClass)//.addClass(validClass)
    },
    errorPlacement: function (error, element) {
        // custom append element error
        error.insertAfter(element)
    },
    submitHandler: function (form, event, callback) {
        let self = this // Swal
        this.settings.beforeSubmit().then(
            function () { // resolve
                let optionsSwal = {
                    ...{
                        title: '\u0e41\u0e08\u0e49\u0e07\u0e40\u0e15\u0e37\u0e2d\u0e19',
                        text: '\u0e04\u0e38\u0e13\u0e15\u0e49\u0e2d\u0e07\u0e01\u0e32\u0e23\u0e1a\u0e31\u0e19\u0e17\u0e36\u0e01\u0e02\u0e49\u0e2d\u0e21\u0e39\u0e25 \u0e43\u0e0a\u0e48\u0e2b\u0e23\u0e37\u0e2d\u0e44\u0e21\u0e48 ?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: '\u0e1a\u0e31\u0e19\u0e17\u0e36\u0e01',
                        cancelButtonText: '\u0e22\u0e01\u0e40\u0e25\u0e34\u0e01',
                        showLoaderOnConfirm: true,
                        allowOutsideClick: () => !Swal.isLoading(),
                        preConfirm: () => {
                            return new Promise((resolve, reject) => {
                                const formData = new FormData(form)
                                $.ajax({
                                    type: $(form).attr('method'),
                                    url: $(form).attr('action'),
                                    data: formData,
                                    contentType: false,
                                    processData: false,
                                }).done(function (data, textStatus, jqXHR) {
                                    resolve(data)
                                }).fail(function (jqXHR, textStatus, errorThrown) {
                                    reject(handleError(jqXHR))
                                })

                            }).then((data) => {
                                if (!data.status) throw data.msg
                                return data
                            })
                                .catch((err) => {
                                    Swal.showValidationMessage(`\u0e40\u0e01\u0e34\u0e14\u0e02\u0e49\u0e2d\u0e1c\u0e34\u0e14\u0e1e\u0e25\u0e32\u0e14 ${err}`)
                                })
                        }
                    }, ...self.settings.swalOptions
                }
                Swal.fire(optionsSwal).then((result) => {
                    if (result.value) {
                        // show dialog
                        if (result.value.status) Swal.fire({ toast: true, position: 'top-end', showConfirmButton: false, icon: 'success', title: '\u0e1a\u0e31\u0e19\u0e17\u0e36\u0e01\u0e02\u0e49\u0e2d\u0e21\u0e39\u0e25\u0e2a\u0e33\u0e40\u0e23\u0e47\u0e08', timer: 2000 })
                        // callback function
                        callback(result.value)
                    }
                })
            },
            function (err) { // reject
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: err,
                    confirmButtonText: '\u0e15\u0e01\u0e25\u0e07'
                })
            }
        );

        return false;
    },
    submitCallback: function (json) { },
    beforeSubmit: function () { return Promise.resolve() },
    swalOptions: {},
    swalAlertSuccess: {},
})
// custom message
$.extend(
    $.validator.messages, {
    required: '\u0e08\u0e33\u0e40\u0e1b\u0e47\u0e19\u0e15\u0e49\u0e2d\u0e07\u0e01\u0e23\u0e2d\u0e01'
    , number: '\u0e01\u0e23\u0e2d\u0e01\u0e02\u0e49\u0e2d\u0e21\u0e39\u0e25\u0e17\u0e35\u0e48\u0e40\u0e1b\u0e47\u0e19\u0e15\u0e31\u0e27\u0e40\u0e25\u0e02'
    , digits: '\u0e01\u0e23\u0e2d\u0e01\u0e02\u0e49\u0e2d\u0e21\u0e39\u0e25\u0e17\u0e35\u0e48\u0e40\u0e1b\u0e47\u0e19\u0e15\u0e31\u0e27\u0e40\u0e25\u0e02'
    , integer: '\u0e01\u0e23\u0e2d\u0e01\u0e02\u0e49\u0e2d\u0e21\u0e39\u0e25\u0e17\u0e35\u0e48\u0e40\u0e1b\u0e47\u0e19\u0e15\u0e31\u0e27\u0e40\u0e25\u0e02'
    , minlength: $.validator.format('\u0e42\u0e1b\u0e23\u0e14\u0e1b\u0e49\u0e2d\u0e19\u0e2d\u0e22\u0e48\u0e32\u0e07\u0e19\u0e49\u0e2d\u0e22 {0} \u0e2d\u0e31\u0e01\u0e02\u0e23\u0e30')
    , maxlength: $.validator.format('\u0e08\u0e33\u0e19\u0e27\u0e19\u0e2d\u0e31\u0e01\u0e02\u0e23\u0e30\u0e15\u0e49\u0e2d\u0e07\u0e19\u0e49\u0e2d\u0e22\u0e01\u0e27\u0e48\u0e32\u0e2b\u0e23\u0e37\u0e2d\u0e40\u0e17\u0e48\u0e32\u0e01\u0e31\u0e1a {0}')
    , max: $.validator.format('\u0e01\u0e23\u0e2d\u0e01\u0e04\u0e48\u0e32\u0e17\u0e35\u0e48\u0e19\u0e49\u0e2d\u0e22\u0e01\u0e27\u0e48\u0e32\u0e2b\u0e23\u0e37\u0e2d\u0e40\u0e17\u0e48\u0e32\u0e01\u0e31\u0e1a {0}')
    , min: $.validator.format('\u0e01\u0e23\u0e2d\u0e01\u0e04\u0e48\u0e32\u0e17\u0e35\u0e48\u0e21\u0e32\u0e01\u0e01\u0e27\u0e48\u0e32\u0e2b\u0e23\u0e37\u0e2d\u0e40\u0e17\u0e48\u0e32\u0e01\u0e31\u0e1a {0}')
    , require_from_group: $.validator.format('\u0e01\u0e23\u0e2d\u0e01\u0e02\u0e49\u0e2d\u0e21\u0e39\u0e25\u0e2d\u0e22\u0e48\u0e32\u0e07\u0e19\u0e49\u0e2d\u0e22 {0} \u0e0a\u0e48\u0e2d\u0e07')
    , extension: $.validator.format('\u0e01\u0e23\u0e38\u0e13\u0e32\u0e40\u0e25\u0e37\u0e2d\u0e01\u0e44\u0e1f\u0e25\u0e4c\u0e19\u0e32\u0e21\u0e2a\u0e01\u0e38\u0e25 (\u0e40\u0e09\u0e1e\u0e32\u0e30 {0})')
})
// custom methods
$.validator.addMethod(
    'sum',
    function (value, element, params) {
        let sumOfVals = 0;
        const $parent = $(element).parents('.group-sum').eq(0)
        $parent.find('input').each(function () {
            sumOfVals = sumOfVals + parseFloat($(this).val());
        });
        if (sumOfVals == params) return true;
        return false;
    },
    $.validator.format("\u0e01\u0e23\u0e38\u0e13\u0e32\u0e01\u0e23\u0e2d\u0e01\u0e02\u0e49\u0e2d\u0e21\u0e39\u0e25\u0e23\u0e27\u0e21\u0e01\u0e31\u0e19\u0e43\u0e2b\u0e49\u0e40\u0e17\u0e48\u0e32\u0e01\u0e31\u0e1a {0}")
);

$.validator.addMethod("pwcheck", function (value) {
    return /^[A-Za-z0-9\d=!\-@._*]*$/.test(value) // consists of only these
        && /[a-z]/.test(value) // has a lowercase letter
        && /\d/.test(value) // has a digit
});

$.validator.addMethod(
    "regex",
    function (value, element, regexp) {
        if (regexp.constructor != RegExp)
            regexp = new RegExp(regexp);
        else if (regexp.global)
            regexp.lastIndex = 0;
        return this.optional(element) || regexp.test(value);
    },
    "error expression reguliere"
);

$.validator.addMethod(
    'idcard',
    function (value, element) {

        if (value.length != 13) return false;
        for (i = 0, sum = 0; i < 12; i++)
            sum += parseFloat(value.charAt(i)) * (13 - i); if ((11 - sum % 11) % 10 != parseFloat(value.charAt(12)))
            return false; return true;
    }
    , 'personal id card incorrect'
)

$.validator.methods.number = function (value, element) {
    element.value = element.value.replace(",", "")
    return this.optional(element) || /^-?(?:\d+|\d{1,3}(?:[\s\.]\d{3})+)(?:[\.]\d+)?$/.test(value);
}

$.validator.addMethod('minheight', function (value, element, param) {
    if ($(element).attr('height')) {
        return $(element).attr('height') >= parseInt(param || '0');
    } return this.optional(element) || true;
}, 'A altura deve ser exatamente {0}px');

$.validator.addMethod('minwidth', function (value, element, param) {
    if ($(element).attr('width')) {
        return $(element).attr('width') >= parseInt(param || '0');
    } return this.optional(element) || true;
}, 'A largura deve ser exatamente {0}px');