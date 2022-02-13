var frmSave = $('form#frmSave'),
    frmValidator;
(function($){
    frmValidator = frmSave
        .validate({
            rules:{
                images:{
                    extension: 'jpg|png|jpeg|gif'
                }
            },
            errorPlacement: function(error, element){
                error.insertAfter(element);
            },
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