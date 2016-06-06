var BASEURL = $('meta[name=_base_url]').attr('content');
var CSRFTOKEN = $('meta[name=_token]').attr('content');

(function () {
    var btn_upload = $('#btn_upload');
    var btn_upload_disable = function() {
        if (!btn_upload.prop('disabled')) {
            $('input[name="qqfile"]').addClass('disabled').attr('disabled', 'disabled');
            btn_upload.addClass('disabled').attr('disabled', 'disabled').before('<i class="glyphicon glyphicon-refresh glyphicon-spin save-spinner"></i> ');
        }
    };
    var btn_upload_enable = function() {
        if (btn_upload.prop('disabled')) {
            $('.save-spinner').remove();
            $('input[name="qqfile"]').removeClass('disabled').removeAttribute('disabled');
            btn_upload.removeClass('disabled').removeAttribute('disabled');
        }
    };

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
            onError: function (id, name, errorReason) {
                var fileEl = fuploader.fineUploader("getItemByFileId", id);
                fileEl.find('.upload-error')
                    .removeClass('hidden')
                    .find('.error-msg').text('Error: ' + errorReason);
                btn_upload_enable();
            },

            onUpload: function (id, name) {
                btn_upload_disable();
            },

            onComplete: function (id, name, response) {
                if (response.success) {
                    var image_id = response.imageId;
                    var fileEl = fuploader.fineUploader("getItemByFileId", id),
                        imageEl = fileEl.find(".uploaded-image");

                    imageEl.html('<input name="images[]" type="hidden" value="' + image_id + '">');
                    fuploader.fineUploader("setUuid", id, image_id);
                }
            },

            onAllComplete: function (s, f) {
                if (s.length > 0) {
                    $('#form_upload').submit();
                }
            }
        }
    });

    btn_upload.click(function (e) {
        fuploader.fineUploader('uploadStoredFiles');
        e.preventDefault();
    });
})();