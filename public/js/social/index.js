(function($){
    $(document).on('click','button.delete',async function(e){
        e.preventDefault()
        const rs = await confirmAlert.fire({
            text: 'คุณต้องการลบข้อมูล ใช่หรือไม่?',
            icon: 'warning',
        })
        if(rs) {
            if (rs.isConfirmed) {
                $(this).closest('h5').find('form#frmDestroy_' + $(this).data('id'))[0].submit()
            }
        }
    })

    $(document).on('change','.social-box select.priority',function(e){
        $.post($('option:selected',$(this)).data('action-priority'),function(){},'json')
    })

    $(document).on('change','.social-box input[type="checkbox"].active',function(e){
        $.post($(this).data('action'),function(){},'json')
    })

})(window.jQuery)