(function($){
    const image_template = $.validator.format($.trim($('textarea#image-display-template').val()))
    const content = $('#img-update-template')
    $('input#customFileImg').change(function(e){
        $('.image-preview',content).empty()
        if (!e.target.files[0]) {
            $('.image-preview',content).hide()
            return
        }
        previewImages(e.target.files)
    })

    function previewImages(files) {
        if (files) {
            $('.image-preview',content).show();
            [].forEach.call(files, readAndPreview);
        }
    
        function readAndPreview(file) {
            $('[type="file"]',content).valid()
            if (!/\.(jpe?g|png|gif)$/i.test(file.name)) {
                return
            }
            var reader = new FileReader();
            reader.addEventListener("load", function () {
                $(image_template(this.result,file.name,niceBytes(file.size))).appendTo($('.image-preview'))
            });
            reader.readAsDataURL(file);
        }
    
    }
})(window.jQuery)