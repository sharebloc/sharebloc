/**
 *
 * jquery file should be included before
 *
 */

$.ajaxSetup({
  url: 'gate.php',
  type: 'POST',
  dataType: 'json',
  timeout: 30000,
  error: function(jqXHR, textStatus, errorThrown){onServerAnswerCommon({err_code:-1, message:'Request to server failed: '+textStatus+errorThrown, type:'error'});},
  success: function(data, textStatus, jqXHR){onServerAnswerCommon(data);}
});

function onServerAnswerCommon(data) {
    stop_blinking = true;
    if (blinking) {
        setTimeout(function(){onServerAnswerCommon(data)}, 250);
        return false;
    }

    $("#debug").text(data.debug);
    lockInputs(false);
    if (data.err_code) {
        if (data.message) {
            data.message = data.message.replace("\\n", "\n");
            alert(data.message);
        }
        return false;
    }
    onServerAnswer(data);
    return false;
}

var inputs_locked = false;
function lockInputs(lock) {
    inputs_locked = lock;
}
function isLocked() {
    return inputs_locked;
}

var blinking = false;
var stop_blinking =false;
// WARN: clears inline style after blinking ends
// todo check possible problems with delayed functions interaction
function blink(j_elem, wait) {
    if (wait==true) {
        stop_blinking = false;
        setTimeout(function(){blink(j_elem, false)}, 400);
        return;
    }

    if (stop_blinking) {
        j_elem.attr({"style": ''});
        stop_blinking = false;
        blinking = false;
        return;
    }
    blinking = true;
    j_elem.animate({"opacity": 0.2}, 500).animate({"opacity": 1},800, function(){blink(j_elem, false)});
}

/* jQuery plugin for images full-view */
/* author support@deepshiftlabs.com */
(function($){
    $.fn.fullImgClick = function() {
        var margin = 5;
        function showBigImage(thumb) {
            $(".full_size_image").click();
            var thumb_width = thumb.prop('width');
            var thumb_height = thumb.prop('height');

            var thumb_offset = thumb.offset();
            var big_img = $("<img>");
            big_img.css('cursor', 'crosshair');
            big_img.addClass('full_size_image');

            var my_thumb = thumb;

            big_img.load(function(){
                var big_img_height = big_img.prop("height");
                var big_img_width = big_img.prop("width");

                my_thumb.attr('data-realHeight', big_img_height);
                my_thumb.attr('data-realWidth', big_img_width);

                if (big_img_width <= thumb_width) {
                    return false;
                }
                if (big_img_width > $(document).width()) {
                    var ratio = big_img_width / big_img_height;
                    big_img_width = $(document).width()-margin*2;
                    big_img_height = big_img_width/ratio;
                }
                big_img.css({
                    'position' : 'absolute',
                    'display' : 'block',
                    'left' : thumb_offset.left + 'px',
                    'top' : thumb_offset.top + 'px',
                    'width' : thumb_width + 'px',
                    'height' : thumb_height + 'px'
                });

                var target_top = thumb_offset.top - ((big_img_height - thumb_height)/2);
                if (target_top < $(window).scrollTop()+margin) {
                    target_top = $(window).scrollTop()+margin;
                }
                var target_left = thumb_offset.left - ((big_img_width - thumb_width)/2);
                if (target_left < $(window).scrollLeft()+margin) {
                    target_left = $(window).scrollLeft()+margin;
                }
                var target_height = big_img_height;
                var target_width = big_img_width;

                $("body").append(big_img);

                big_img.animate({
                    'height' : target_height,
                    'width' : target_width,
                    'top' : target_top,
                    'left' : target_left
                });

                big_img.click(function () {
                     $(this).animate({
                            'height' : thumb_height,
                            'width' : thumb_width,
                            'top' : thumb_offset.top,
                            'left' : thumb_offset.left
                        },
                        { 'complete' : function(){$(this).remove();}
                    });
                });
            }).attr('src', thumb.attr('src')); // src should be after the onload handler for IE8
        }
        this.each(function() {
            if ($(this).parent('a').length) {
                return;
            }
            var me = $(this);
            var big_img = $("<img>");
            big_img.addClass('hide');
            big_img.load(function(){
                if ($(this).prop("width") <= me.prop('width')) {
                    return false;
                } else {
                    me.css('cursor', 'cell');
                    me.click(function(){showBigImage(me);});
                }
                $(this).remove();
            }).attr('src', me.attr('src'));
        });
     };
})(jQuery);