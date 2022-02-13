var frmDisApprove = $('#frmDisApprove'),
    datatable;
(function ($) {
    $(document).ready(function () {
        // check data in session storage
        if (sessionStorage.hasOwnProperty('tab_alumni_glory')
            && sessionStorage.tab_alumni_glory != 'undefined'
            && $(`ul#tab-status li a[data-type="${sessionStorage.tab_alumni_glory}"]`).length > 0) {
            $(`ul#tab-status li a[data-type="${sessionStorage.tab_alumni_glory}"]`).tab('show')
        } else {
            // first tab
            $("ul#tab-status li:first-child a").tab('show')
        }
        // save active tab to session storage
        sessionStorage.setItem('tab_alumni_glory', $("ul#tab-status li a.active").attr('data-type'))

        // add eventlistener trigger tab change
        // and reload datatable
        $('ul#tab-status li a[data-toggle="tab"]').on('shown.bs.tab', function (event) {
            // save active tab to session storage
            sessionStorage.setItem('tab_alumni_glory', $(event.target).attr('data-type'))
            event.target // newly activated tab
            event.relatedTarget // previous active tab
            if (datatable) datatable.ajax.reload()
        })

        $('#modalDisApprove').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var modal = $(this)
            var form = modal.find('form#frmDisApprove')
            form.attr('action', form.attr('action').replace(':id', button.data('id')))
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
    })
})(window.jQuery)