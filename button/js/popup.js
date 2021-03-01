/* we are turning off the linter for right now in this file */
/* eslint-disable */
jQuery(function($) {
    // call popup
    $('body').on('click', '.infogr_btn_new', function(e) {
        var igr_fieldid = $(this).siblings().find('.infogram--shortcode').eq(0).attr('id') || false;
        if(igr_fieldid) {
          $('body').data('igrfieldid', igr_fieldid);
        }
        $('#infogr_media_popup').fadeIn(200);
        $('.infogr_popup_content').html('').addClass('loader');


        var data = {
            'action': 'infogram_dialog'
        };

        jQuery.post(ajaxurl, data, function(response) {
            $('.infogr_popup_content').removeClass('loader').html(response);
        });
    });

    // close popup
    $('#close_infogr_popup').click(function() {
        $('#infogr_media_popup').fadeOut(200);
        $('#infogr_add_embed').val('');
        err_remove();
    });

    // on click on text field remove error
    $('#infogr_add_embed').on('input propertychange paste click', function() {
        err_remove();
    });

    // input graphics with link
    $('#infogr_embed').submit(function() {
        var check = ['https://infogram.com/', 'https://e.infogram.com/', '?src=embed', '/'];
        var link = $('#infogr_add_embed').val();
        var loc = $('body').data('igrfieldname') || false;
        var haspar = $('body').data('igrhasparent') || false;

        if (link.indexOf(check[0]) + 1) {
            link = link.replace(check[0], '');
            if (link.indexOf(check[3]) + 1) {
                inforg_error(true);
                return false;
            } else {
                err_remove();
            }
        } else if (link.indexOf(check[1]) + 1) {
            if (link.indexOf(check[2]) + 1) {
                link = link.replace(check[1], '');
                link = link.replace(check[2], '');
            } else {
                link = link.replace(check[1], '');
            }
            if (link.indexOf(check[3]) + 1) {
                inforg_error(true);
                return false;
            } else {
                err_remove();
            }
        } else {
            inforg_error(true);
            return false;
        }

        inforg_error(false);
        err_remove();

        infogr_generate_shortcode(link, false, loc, haspar);
        $('#infogr_add_embed').val('');

        return false;
    });

    // show error
    function inforg_error(show) {
        if (show) {
            if (!$('span').is('.infogr_error')) {
                $('#infogr_embed').append('<span class="infogr_error">Please use a valid infogram.com URL that contains an infographic.</span>').addClass('infogr_error');
            }
        } else {
            if ($('span').is('.infogr_error')) {
                $('span.infogr_error').remove();
                $('#infogr_embed').removeClass('infogr_error');
            }
        }
    }

    // remove error
    function err_remove() {
        if ($('span').is('.infogr_error')) {
            $('span.infogr_error').remove();
            $('#infogr_embed').removeClass('infogr_error');
        }

    }

    // Generate the shortcode
    function infogr_generate_shortcode(embed, code, loc) {
        // generate the shortcode
        var shortcode = '[infogram';
        if (embed) {
            shortcode = shortcode + ' id="' + embed + '"';
        }
        if (code) {
            shortcode = shortcode + ' prefix="' + code + '"';
        }
        shortcode = shortcode + ']';

        // do something with it
        if(loc && loc != '') {
          /* DISABLING THE API CALL ENTIRELY FOR NOW
          // we can't just use getField with the name because for some reason it gives us the wrong thing
          // so we have to be more specific
          var acf_searchparams = {key: key, limit: 1, suppressFilters: true};
          if(hasparent) {
            acf_searchparams.parent = $(hasparent);
          }
          */

          // we're now using direct jQuery because somehow the ACF API is just not happy
          var acf_field = $('#'+loc);
          acf_field.removeProp('readonly');
          acf_field.val(shortcode);
          acf_field.prop('readonly', 'readonly');

        } else {
          window.send_to_editor(shortcode);
        }

        // close the popup and clear any body data we've stashed
        $('#close_infogr_popup').click();
        $('body').removeData(['igrfieldid']);
    }

    // Generate the shortcode
    $('body').on('click', '.infographic', function() {
        var embed = $(this).attr('data-embed');
        var code = $(this).attr('data-code');
        var loc = $('body').data('igrfieldid') || false;
        infogr_generate_shortcode(embed, code, loc);
    });
});
