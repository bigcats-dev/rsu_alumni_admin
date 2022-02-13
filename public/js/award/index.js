var frmSave = $('form#frmAward');
(function ($) {
    $.fn.extend({
        treed: function (o) {
            var openedClass = 'fa-chevron-down';
            var closedClass = 'fa-chevron-right';

            if (typeof o != 'undefined') {
                if (typeof o.openedClass != 'undefined') {
                    openedClass = o.openedClass;
                }
                if (typeof o.closedClass != 'undefined') {
                    closedClass = o.closedClass;
                }
            };

            //initialize each of the top levels
            var tree = $(this);
            tree.addClass("tree");
            tree.find('li').has("ul").each(function () {
                var branch = $(this); //li with children ul
                branch.prepend("<i class='indicator fas " + closedClass + "'></i>");
                branch.addClass('branch');
                branch.on('click', function (e) {
                    if (this == e.target) {
                        var icon = $(this).children('i:first');
                        icon.toggleClass(openedClass + " " + closedClass);
                        $(this).children().children().toggle();
                    }
                }).trigger('click')
                branch.children().children().toggle();
            });
            //fire event from the dynamically added icon
            tree.find('.branch .indicator').each(function () {
                $(this).on('click', function () {
                    $(this).closest('li').click();
                });
            });
            //fire event to open branch if the li contains an anchor instead of text
            tree.find('.branch > a').each(function () {
                $(this).on('click', function (e) {
                    $(this).closest('li').click();
                    e.preventDefault();
                });
            });
            //fire event to open branch if the li contains a button instead of text
            tree.find('.branch > button').each(function () {
                $(this).on('click', function (e) {
                    $(this).closest('li').click();
                    e.preventDefault();
                });
            });
        }
    });

    $('ul#tree_award').treed();

    $('#mdAward').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var modal = $(this)
        var form = modal.find('form')
        form.prop('action', button.data('action'))
        form.find('[name="award_sub_type_id"]').val(button.data('parent_id'))
        form.find('[name="award_type_name"]').val(button.data('name'))
    })

    $(document).on('click','ul#tree_award li i.fa-trash',async function(e){
        e.preventDefault()
        const rs = await confirmAlert.fire({
            text: 'คุณต้องการลบข้อมูล ใช่หรือไม่?',
            icon: 'warning',
        })
        if(rs) {
            if (rs.isConfirmed) {
                $('#tab-overlay').show()
                $(e.target).closest('li').find('form#frmDestroy_' + $(e.target).data('id'))[0].submit()
            }
        }
    })

    $(document).on('click','ul#tree_award li input[type="checkbox"]',async function(e){
        $.post($(e.target).data('action'),function(){},'json')
        var stated = $(e.target).is(':checked')
        $(e.target).closest('li').find('ul li input[type="checkbox"]').each(function(){
            if ($(this).is(':checked') != stated) {
                $(this).prop('checked',stated)
                $.post($(this).data('action'),function(){},'json')
            }
        })
    })

    frmSave.validate({
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