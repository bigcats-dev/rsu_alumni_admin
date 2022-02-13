var frmSave = $('form#frmSave'),
    coordinator_temp = $.validator.format($.trim($('textarea#coordinator-template').val())),
    frmValidator;
(function($){
    frmValidator = frmSave
        .find('button#btn-add-coordinator')
        .click(function(){
            const container = $('div#coordinators-panel')
            if($('div.row',container).length === 0) $(coordinator_temp(0)).appendTo(container)
            else $(coordinator_temp(($('div.row:last',container).data('index') || 0) + 1)).insertAfter($('div.row:last',container))
        })
        .end()
        .validate({
            rules:{
                'files_1[]':{
                    extension: 'pdf'
                },
                'files_2[]':{
                    extension: 'pdf'
                },
                'files_3[]':{
                    extension: 'pdf'
                },
                'files_4[]':{
                    extension: 'pdf'
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
                            $('#tab-overlay').show()
                            $(form).find('[type="submit"]').button('loading')
                            form.submit()
                        }
                    })
            }
        })
})(window.jQuery)