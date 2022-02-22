var datatable;
(function ($) {
    $(document).ready(function () {
        datatable = $('table#tb-lists').DataTable(configTable)
    })
})(window.jQuery)