(function($){
    var content
    const pdf_template = $.validator.format($.trim($('textarea#files-display-template').val()))
    $('input.customFilePdf').change(function(e){
        content = $(this).parent().parent()
        $('.file-preview',content).empty()
        if (!e.target.files[0]) {
            $('.file-preview',content).hide()
            return
        }
        previewFiles(e.target.files)
    })

    function previewFiles(files) {
        if (files) {
            $('.file-preview',content).show();
            [].forEach.call(files, readAndPreview);
        }
    
        function readAndPreview(file) {
            $('[type="file"]',content).valid()
            if (!/\.(pdf)$/i.test(file.name)) {
                $('.file-preview',content).hide()
                return
            }

            $(pdf_template(file.name,niceBytes(file.size))).appendTo($('.file-preview',content)) 
        }
    
    }
})(window.jQuery)