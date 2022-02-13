var frmSave = $('form#frmSave'),
    frmValidator;
(function($){
    frmValidator = frmSave
        .validate({
            submitHandler: function (form) {
                confirmAlert.fire()
                    .then((result) => {
                        if (result.isConfirmed) {
                            $(form).find('[type="submit"]').button('loading')
                            form.submit()
                        }
                    })
            }
        })
})(window.jQuery)