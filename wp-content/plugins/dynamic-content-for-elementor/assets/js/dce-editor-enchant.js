/*
 * DCE EDITOR
 * dynamic.ooo
 */

// SELECT2 everywhere
jQuery(window).on( 'load', function() {
//jQuery(window).on('elementor:init', function () {
//jQuery( window ).on( 'elementor/frontend/init', function() {
    if (jQuery.fn.select2) {
        if ( window.elementorFrontend ) {
            elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function( $scope ) {
                jQuery('.elementor-control-type-select select').select2();
            } );
        }
        elementor.hooks.addAction( 'panel/open_editor/section', function( panel, model, view ) {
            jQuery('.elementor-control-type-select select').select2();
        } );
        elementor.hooks.addAction( 'panel/open_editor/column', function( panel, model, view ) {
            jQuery('.elementor-control-type-select select').select2();
        } );
        elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
            jQuery('.elementor-control-type-select select').select2();
        } );
    }
    setInterval(function(){
        if (jQuery.fn.select2) {
            // add navigator element toggle
            jQuery('.elementor-control-type-select select').not('.select2-hidden-accessible').each(function(){
                jQuery(this).select2();
            });
        }
    }, 1000);
});

// Hide Description
jQuery(window).on( 'load', function() {
    if ( window.elementorFrontend ) {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function( $scope ) {
            description_to_abbr();
        } );
    }
    elementor.hooks.addAction( 'panel/open_editor/section', function( panel, model, view ) {
        description_to_abbr();
    } );
    elementor.hooks.addAction( 'panel/open_editor/column', function( panel, model, view ) {
        description_to_abbr();
    } );
    elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
        description_to_abbr();
    } );

    setInterval(function(){
        // add navigator element toggle
        description_to_abbr();
    }, 1000);
});
function description_to_abbr() {
    jQuery('.elementor-control-field-description').not('.elementor-control-field-description-hidden').each(function() {
        var title = jQuery(this).siblings('.elementor-control-field').children('.elementor-control-title');
        if (title.text().trim()) {
            var text = jQuery(this).text();
            text = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            title.wrapInner('<abbr title="'+text+'"></abbr>');
            jQuery(this).addClass('elementor-control-field-description-hidden').hide();
            title.on('click', function() {
                jQuery(this).parent().siblings('.elementor-control-field-description').toggle();
                return false;
            });
        }
    });
}

jQuery(window).load(function() {
    var iFrameDOM = jQuery("iframe#elementor-preview-iframe").contents();

    // get model CID on mouse dx click
    /*iFrameDOM.on('mousedown', '.elementor-element', function(event) {
        if (event.which == 3) {
            //iFrameDOM.find('body').on('contextmenu', function() {
            var eid = jQuery(this).data('id');
            var cid = jQuery(this).data('model-cid');
            var type = jQuery(this).data('element_type');
            dce_model_cid = cid;
            console.log(type + ' - ' + eid + ' - ' + cid);
            if (dce_model_cid) {
                return false;
            }
        }
        
    });*/
    
    // add EDIT Template on Context Menu
    iFrameDOM.on('mousedown', '.elementor-editor-active .elementor:not(.elementor-edit-mode)', function(event) {
        if (event.which == 3) {
            var template_id = jQuery(this).data('elementor-id');
            var post_id = iFrameDOM.find('.elementor-editor-active .elementor.elementor-edit-mode').data('elementor-id');
            if (template_id && post_id) {
                setTimeout(function(){
                    var menu = jQuery('.elementor-context-menu:visible');                
                    if (menu.length) {                    
                        menu.find('.elementor-context-menu-list__item-template').remove();
                        var edit_url = window.location.href.replace('post='+post_id, 'post='+template_id);
                        menu.find('.elementor-context-menu-list__item-edit').after('<div class="elementor-context-menu-list__item elementor-context-menu-list__item-template" onclick="window.open(\''+edit_url+'\'); return false;"><div class="elementor-context-menu-list__item__icon"><i class="eicon-edit"></i></div><div class="elementor-context-menu-list__item__title">Edit Template</div></div>');
                    }
                }, 10);
            }
        }
    });

});


