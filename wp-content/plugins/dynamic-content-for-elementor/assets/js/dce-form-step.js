function dce_show_step(target, element, direction = 'next', scroll = false) {
    //console.log('try step ' + target);
    var form = jQuery('.elementor-element-' + element);
    var step = jQuery('.elementor-element-' + element + ' #dce-form-step-' + target);
    jQuery('.elementor-element-' + element + ' .elementor-error').removeClass('elementor-error');
    jQuery('.elementor-element-' + element + ' .dce-form-step').hide().addClass('elementor-hidden');
    jQuery('.elementor-element-' + element + ' .dce-step-active').removeClass('dce-step-active');

    jQuery('.elementor-element-' + element + ' .dce-step-active-progressbar').removeClass('dce-step-active-progressbar');
    jQuery('.elementor-element-' + element + ' #dce-form-step-' + target + '-progressbar').addClass('dce-step-active-progressbar');
    
    jQuery('.elementor-element-' + element + ' .dce-step-active-summary').removeClass('dce-step-active-summary');
    jQuery('.elementor-element-' + element + ' #dce-form-step-' + target + '-summary').addClass('dce-step-active-summary');
    jQuery('.elementor-element-' + element + ' #dce-form-step-' + target + '-summary').addClass('dce-step-filled-summary');
    //console.log(jQuery('.elementor-element-' + element + ' .dce-form-step-summary').not('.dce-step-filled-summary').length);
    if (!jQuery('.elementor-element-' + element + ' .dce-form-step-summary').not('.dce-step-filled-summary').length) {
        jQuery('.elementor-element-' + element + ' .dce-form-summary-wrapper .elementor-button-wrapper').show();
    }
    
    //alert(target);
    if (step.hasClass('dce-form-visibility-step')) {
        if ((step.hasClass('dce-form-visibility-step-hide-init') && !step.hasClass('dce-form-visibility-step-hide'))
                || (step.hasClass('dce-form-visibility-step-show-init') && !step.hasClass('dce-form-visibility-step-show'))) {
            
            dce_epro_applyStep(direction, step);
            console.log('skip step ' + target + ', direction ' + direction);
            var new_target = step.find('.elementor-button-' + direction).attr('data-target');
            return dce_show_step(new_target, element, direction, scroll);       
        }
    }
    
    console.log('apply step ' + target );
    dce_epro_applyStep(direction, step);
    
    console.log('show step ' + target );
    //step.show().addClass('dce-step-active');
    step.css('display', 'flex').addClass('dce-step-active');
    step.removeClass('elementor-hidden');
    
    if (scroll) {
        console.log('scroll to top');
        jQuery('html, body').animate({
            scrollTop: jQuery('.elementor-element-' + element).offset().top
        }, 500);
    }
    
    return true;
    
    
    /*
    elementorProFrontend.elements.$indicators = step;
    elementorProFrontend.elements.$currentIndicator = step;
    if ('progress_bar' === form.state.stepsType) {
        form.setProgressBar();
    } else {
        form.updateIndicatorsState(direction);
    }
    */
}

function dce_epro_applyStep(direction, step) {
    if (direction == 'next') {
        step_direction = step.prev();
    } else {
        step_direction = step.next();
    }
    if (step_direction) {
        console.log('applyStep '+step_direction.attr('id'));
        if (step_direction.find('.e-form__buttons__wrapper__button-' + direction).length) {
            console.log('epro click');
            step_direction.find('.e-form__buttons__wrapper__button-' + direction).trigger('click');
            return true;
        }
    }
    return false;
}

function dce_valid_step(step) {
    
}
function dce_validate_step(step) {
    var isValid = true;
    
    step.find('.elementor-field-group [required]').each(function (index, el) {
    if (!el.checkValidity()) {
        el.reportValidity();
        return isValid = false;
      }
    });
    return isValid;
    
    /*step.find('.elementor-field-required input, .elementor-field-required select, .elementor-field-required textarea').each(function () {
        if (jQuery(this).prop('required') && !jQuery(this).prop('disabled')) {
            switch (jQuery(this).attr('type')) {
                case 'checkbox':
                //case 'acceptance':
                case 'radio':
                    var tmp = jQuery(this).attr('id').split('-');
                    tmp.pop();
                    var base_id = tmp.join('-');
                    if (!jQuery('input[id^="' + base_id + '"]:checked').length) {
                        isValid = false;
                    }
                    break;
                default:
                    if (!jQuery(this).val() || !jQuery(this).is(':valid')) {
                        isValid = false;
                    }
            }
        }
        if (!isValid) {
            //console.log(this);
            jQuery(this).closest('.elementor-field-required').addClass('elementor-error');
        }
    });
    return isValid;*/
}

function dce_replace_field_shortcode(target, element, direction = 'next') {
    /*var step = jQuery('.elementor-element-' + element + ' #dce-form-step-' + target);
    var step_html = step.html();
    jQuery('.elementor-element-' + element + ' .elementor-field-textual, .elementor-element-' + element + ' .elementor-field-option radio:checked').each(function(){
        var field_id = dce_form_field_custom_id(jQuery(this).attr('type'), jQuery(this).attr('id'));
        console.log(field_id);
        step_html.replace('[field id="'+field_id+'"]', jQuery(this).val()); 
        step_html.replace('[form:'+field_id+']', jQuery(this).val()); 
    });
    step.html(step_html);*/
}

function dce_form_field_custom_id(type, id) {
    var tmp = id.split('-');
    switch (type) {
        case 'checkbox':
        //case 'acceptance':
        case 'radio':            
            tmp.pop();            
        default:
            return tmp.pop();
    }
    return id;
}
