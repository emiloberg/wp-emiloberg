<?php



/*
$termSettings = array(
    'name' => 'A-kategorin'
);

wp_update_term(2, 'category', $termSettings);
*/

/**
 * Getting options:
 * $options = get_option('sample_theme_options');
 * echo $options['sometext'];
 */


/**
 * HELPER FUNCTIONS
 *
 */


function eo_print_category_image_thumbnail($catImageId) {
    //$catImageId = eo_get_image_id_by_link($imageUrl);
    if(is_numeric($catImageId) ) {
        $catImage = wp_get_attachment_image_src($catImageId, 'image-50x37');
        $catImage = $catImage[0];
        $catImageFull = wp_get_attachment_image_src($catImageId, 'image-1200');
        $catImageFull = $catImageFull[0];
        return '<a href="#" class="category-image-table-preview" data-full="' . $catImageFull . '"><img src="' . $catImage . '"></a>';
    } else {
        return '';
    }
}

function eo_get_image_in_specific_size_from_id($imageId, $size) {
    $thisImage = '';
    if(is_numeric($imageId) ) {
        $thisImage = wp_get_attachment_image_src($imageId, $size);
        $thisImage = $thisImage[0];
    }
    return $thisImage;
}

/**
 * THEME OPTIONS
 *
 */


add_action( 'admin_init', 'theme_options_init' );
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
    register_setting( 'eo_options', 'eo_theme_options', 'theme_options_validate' );
}

/**
 * Load up the menu page
 */
function theme_options_add_page() {
    add_theme_page( __( 'EO Theme Options', 'eotheme' ), __( 'EO Theme Options', 'eotheme' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}

function eo_get_category_image_settings_row($options, $catId, $catName, $catCount, $zebra, $customCSS = '') {

    $editCategoryURL = "edit-tags.php?action=edit&taxonomy=category&post_type=custom_type&tag_ID=" . $catId;


    $isRealCategory = false;
    if(is_numeric($catId)) {
        $isRealCategory = true;
    }

    if($isRealCategory) {
        $categoryLink = get_category_link($catId);
    } else {
        $categoryLink = get_home_url();
    }


    $hideEditLinksStyle = '';
    if (empty($options['catimage-' . $catId])) {
        $hideEditLinksStyle = 'style="display:none"';
    }
    ?>

    <tr class="<?php print $zebra; ?> <?php print $customCSS; ?>" data-term="<?php print $catId; ?>">
        <td class="category-image-list-preview">
            <?php print eo_print_category_image_thumbnail($options['catimage-' . $catId]); ?>
        </td>
        <td class="post-title">
            <a target="_blank" href="<?php print $categoryLink ?>"><strong class="category-name"><?php print $catName ?></strong></a>
            <div class="row-actions">
                <?php if($isRealCategory) { ?>
                <span>
                    <a class="iframe-popup" href="<?php echo $editCategoryURL ?>">Edit Category</a> |
                </span>
                <?php } ?>

                Image:
                <span class="toggle-image-select">
                    <a href="#" class="image-select-link" data-category="<?php print $catId; ?>">Select</a>
                </span>
                <span class="position-image" <?php print $hideEditLinksStyle ?>>
                     - <a href="#" class="position-image-link" data-category="<?php print $catId; ?>">Reposition</a>
                </span>
                <span class="remove-image trash" <?php print $hideEditLinksStyle  ?>>
                     - <a href="#" class="remove-image-link" data-category="<?php print $catId; ?>">Remove</a>
                </span>
            </div>
        </td>
        <td>
            <?php print $catCount ?>
            <input id="eo_theme_options[catimage-<?php print $catId; ?>]" type="hidden" name="eo_theme_options[catimage-<?php print $catId; ?>]" value="<?php print $options['catimage-' . $catId]; ?>" />
            <input id="eo_theme_options[catimage-position-<?php print $catId; ?>]" type="hidden" name="eo_theme_options[catimage-position-<?php print $catId; ?>]" value="<?php print $options['catimage-position-' . $catId]; ?>" />
        </td>
        <td>
            <?php
            if($isRealCategory) {
                print category_description($catId);
            } else {
                print 'n/a';
            }
            ?>
        </td>
    </tr>

<?
}



/**
 * Create the options page
 */
function theme_options_do_page() {
    if ( ! isset( $_REQUEST['settings-updated'] ) )
        $_REQUEST['settings-updated'] = false;

    ?>

    <div class="wrap">
        <h2><?php print wp_get_theme() . __( ' Theme Options', 'eotheme' ) ?></h2>

        <div id="popup-preview-only-image" class="mfp-hide white-popup">
            Placeholder for preview image
        </div>

        <div id="popup-position-image" class="mfp-hide white-popup image-preview-popup">
            <h1>Position Category Image

                (<output for="eo_theme_options[previews-position-sidebar]"></output>,
                <output for="eo_theme_options[previews-position-top]"></output>)

            </h1>
            <div id="image-previews" class="clearfix">
                <div>
                    <div class="preview-container-sidebar">
                        <input id="eo_theme_options[previews-position-sidebar]" class="preview-sidebar-range has-output" type="range" name="eo_theme_options[previews-position-sidebar]" min="0" max="100" value=50" step="1">
                        <div id="image-preview-sidebar"></div>
                    </div>
                    <div class="preview-container-top">
                        <input id="eo_theme_options[previews-position-top]" class="preview-top-range has-output position-top-range" type="range" name="eo_theme_options[previews-position-top]" min="0" max="100" value="50" step="1">
                        <div id="image-preview-top"></div>
                    </div>
                </div>
            </div>
        </div>


        <?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
            <div class="updated fade"><p><strong><?php _e( 'Options saved', 'eotheme' ); ?></strong></p></div>
        <?php endif; ?>



        <form id="category-images-form" method="post" action="options.php" class="clearfix">
            <?php settings_fields( 'eo_options' ); ?>
            <?php $options = get_option( 'eo_theme_options' ); ?>


            <h3>Section Images</h3>
            <div class="updated category-images-save-warning" style="display: none;">
                <p>You have unsaved changes! Why not <a href="#" class="save-settings-link"><strong>save settings now</strong></a>?</p>
            </div>




            <div class="theme-options-column">
                <table class="widefat">
                    <tr>
                        <th scope="col" class="preview-col">Preview</th>
                        <th scope="col" class="cat-col">Category</th>
                        <th scope="col" class="posts-col">Posts</th>
                        <th scope="col" class="desc-col">Description</th>
                    </tr>

                    <?php

                    eo_get_category_image_settings_row($options, 'home', 'Site Front Page', 'n/a', '');
                    eo_get_category_image_settings_row($options, 'catdefault', 'Category Default', 'n/a', 'alternate', 'divide-after');

                    $args = array(
                        'type'                     => 'post',
                        'child_of'                 => 0,
                        'parent'                   => '',
                        'orderby'                  => 'name',
                        'order'                    => 'ASC',
                        'hide_empty'               => 0,
                        'hierarchical'             => 1,
                        'exclude'                  => '',
                        'include'                  => '',
                        'number'                   => '',
                        'taxonomy'                 => 'category',
                        'pad_counts'               => false
                    );

                    $categories = get_categories($args);
                    foreach($categories as $key=>$cat) {
                        if(($key % 2) == 0) {
                            $zebra = "";
                        } else {
                            $zebra = "alternate";
                        }
                        eo_get_category_image_settings_row($options, $cat->term_id, $cat->name, $cat->category_count, $zebra);
                    }
                    ?>

                </table>
            </div>


            <p class="submit">
                <input id="primary-submit" type="submit" class="button-primary" value="<?php _e( 'Save Options', 'eotheme' ); ?>" />
            </p>
        </form>
    </div>
<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 * Should probably sanitize this...
 */
function theme_options_validate( $input ) {
    return $input;
}



/**
 * AJAX, Get the small preview image for the category image, to be shown
 *
 */
function eo_ajax_get_category_image_small_preview() {
    $results = eo_print_category_image_thumbnail($_POST['imageid']);
    die($results);
}
add_action( 'wp_ajax_ajax_get_category_image_small_preview', 'eo_ajax_get_category_image_small_preview' );



/**
 * AJAX, get an image in a specific size.
 *
 */
function eo_ajax_get_category_image_from_image_id() {
    $results = eo_get_image_in_specific_size_from_id($_POST['imageid'], $_POST['imagesize']);
    die($results);
}
add_action( 'wp_ajax_ajax_get_category_image_from_image_id', 'eo_ajax_get_category_image_from_image_id' );