/**
 * Theme: Adminox Admin Template
 * Author: Coderthemes
 * Form Advanced
 */


jQuery(document).ready(function () {

    // Select2
    $(".select2").select2({
        
            placeholder: 'Select..',
            allowClear: true
    });
    $(".mselect2").select2({
            maximumSelectionLength: 3,
            placeholder: 'Select..',
            allowClear: true
    });

});