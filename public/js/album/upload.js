(function ($) {
    // DropzoneJS Demo Code Start
    var image_temp = $.validator.format($.trim($('textarea#container-image-template').val()))
    var script = document.getElementById('jsUpload')
    Dropzone.autoDiscover = false

    // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
    var previewNode = document.querySelector("#template")
    previewNode.id = ""
    var previewTemplate = previewNode.parentNode.innerHTML
    previewNode.parentNode.removeChild(previewNode)

    var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
        url: script.getAttribute('data-upload-url'), // Set the url
        thumbnailWidth: 80,
        thumbnailHeight: 80,
        parallelUploads: 20,
        previewTemplate: previewTemplate,
        autoQueue: false, // Make sure the files aren't queued until manually added
        previewsContainer: "#previews", // Define the container to display the previews
        clickable: ".fileinput-button", // Define the element that should be used as click trigger to select files.
        acceptedFiles : "image/*", // allow file type image
        uploadprogress: function(file, progress, bytesSent) {
            if (file.previewElement) {
                var progressElement = file.previewElement.querySelector("[data-dz-uploadprogress]");
                progressElement.style.width = progress + "%";
                progressElement.querySelector(".progress-text").textContent = progress + "%";
            }
        },
        init: function () {
            this.on("sending", function (file, xhr, formData) {
                formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
            });
           
            this.on("success", function(file, responseText) { // #1
                var element = file
                if (responseText) {
                    element.previewElement.remove()
                    const {status,file} = responseText
                    if (status) {
                        // append file to panel success
                        console.log('success');
                        uploadSuccess(file)
                    }
                }
           });
        }
    })

    myDropzone.on("addedfile", function (file) {
        // Hookup the start button
        file.previewElement.querySelector(".start").onclick = function () { myDropzone.enqueueFile(file) }

    })

    // Update the total progress bar
    myDropzone.on("totaluploadprogress", function (progress) {})

    myDropzone.on("sending", function (file) {
        // And disable the start buttonv
        var btnStart = $(file.previewElement.querySelector(".start"))
        btnStart.button('loading')
        btnStart.prop("disabled", "disabled")
        file.previewElement.querySelector(".delete").setAttribute("disabled", "disabled")
    })

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone.on("queuecomplete", function (progress) { // #3
        console.log('queuecomplete');
        document.querySelector("#total-progress").style.opacity = "0"
    })

    // Setup the buttons for all transfers
    // The "add files" button doesn't need to be setup because the config
    // `clickable` has already been specified.
    document.querySelector("#actions .start").onclick = function () {
        myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
    }
    document.querySelector("#actions .cancel").onclick = function () {
        myDropzone.removeAllFiles(true)
    }

    $(document).on('click','#panel-images a.delete',async function(e){
        var container = $(this).parent().parent()
        e.preventDefault()
        const rs = await confirmAlert.fire({
            text: '\u0e04\u0e38\u0e13\u0e15\u0e49\u0e2d\u0e07\u0e01\u0e32\u0e23\u0e25\u0e1a\u0e23\u0e39\u0e1b\u0e20\u0e32\u0e1e \u0e43\u0e0a\u0e48\u0e2b\u0e23\u0e37\u0e2d\u0e44\u0e21\u0e48 ?',
            icon: 'warning',
        })
        if(rs) {
            if (rs.isConfirmed) {
                $.ajax({
                    url : $(this).data('url'),
                    type: 'POST',
                    beforeSend: function() { $('#tab-overlay').show() },
                    success: function(json) {
                        $('#tab-overlay').hide()
                        if (json.status) {
                            container.remove()
                        }
                    },
                    error: function(xhr) {
                        $('#tab-overlay').hide()
                        console.log(xhr);
                    }
                })
            }
        }
    })

    $(document).on('change','#panel-images input[name="cover_page"]',function(){
        $.post($(this).data('url'),function(){},'json')
    })

    function uploadSuccess(file) {
        $(image_temp(file.url,file.action_del,file.gallery_id)).appendTo($('#panel-images'))
    }
})(window.jQuery)