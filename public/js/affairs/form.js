var frmSave = $('form#frmSave'),
    frmValidator;
(function($){
    frmValidator = frmSave
        .find('.input-daterange')
            .datepicker({
                format: "dd/mm/yyyy",
                language: "th-th",
                orientation: "bottom auto",
                thaiyear: true,
            })
            .end()
        .find('textarea.summernote')
            .each(function(a,b){
                $(b).summernote({
                    callbacks: {
                        onChange: function (contents, $editable) { 
                            $(b).val($(b).summernote('isEmpty') ? "" : contents);
                            frmValidator.element($(b));
                        }
                    }
                })
            })
            .end()
        .validate({
            ignore: ':hidden:not(.summernote),.note-editable.card-block',
            rules:{
                files:{
                    extension: 'jpg|png|jpeg|gif'
                }
            },
            errorPlacement: function(error, element){
                if (element.hasClass("summernote")) {
                    error.insertAfter(element.siblings(".note-editor"));
                } else {
                    error.insertAfter(element);
                }
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