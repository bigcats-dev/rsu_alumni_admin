(function($){
    const image_template = $.validator.format($.trim($('textarea#image-display-template').val()))
    const content = $('#img-update-template')
    const inputFile = $('input#customFileImg')
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
            var image  = new Image();
            reader.addEventListener("load", function () {
                image.src = this.result;
                image.onload = function(){
                    inputFile.attr('height',this.height)
                    inputFile.attr('width',this.width)
                    $(image_template(this.src,file.name,niceBytes(file.size),this.width,this.height)).appendTo($('.image-preview'))
                }
                 
            });
            reader.readAsDataURL(file);
        }
    
    }
})(window.jQuery)