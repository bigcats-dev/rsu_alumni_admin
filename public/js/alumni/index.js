var datatable;
(function ($) {
    $(document).ready(function () {
        frmSearch
            .find('[name="faculty"]')
                .change(function(){
                    $('option:not(:first)',$('[name="major"]',frmSearch)).remove()
                    var datas = []
                    if ($(this).val() != '')
                        datas = majors.filter((i) => i.faculty_code == $(this).val())
                    else
                        datas = [...majors]
                    datas.forEach((i) => $('[name="major"]',frmSearch).append(`<option value="${i.major_code}">${i.major_name_th}</option>`))
                    $('[name="major"]',frmSearch).selectpicker('refresh')
                })
                .end()
        datatable = $('table#tb-lists').DataTable(configTable)
    })
})(window.jQuery)