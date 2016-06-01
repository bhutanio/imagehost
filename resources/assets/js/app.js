var BASEURL = $('meta[name=_base_url]').attr('content');
var CSRFTOKEN = $('meta[name=_token]').attr('content');

(function () {
    var fuploader = $('#images_fileuploader').fineUploader({
        template: "upload-template",
        thumbnails: {
            placeholders: {
                waitingPath: BASEURL + "/images/fuwaiting-generic.png",
                notAvailablePath: BASEURL + "/images/funot_available-generic.png"
            }
        },
        request: {
            endpoint: BASEURL + '/image/upload',
            params: {_token: CSRFTOKEN}
        },
        editFilename: {
            enabled: false
        },
        retry: {
            enableAuto: false
        },
        chunking: {
            enabled: false
        },
        deleteFile: {
            enabled: true,
            method: "DELETE",
            endpoint: BASEURL + '/image/delete',
            params: {_token: CSRFTOKEN}
        },
        autoUpload: false,
        validation: {
            allowedExtensions: ['jpeg', 'jpg', 'gif', 'png'],
            sizeLimit: 20971520 // 20 Mb
        },
        callbacks: {
            onAllComplete: function (s, f) {
                if (s.length > 0) {
                    $('#form_upload').submit();
                }
            }
        }
    }).on('error', function (event, id, name, errorReason) {
        var fileEl = fuploader.fineUploader("getItemByFileId", id);
        fileEl.find('.upload-error')
            .removeClass('hidden')
            .find('.error-msg').text('Error: ' + errorReason);
    }).on('complete', function (event, id, name, response) {
        if (response.success) {
            var image_id = response.imageId;
            var fileEl = fuploader.fineUploader("getItemByFileId", id),
                imageEl = fileEl.find(".uploaded-image");

            imageEl.html('<input name="images[]" type="hidden" value="' + image_id + '">');
            fuploader.fineUploader("setUuid", id, image_id);
        }
    });

    $('#btn_upload').click(function (e) {
        fuploader.fineUploader('uploadStoredFiles');
        e.preventDefault();
    });
})();