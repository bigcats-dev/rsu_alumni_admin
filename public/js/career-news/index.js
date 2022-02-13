var frmApprove = $('#frmApprove'),
    frmDisApprove = $('#frmDisApprove'),
    settingmail_template = $.validator.format($.trim($("textarea#mail-setting-template").val())),
    datatable;
(function ($) {
    // check data in session storage
    if (sessionStorage.hasOwnProperty('tab_career_news')
        && sessionStorage.tab_career_news != 'undefined'
        && $(`ul#tab-status li a[data-type="${sessionStorage.tab_career_news}"]`).length > 0) {
            $(`ul#tab-status li a[data-type="${sessionStorage.tab_career_news}"]`).tab('show')
    } else {
        // first tab
        $("ul#tab-status li:first-child a").tab('show')
    }
    // save active tab to session storage
    sessionStorage.setItem('tab_career_news',$("ul#tab-status li a.active").attr('data-type'))

    // add eventlistener trigger tab change
    // and reload datatable
    $('ul#tab-status li a[data-toggle="tab"]').on('shown.bs.tab', function (event) {
        // save active tab to session storage
        sessionStorage.setItem('tab_career_news',$(event.target).attr('data-type'))
        event.target // newly activated tab
        event.relatedTarget // previous active tab
        if (datatable) datatable.ajax.reload()
    })

    $('#modalAprove').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var modal = $(this)
        var form = modal.find('form#frmApprove')
        form.attr('action', form.attr('action').replace(':id', button.data('id')))
        form.find('[name="rad"][value="1"]').prop('checked', true).trigger('change')
        cloneSetting()
    })

    $('#modalAprove').on('hidden.bs.modal', function (event) {
        var modal = $(this)
        var form = modal.find('form#frmApprove')
        form.find('.setting-box').remove()
    })

    $('#modalDisApprove').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var modal = $(this)
        var form = modal.find('form#frmDisApprove')
        form.attr('action', form.attr('action').replace(':id', button.data('id')))
    })

    frmApprove
        .find('[name="rad"]')
        .change(function () {
            if ($('[name="rad"]:checked', frmApprove).val() == '3') {
                $('.form-email', frmApprove).show()
            } else {
                $('.form-email', frmApprove).hide()
            }
        })
        .end()
        .find('button#btn-add')
        .click(function () {
            cloneSetting()
        })
        .end()
        .validate({
            errorPlacement: function(error,element) {
                if (element.hasClass('selectpicker')) {
                    error.appendTo(element.parent().parent())
                } else {
                    error.insertAfter(element)
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

    frmDisApprove.validate({
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

    datatable = $('table#tb-lists').DataTable(configTable)
})(window.jQuery)

function cloneSetting() {
    const i = $('.setting-box:last', frmApprove).data('index')
    const k = typeof i === 'undefined' ? 0 : i + 1
    $(settingmail_template(k, k + 1))
        .find('.selectpicker').selectpicker('refresh').end()
        .insertBefore($('div.btn-add', frmApprove))
}