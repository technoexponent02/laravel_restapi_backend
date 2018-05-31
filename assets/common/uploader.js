/*
 * Image upload library by tuhinmandal@yahoo.in
 */
// Function to allow an event to fire after all images are loaded
$.fn.imagesLoaded = function () {
    var $imgs = this.find('img[src!=""]');
    if (!$imgs.length) {return $.Deferred().resolve().promise();}
    var dfds = [];
    $imgs.each(function() {
        var dfd = $.Deferred();
        dfds.push(dfd);
        var img = new Image();
        img.onload = function(){dfd.resolve();}
        img.onerror = function(){dfd.resolve();}
        img.src = this.src;

    });
    return $.when.apply($,dfds);
}
/*-----------------------------------------------------------------*/
var ALLOWED_TYPES = ['gif', 'png', 'jpg', 'jpeg', 'bmp'];
var UPLOAD_LIMIT = 8;
var gImgCount = 0;
var gExistingImages = [];

$(function() {
    // Change the upload limit
    $("#uploadLimit").html(UPLOAD_LIMIT);
    var $sel_img = $("#sel_img");
    $sel_img.disableSelection();
    // upload image to server
    var j = 0;
    var $upload_img = $("#upload_img");
    $upload_img.change( function(event) {
        if(gImgCount >= UPLOAD_LIMIT) {
            alert("Maximum " + UPLOAD_LIMIT + " photos.");
            return false;
        }
        var tempFileNameArr = [];
        if(!event.target.files || !window.FileReader) {
            console.log("No file chosen.");
            return false;
        }
        var ext;
        var doUpload = true;
        var formData = new FormData();

        var files = event.target.files;

        var filesArr = Array.prototype.slice.call(files);
        filesArr.forEach(function(f, key) {
            ext = f.name.split('.').pop().toLowerCase();
            if(!f.type.match("image.*")) {
                alert('Wrong file format. Upload images only.');
                doUpload = false;
                return false;
            }
            else if($.inArray(ext, ALLOWED_TYPES) == -1) {
                alert('Only jpeg, jpg, png, gif, bmp files with the extension allowed!');
                doUpload = false;
                return false;
            }

            formData.append('car_image', f);

            var filename = f.name;
            tempFileNameArr.push(filename);

            if($.inArray( filename, gExistingImages ) != -1) {
                alert("You have selected the same image!!");
                doUpload = false;
                return false;
            }

            j = j + 1;
        });

        if(doUpload) {
            var addFileDiv = $("#addFileDiv");
            $.ajax({
                url: UPLOAD_URL,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    var $fileLoader = $("#fileLoader");
                    if(gImgCount == 0)
                        $fileLoader.addClass("bigAddFile");
                    else
                        $fileLoader.removeClass("bigAddFile");
                    //$fileLoader.show();
                    showUploadProgress();
                    addFileDiv.hide();
                },
                success: function (response) {
                    if (response.status === 'FAILED') {
                        var err = '';
                        response.error.car_image.forEach(function(v, key) {
                            err += v;
                        });
                        if(err != '')
                            alert(err);
                        $("#fileLoader").hide();
                        // Upsize Add new div
                        if(!gImgCount)
                            $("#addFileDiv").addClass("bigAddFile");
                        if(gImgCount < UPLOAD_LIMIT)
                            addFileDiv.show();
                    } else {
                        showUploadSuccess();
                        gImgCount++;
                        // Push to existing files
                        tempFileNameArr.forEach(function(val, key) {
                            gExistingImages.push(val);
                        });
                        // Downsize Add new div
                        addFileDiv.removeClass("bigAddFile");
                        if(gImgCount < UPLOAD_LIMIT)
                            addFileDiv.show();
                        $("#add_no").html(gImgCount + 1);
                        // Show uploaded image
                        var imageTag = '<img src="'+ response.path +'" alt="New Image"/>';
                        var imagediv = '<div class="uploadBX singleUploader" data-num="' + gImgCount + '">' +
                                '<span class="del" data-img-id="' + response.img_id + '"><i class="fa fa-close"></i></span>' +
                                '<span class="no">' + gImgCount + '</span>' +
                                '<div class="uploadSuccess"></div>' + imageTag +
                                '<input type="hidden" name="ad_file_img[]" class="ad_file_img" value="'+ response +'" />' +
                                '</div>'
                            ;
                        $("#tempDiv").html(imageTag).imagesLoaded().then(function(){
                            $("#fileLoader").hide();
                            $sel_img.append(imagediv);
                        });
                    }
                },
                error: function() {
                    $("#fileLoader").hide();
                    // Upsize Add new div
                    if(!gImgCount)
                        $("#addFileDiv").addClass("bigAddFile");
                    if(gImgCount < UPLOAD_LIMIT)
                        addFileDiv.show();
                }
            });
        }
    });

    // Delete image
    $(document).on("click", ".del", function () {
        gImgCount = gImgCount > 1 ? --gImgCount : 0;
        var img_id = $(this).attr("data-img-id");
        var $elm = $(this).parent();
        var addFileDiv = $("#addFileDiv");
        var delIndex = $elm.data("num");
        gExistingImages.splice((delIndex  - 1), 1);
        // Remove the image from server
        var formData = {
            "img_id" : img_id
        };
        $.ajax({
            url: REMOVE_URL,
            type: "POST",
            data: formData,
            beforeSend: function() {

            },
            success: function (response) {
                if (response.status === 'FAILED') {

                } else {

                }
            },
            error: function() {

            }
        });

        // Show upload div
        if(gImgCount < UPLOAD_LIMIT)
            addFileDiv.show();
        // Upsize Add new div
        if(!gImgCount)
            addFileDiv.addClass("bigAddFile");
        $("#add_no").html(gImgCount + 1);
        // Remove the image
        $elm.remove();
        // Update each upload box
        var current;
        $(".singleUploader").each(function() {
            current = $(this).data("num");
            if(current > delIndex) {
                $(this).attr("data-num", current - 1);
                $(this).find(".no").html(current - 1);
            }
        });

    });
    // Sort images
    $sel_img.sortable({
        appendTo: 'body', helper: 'clone', zIndex: 300,
        stop: function(event, ui) {
            var num = 1;
            $(".singleUploader").each(function() {
                $(this).attr("data-num", num);
                $(this).find(".no").html(num);
                num ++;
            });
        }
    });
});

function showUploadProgress() {
    $("#uploadProgress").show();
    $("#uploadSuccess").hide();
    $("#fileLoader").removeClass("success");
    $("#fileLoader").show();
}

function showUploadSuccess() {
    $("#uploadProgress").hide();
    $("#uploadSuccess").show();
    $("#fileLoader").addClass("success");
    $("#fileLoader").show();
}

