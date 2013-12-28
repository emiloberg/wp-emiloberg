/*global confirm*/
/*global wp,ajaxurl*/
/*global dominantColorPickerOptions*/
/*jshint devel:true */

/**
 * Above lines is for JSHint
 * - confirm: browser confirm box
 * - wp and ajaxurl: set by Wordpress
 * - dominantColorPickerOptions: set by inline javascript where used
 */

/**
 * GLOBALS
 *
 */
var selectedCategory = ''; //Global variable to be used on category image selection/positioning.
var currentCategoryPreviewImage = '';

/**
 * HELPER FUNCTIONS
 *
 */
function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function showCatImagesSaveWarning(catId) {
    jQuery('.category-images-save-warning').show();
    jQuery('tr[data-term=' + catId + ']').addClass('changed');
}

/**
 * COLOR PICKER
 *
 */

// Show color picker to pick dominant color of an image in the document library.
// Documentation for wpColorPicker:
// http://automattic.github.io/Iris/
jQuery(document).on('focus', '.color-input', function(){
    jQuery('.color-input').wpColorPicker(dominantColorPickerOptions);

} );


/**
 * ============================================== CATEGORY IMAGE ==============================================
 *
 */


/**
 * Show value of <input type="range"> in <output>
 *
 */
function updateRangeOutput(currentSlider) {
    var currentValue = currentSlider.val();
    var currentSliderName = currentSlider.attr('name');
    var outputElement = jQuery("output[for='" + currentSliderName + "']");
    outputElement.html(currentValue + '%');
}

/**
 * Update the image position fields
 *
 */
function updateImagePositions() {
    jQuery('#eo_theme_options\\[catimage-position-' + selectedCategory + '\\]').val(
        jQuery('#eo_theme_options\\[previews-position-sidebar\\]').val() + ' ' +
            jQuery('#eo_theme_options\\[previews-position-top\\]').val()
    );
}

/**
 * Get the image position fields and set range sliders to that value
 *
 */
function getImagePositionAndSetToSliders() {
    var searchString = jQuery('#eo_theme_options\\[catimage-position-' + selectedCategory + '\\]').val();
    var positions = searchString.split(" ");
    if( isNumber(positions[0]) && isNumber(positions[1]) ) {
        jQuery('#eo_theme_options\\[previews-position-sidebar\\]').val(positions[0]);
        jQuery('#eo_theme_options\\[previews-position-top\\]').val(positions[1]);
    } else {
        jQuery('#eo_theme_options\\[previews-position-sidebar\\]').val('50');
        jQuery('#eo_theme_options\\[previews-position-top\\]').val('50');
    }

    updateRangeOutput(jQuery('#eo_theme_options\\[previews-position-sidebar\\]'));
    updateRangeOutput(jQuery('#eo_theme_options\\[previews-position-top\\]'));
    updateImagePositions();
}

/**
 * Update the the preview images.
 *
 */
function updateCategoryImagePreview(posX, posY, previewBox) {
    var htmlPreview = '<div class="section-image" style="background-image: url(\'' + currentCategoryPreviewImage + '\'); background-position: ' + posX + '% ' + posY + '%;">';
    jQuery(previewBox).html(htmlPreview);
}

function updateBothCategoryImagePreviewBoxs() {
    updateCategoryImagePreview(50, jQuery('#eo_theme_options\\[previews-position-top\\]').val(), '#image-preview-top');
    updateCategoryImagePreview(jQuery('#eo_theme_options\\[previews-position-sidebar\\]').val(), 50, '#image-preview-sidebar');
}

function openCategoryPreviewImagePositionPopup(categoryId) {

    var imageId = jQuery('#eo_theme_options\\[catimage-' + categoryId + '\\]').val();
    jQuery.ajax({
        url : ajaxurl,
        type : 'POST',
        cache: 'false',
        data: {
            'action':'ajax_get_category_image_from_image_id',
            'imageid': imageId,
            'imagesize': 'image-1200'
        }
    })
        .done(function(response) {
            currentCategoryPreviewImage = response;

            getImagePositionAndSetToSliders();
            updateBothCategoryImagePreviewBoxs();

            //Open positioning popup
            jQuery.magnificPopup.open({
                items: {
                    src: '#popup-position-image',
                    type: 'inline'
                }
            }, 0);
        });
}



function toggleCategoryImageEditLinks(termId) {
    if( jQuery('#eo_theme_options\\[catimage-' + selectedCategory + '\\]').val().length > 0 ) {
        jQuery('tr[data-term=' + termId + '] .remove-image').show();
        jQuery('tr[data-term=' + termId + '] .position-image').show();
    } else {
        jQuery('tr[data-term=' + termId + '] .remove-image').hide();
        jQuery('tr[data-term=' + termId + '] .position-image').hide();
    }
}



/**
 * Update the form field with the new image ID
 *
 */
function updateFormWithNewCategoryImageId(categoryId, imageId) {
    jQuery('#eo_theme_options\\[catimage-' + categoryId + '\\]').val(imageId);
}

/**
 * AJAX request to get and set the new category image thumbnail in the table
 *
 */
function updateCategoryImageThumbnail(categoryId, imageId) {
    jQuery.ajax({
        url : ajaxurl,
        type : 'POST',
        cache: 'false',
        data: {
            'action':'ajax_get_category_image_small_preview',
            'imageid': imageId
        }
    })
        .done(function(response) {
            jQuery('tr[data-term=' + categoryId + '] .category-image-list-preview').html(response);
        });
}


/**
 * Use WP Image Library for selecting Category Image.
 *
 */
jQuery(document).ready(function(){
    var custom_uploader;

    jQuery('.image-select-link').click(function(e) {

        selectedCategory = jQuery(this).data("category");

        e.preventDefault();
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Category Image',
            button: {
                text: 'Choose Category Image'
            },
            multiple: false
        });

        //When a file is selected, do a bunch load of stuff
        custom_uploader.on('select', function() {
            var thisimage;
            thisimage = custom_uploader.state().get('selection').first().toJSON();
            updateFormWithNewCategoryImageId(selectedCategory, thisimage.id);
            updateCategoryImageThumbnail(selectedCategory, thisimage.id);
            toggleCategoryImageEditLinks(selectedCategory);
            showCatImagesSaveWarning(selectedCategory);
            openCategoryPreviewImagePositionPopup(selectedCategory);
        });

        //Open the uploader dialog
        custom_uploader.open();
    });
});



/**
 * DOM Ready
 *
 */
jQuery(function() {


    /**
     * Initialize Popup
     * Used for "edit category" popup
     *
     */
    jQuery('.iframe-popup').magnificPopup({
        type: 'iframe'
    });


    //React when user changes the category image position sliders
    // - Show value of <input type="range"> in <output>
    // - Update the settings field.
    // - Show warning of unsaved changes
    jQuery("input[type='range'].has-output").change(function() {
        updateRangeOutput(jQuery(this));
        updateImagePositions();
        showCatImagesSaveWarning(selectedCategory);
    });

    //Update Image Preview - Sidebar
    jQuery('#eo_theme_options\\[previews-position-sidebar\\]').change(function() {
        updateCategoryImagePreview(jQuery(this).val(), 50, '#image-preview-sidebar');
    });

    //Update Image Preview - Top
    jQuery('#eo_theme_options\\[previews-position-top\\]').change(function() {
        updateCategoryImagePreview(50, jQuery(this).val(), '#image-preview-top');
    });

    //Remove category image on click 'Remove'
    jQuery('.remove-image-link').click(function (event) {
        event.preventDefault(); //Prevent scrolling to top as this is a #-link
        selectedCategory = jQuery(this).data("category");
        if( confirm( 'Are you sure you want to remove the image for category: ' + jQuery('tr[data-term=' + selectedCategory + '] .category-name').text() + '?' ) ) {
            jQuery('#eo_theme_options\\[catimage-' + selectedCategory + '\\]').val('');
            jQuery('#eo_theme_options\\[catimage-position-' + selectedCategory + '\\]').val('');
            toggleCategoryImageEditLinks(selectedCategory);
            showCatImagesSaveWarning(selectedCategory);
            jQuery('tr[data-term=' + selectedCategory + '] .category-image-list-preview').html('');
        }
    });

    //Open 'Position Category Image' popup when clicking "Position"
    jQuery('.position-image-link').click(function (event) {
        event.preventDefault(); //Prevent scrolling to top as this is a #-link
        selectedCategory = jQuery(this).data("category");
        openCategoryPreviewImagePositionPopup(selectedCategory);
    });


    // Preview the category image in a popup, when clicked.
    jQuery(document).on('click', '.category-image-table-preview', function() {
        //Set clicked image as HTML in the popup
        jQuery('#popup-preview-only-image').html('<img class="preview-big-cat-image" src="' + jQuery(this).data('full') + '">');

        //Open Popup
        jQuery.magnificPopup.open({
            items: {
                src: '#popup-preview-only-image',
                type: 'inline'
            },
            closeOnContentClick: true
        }, 0);
    });


    //Save category images form when user clicks unsaved changes warning link
    jQuery('.save-settings-link').click(function () {
      jQuery('#category-images-form').submit();
    });


    //Hide all elements with 'fade'-class after 2 seconds
    //Like the "options saved" message
    jQuery('.fade').delay(2000).slideUp(400);



});