;
(function ($) {
    var WidgetElementsPopupHandler = function ($scope, $) {

        var dce_popup_settings = get_Dyncontel_ElementSettings($scope);
        var id_scope = $scope.attr('data-id');
        var id_popup = $scope.find('.dce-modal').attr('id');
        //console.log(id_popup);
        var is_animate = false;
        //

        function dce_show_modal(id_modal) {
            //$('#'+id_modal).closest('.elementor-element').data('settings');
            //console.log('show modal: '+id_modal);
            //alert('apro' + id_modal);
            var id_modal_scope = id_modal.split('-');
            id_modal_scope.pop();
            id_modal_scope = id_modal_scope.join('-');

            var open_delay = 0;
            if (dce_popup_settings.open_delay.size) {
                open_delay = dce_popup_settings.open_delay.size;
            }
            //alert(dce_popup_settings.open_delay+' '+open_delay);
            if(!is_animate)
            setTimeout(function () {
                //aggiungo al body la classe aperto
                if (!elementorFrontend.isEditMode()) {
                    is_animate = true;
                    $('body').removeClass('modal-close-' + id_modal).removeClass('modal-close-' + id_modal_scope);
                    $('body').addClass('modal-open-' + id_modal).addClass('modal-open-' + id_modal_scope).addClass('dce-modal-open');
                    $('html').addClass('dce-modal-open');

                    $('#' + id_modal + ' .modal-dialog').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (el) {
                       is_animate = false;
                    });

                }
                if (dce_popup_settings.wrapper_maincontent) {
                    $(dce_popup_settings.wrapper_maincontent).addClass('dce-push').addClass('animated').parent().addClass('perspective');
                }
                console.log('show '+id_modal);
                $('#' + id_modal).show();
                //$('#'+id_modal+' .modal-dialog').addClass(dce_popup_settings.open_animation); //modal();
                $('#' + id_modal + '-background').show().removeClass('fadeOut').addClass('fadeIn').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (el) {
                       is_animate = false;
                    });

            }, open_delay);
        }

        function dce_hide_modal(id_modal) {
            //$('#'+id_modal).closest('.elementor-element').data('settings');
            // set cookie       
            //alert('chiudo' + id_modal);
            var id_modal_scope = id_modal.split('-');
            id_modal_scope.pop();
            id_modal_scope = id_modal_scope.join('-');
            //console.log('modal-close-' + id_modal_scope);

            if (!dce_popup_settings.always_visible) {
                dce_setCookie(id_modal, 1, dce_popup_settings.cookie_lifetime);
            }
            var settings_close_delay = 0;
            if (dce_popup_settings.close_delay) {
                settings_close_delay = dce_popup_settings.close_delay;
            }

            //levo dal body la classe aperto
            $('body').removeClass('modal-open-' + id_modal).removeClass('modal-open-' + id_modal_scope);
            $('body').addClass('modal-close-' + id_modal).addClass('modal-close-' + id_modal_scope);

            //
            $('#' + id_modal + '-background').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (el) {
                console.log('hide bg '+id_modal);
                $('#' + id_modal + '-background').hide();
                $(el.currentTarget).off('webkitAnimationEnd oanimationend msAnimationEnd animationend');
                //$('#' + id_modal + ' .modal-dialog').trigger('animationend');
            });

            //
            $('#' + id_modal + ' .modal-dialog').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function (el) {
                console.log('hide '+id_modal);
                $('#' + id_modal).hide();
                //$(el.currentTarget).removeClass(dce_popup_settings.close_animation);
                //$(el.currentTarget).off('webkitAnimationEnd oanimationend msAnimationEnd animationend');
                setTimeout(function () {
                    if (!elementorFrontend.isEditMode()) {
                        $('body').removeClass('modal-close-' + id_modal).removeClass('modal-close-' + id_modal_scope).removeClass('dce-modal-open');
                        $('html').removeClass('dce-modal-open');
                        $(el.currentTarget).off('webkitAnimationEnd oanimationend msAnimationEnd animationend');
                    }
                    if (dce_popup_settings.wrapper_maincontent)
                        $(dce_popup_settings.wrapper_maincontent).removeClass('dce-push').removeClass('animated').parent().removeClass('perspective');

                }, 300);
            });

            setTimeout(function () {
                //alert(dce_popup_settings.close_animation);
                //$('#'+id_modal+' .modal-dialog').removeClass(dce_popup_settings.open_animation).addClass(dce_popup_settings.close_animation); //modal();
                $('#' + id_modal + '-background').removeClass('fadeIn').addClass('fadeOut');
            }, settings_close_delay);
        }



        /*document.addEventListener('DOMContentLoaded', function() {
         
         
         
         }, false);*/





        //var dce_popup_settings = $(this).closest('.elementor-element').data('settings');
        var modal = $scope.find('.dce-popup-container-' + id_scope);

        function push_actions() {
            /*if( typeof elementSettings.enabled_push !== 'undefined' &&  elementSettings.enabled_push){
             //alert(elementSettings.enabled_push);
             if(!$('#dce-wrap').length){
             
             // avvolgo il contenuto del body per poterlo spostare
             $('body').wrapInner('<div id="dce-outer-wrap"><div id="dce-wrap" class="dce-wrap-animated animated"></div></div>');
             //sposto il modale fuori
             
             }
             // ....
             
             }*/
            if (!elementorFrontend.isEditMode()) {
                $(modal).prependTo("body");
            }

        }
        push_actions();



        // - * - * - * - * - * - * - * - * - * - * -


        // ON LOAD
        $('.dce-popup-container-'+id_scope+'.dce-popup-onload').each(function () {

            //$(this).closest('.elementor-element').data('settings');
            var id_modal = $(this).find('.dce-modal').attr('id');

            //console.log('trigger onload for: '+id_modal);
            // read cookie
            var cookie_popup = dce_getCookie(id_modal);
            if (dce_popup_settings.always_visible) {
                cookie_popup = false;
            }
            if (!cookie_popup) {
                dce_show_modal(id_modal);
            } else {
                //console.log('cookie already setted for: '+id_modal);
            }
        });

        // BUTTON
        $scope.on('click', '.dce-button-open-modal, .dce-button-next-modal', function () {
            var id_modal = $(this).data('target')
            //console.log('trigger click btn for: '+id_modal);
            dce_show_modal(id_modal);
        });


        /*// WIDGET
        $('.dce-popup-widget').each(function () {
            var id_modal = $(this).find('.dce-modal').attr('id');
            //$(this).closest('.elementor-element').data('settings');
            var cookie_popup = dce_getCookie(id_modal);

            if (dce_popup_settings.always_visible) {
                cookie_popup = false;
            }
            if (cookie_popup) {
                $(this).removeClass('dce-popup-widget');
                //console.log('cookie setted for: '+id_modal);
            }
        });
        //alert($('.dce-popup-widget').length);
        if ($('.dce-popup-widget').length) {
            $(window).on('load scroll resize', function () {
                $('.dce-popup-widget').each(function () {
                    if ($(this).visible()) {
                        $(this).removeClass('dce-popup-widget');
                        var id_modal = $(this).find('.dce-modal').attr('id');
                        //console.log('trigger widget for: '+id_modal);
                        dce_show_modal(id_modal);
                        //console.log('visible widget: '+id_modal);
                    }
                });
            });
        }*/

        // SCROLL
        if ($('.dce-popup-container-'+id_scope+'.dce-popup-scroll').length) {
            $(window).on('scroll', function () {
                $('.dce-popup-scroll').each(function () {
                    //$(this).closest('.elementor-element').data('settings');
                    if ($(window).scrollTop() > dce_popup_settings.scroll_display_displacement) {
                        $(this).removeClass('dce-popup-scroll');
                        var id_modal = $(this).find('.dce-modal').attr('id');
                        //console.log('trigger scroll for: '+id_modal);
                        dce_show_modal(id_modal);
                        //console.log('visible scroll: '+id_modal);
                    }
                });
            });
        }

        $(window).on('scroll', function () {
            $('.modal-hide-on-scroll:visible').each(function () {
                $(this).removeClass('modal-hide-on-scroll');
                dce_hide_modal($(this).attr('id'));
            });
        });

        $(document).on('keyup', function (evt) {
            if (evt.keyCode == 27) {
                $('.modal-hide-esc:visible').each(function () {
                    //$(this).removeClass('modal-hide-esc');
                    dce_hide_modal($(this).attr('id'));
                });
            }
        });

        // la X
        $(document).on('click', '#'+id_popup+'.dce-modal .dce-modal-close, .dce-modal .dce-button-close-modal, .dce-modal .dce-button-next-modal', function () {
            //console.log('close '+$(this).closest('.dce-modal').attr('id'));
            dce_hide_modal(id_popup);
            //alert($(this).closest('.dce-modal').attr('id'));
            return false;
        });
        // Il Layer
        $(document).on('click', '#'+id_popup+'-background.dce-modal-background-layer-close', function () {
            //dce_hide_modal($(this).attr('data-target'));
            dce_hide_modal(id_popup);
        });

        // VIDEO
        /*(function($) {
         $('#popup-<?php echo $selectedPopup->post->ID; ?>').on('hide.bs.modal', function(e) {
         var $if = $(e.delegateTarget).find('iframe');
         var src = $if.attr("src");
         $if.attr("src", '/empty.html');
         $if.attr("src", src);
         });
         })($);*/

    }

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/dyncontel-popup.default', WidgetElementsPopupHandler);
    });
})(jQuery);