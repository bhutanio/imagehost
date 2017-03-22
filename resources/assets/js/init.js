if (typeof jQuery === 'undefined') {
    throw new Error('Requires jQuery')
}

import swal from "sweetalert2";

const CSRFTOKEN = $('meta[name=_token]').attr('content');
const BASEURL = $('meta[name=_base_url]').attr('content');

+(function ($) {
    'use strict';

    $(window).on('load resize', function () {
        $('#content-area').css('min-height', $(window).height() - ($('header').height() + $('footer').height() + 80) + 'px');
    });

    // Tooltip
    $('[data-toggle="tooltip"]').tooltip({'container': 'body'});

    // Popover
    $('[data-toggle="popover"]').popover();

    let btn_upload = $('#btn_upload');
    let btn_upload_disable = function () {
        if (!btn_upload.prop('disabled')) {
            $('input[name="qqfile"]').addClass('disabled').attr('disabled', 'disabled');
            btn_upload.addClass('disabled').attr('disabled', 'disabled').before('<i class="glyphicon glyphicon-refresh glyphicon-spin save-spinner"></i> ');
        }
    };
    let btn_upload_enable = function () {
        if (btn_upload.prop('disabled')) {
            $('.save-spinner').remove();
            $('input[name="qqfile"]').removeClass('disabled').removeAttribute('disabled');
            btn_upload.removeClass('disabled').removeAttribute('disabled');
        }
    };

    let fuploader = $('#images_fileuploader').fineUploader({
        template: "upload-template",
        thumbnails: {
            placeholders: {
                waitingPath: BASEURL + "/images/waiting-generic.png",
                notAvailablePath: BASEURL + "/images/not_available-generic.png"
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
                let fileEl = fuploader.fineUploader("getItemByFileId", id);
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
                    let image_id = response.imageId;
                    let fileEl = fuploader.fineUploader("getItemByFileId", id),
                        imageEl = fileEl.find(".uploaded-image");

                    imageEl.html('<input name="images[]" type="hidden" value="' + image_id + '">');
                    fuploader.fineUploader("setUuid", id, image_id);
                } else {
                    btn_upload_enable();
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

    $('.btn_delete_album, .btn_delete_image').click(function (e) {
        let album_id = $(this).data('album-id');
        let image_id = $(this).data('image-id');

        let delete_type = 'Album';
        let data_id = album_id;
        if (!isNaN(image_id)) {
            delete_type = 'Image';
            data_id = image_id;
        }

        swal({
            title: 'Delete this ' + delete_type + '?',
            text: 'Are you sure, you want to delete this ' + delete_type + '?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            allowOutsideClick: false,
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    return $.ajax({
                        url: BASEURL + '/image/delete',
                        type: 'DELETE',
                        data: {'_token': CSRFTOKEN, 'action': delete_type, 'id': data_id},
                        dataType: 'json'
                    }).done(function (msg) {
                        resolve();
                    }).fail(function (jqXHR) {
                        reject('Error: ' + ((jqXHR.responseJSON) ? jqXHR.responseJSON : jqXHR.statusText));
                    });
                });
            }
        }).then(function () {
            swal(delete_type + " Deleted!", delete_type + " deleted successfully.", "success");
        }, function () {
            swal.resetDefaults()
        });

        e.preventDefault();
    })

})(jQuery);