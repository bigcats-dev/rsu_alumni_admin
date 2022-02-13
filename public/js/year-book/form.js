var frmSave = $('form#frmSave'),
    frmValidator,
    jsFrm = $('script#jsFrmYearBook'),
    ms_major = jsFrm.data('major');
(function($){
    frmValidator = frmSave
        .find('.selectpicker')
            .selectpicker()
            .end()
        .find('[name="faculty_id"]')
            .change(function(){
                var major_id = $('[name="major_id"]',frmSave).val()
                $('[name="major_id"]',frmSave).find('option').not(':first').remove()
                var datas = ms_major.filter((i) => i.faculty_id == $(this).val())
                for (let index = 0; index < datas.length; index++) {
                    const element = datas[index];
                    $('[name="major_id"]',frmSave).append(`<option value="${element.major_id}" ${element.major_id == major_id ? 'selected' : ''}>(${element.major_code}) ${element.major_name_th}</option>`)
                }
                $('[name="major_id"]',frmSave).selectpicker('refresh');
            })
            .trigger('change')
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
                    extension: 'pdf'
                },
                images:{
                    extension: 'jpg|png|jpeg|gif'
                }
            },
            errorPlacement: function(error, element){
                if (element.hasClass("summernote")) {
                    error.insertAfter(element.siblings(".note-editor"));
                } else if (element.hasClass('selectpicker')){
                    error.appendTo(element.parent().parent())
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