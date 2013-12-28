<?php

/************* INCLUDE NEEDED FILES ***************/

require_once ( 'theme-options.php' );

require_once( 'library/bones.php' ); // if you remove this, bones will break
require_once( 'library/custom-post-type.php' ); // you can disable this if you like

require_once( 'library/php/simple_html_dom.php' );
/*
3. library/admin.php
	- removing some default WordPress dashboard widgets
	- an example custom dashboard widget
	- adding custom login css
	- changing text in footer of admin
*/
// require_once( 'library/admin.php' ); // this comes turned off by default



/************* ADD ADMIN JS/CSS *************/
function eo_add_admin_js_css() {


    //Add support for Media Library for picking Category Image
    wp_enqueue_media();
    wp_enqueue_script( 'custom-header' );

    //Add admin style
    wp_enqueue_style('eo-admin-style', get_stylesheet_directory_uri() . '/library/css/admin-style.css');

    //Add javascript to admin panel to be able to use colorselector when adding/editing posts
    //Enqueue WP built in color-picker
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style( 'wp-color-picker' );

    //Add magnific-popup
    wp_enqueue_script('magnific-popup', get_stylesheet_directory_uri().'/library/js/libs/jquery.magnific-popup.js', "", "", true);

    //Add admin JS
    //Enqueue our own js to enable color picker at the post page.
    //Do this last to ensure other Javascripts is loaded
    wp_enqueue_script('eo-admin-js', get_stylesheet_directory_uri().'/library/js/admin-scripts-ck.js', "", "", true);

}
add_action('admin_enqueue_scripts', 'eo_add_admin_js_css');

/************* INCLUDE COMMON JAVASCRIPT *************/
//
add_action( 'wp_enqueue_scripts', 'eo_load_javascript_files' );
 
function eo_load_javascript_files() {
  wp_enqueue_script('magnific-popup', get_stylesheet_directory_uri().'/library/js/libs/jquery.magnific-popup.js', "", "", true );

  //Menu styles and scripts
	  //On top
//	  wp_enqueue_script('modernizr', get_stylesheet_directory_uri().'/library/js/libs/modernizr.custom.js', "", "", false );
	
	  //In footer
	  wp_enqueue_script('bordermenu-classie', get_stylesheet_directory_uri().'/library/js/libs/classie.js', "", "", true );
	  wp_enqueue_script('bordermenu-bordermenu', get_stylesheet_directory_uri().'/library/js/libs/borderMenu.js', "", "", true );  
  
}



/************* MAKE SURE SHORTCODES ARE OUTSIDE P TAG *************/
add_filter('the_content', 'eo_move_shortcodes_outside_p_tag');
function eo_move_shortcodes_outside_p_tag($content) {
	if( is_singular() && is_main_query() ) {

        /**
         * Make sure the shortcodes are NOT enclosed in <p>-tags
         *
         */

        //Which shortcodes to look for? delimiter with pipe, e.g
        //'shortcode1|code2|code3'
        $shortcodesToClean = "caption|big|e2e";

        //Replace all whitespace before and after shortcode with two line breaks,
        //to make sure it's all uniform, no matter what amount of whitespace the user inputed
        $content = preg_replace('/\s*\[(' . $shortcodesToClean . ')\]/i', "\n\n[$1]", $content);
        $content = preg_replace('/\[\/(' . $shortcodesToClean . ')\]\s*/i', "[/$1]\n\n", $content);

        //Run WP:s own line-break-to-p-converter
        $content = wpautop($content);

        //Now, we'll have our shortcodes enclosed with <p>, remove those <p>-tags.
        $content = preg_replace('/<p>\[(' . $shortcodesToClean . ')\]/i', '[$1]', $content);
        $content = preg_replace('/\[\/(' . $shortcodesToClean . ')\]<\/p>/i', '[/$1]', $content);




	}	
	return $content;
}



/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size('image-inline', 680, 9999);
add_image_size('image-2000', 2000, 9999);
add_image_size('image-1200', 1200, 9999);
add_image_size('image-1000', 1000, 9999);
add_image_size('image-800', 800, 9999);
add_image_size('image-500', 500, 9999);
add_image_size('image-50x37', 50, 37);

//Remove standard thumbnail image sizes
function eo_remove_default_image_sizes($sizes) {
    unset($sizes['thumbnail']);
    unset($sizes['medium']);
    unset($sizes['large']);
    return $sizes;
}
add_filter('image_size_names_choose', 'eo_remove_default_image_sizes');

//Add custom image sizes to Media Library
function eo_add_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'image-inline' => __('Inline image'),
    ) );
}
add_filter( 'image_size_names_choose', 'eo_add_custom_image_sizes' );










/************* IMAGES ********************/
/**
 * Remove WP's default insertion of [caption] shortcode 
 * when including an image to a post
 */
add_filter( 'disable_captions', create_function('$a', 'return true;') );


/**
 * Get Image ID from it's URL
 */
function eo_get_image_id_by_link($link)
{
    global $wpdb;
    $link = preg_replace('/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $link);
    return $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE BINARY guid='$link'");
}

/*
 * Get color palette (most used colors) from image.
 */
function eo_colorPalette($imageFile, $numColors, $granularity = 5) 
{ 
   $granularity = max(1, abs((int)$granularity)); 
   $colors = array(); 
   $size = @getimagesize($imageFile); 
   if($size === false) 
   { 
      logme("Unable to get image size data"); 
      return false; 
   } 

   $img = @imagecreatefromstring(file_get_contents($imageFile));

   if(!$img) 
   { 
      logme("Unable to open image file"); 
      return false; 
   } 
   for($x = 0; $x < $size[0]; $x += $granularity) 
   { 
      for($y = 0; $y < $size[1]; $y += $granularity) 
      { 
         $thisColor = imagecolorat($img, $x, $y); 
         $rgb = imagecolorsforindex($img, $thisColor); 
         $red = round(round(($rgb['red'] / 0x33)) * 0x33); 
         $green = round(round(($rgb['green'] / 0x33)) * 0x33); 
         $blue = round(round(($rgb['blue'] / 0x33)) * 0x33); 
         $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue); 
         if(array_key_exists($thisRGB, $colors)) 
         { 
            $colors[$thisRGB]++; 
         } 
         else 
         { 
            $colors[$thisRGB] = 1; 
         } 
      } 
   } 
   arsort($colors); 
   return array_slice(array_keys($colors), 0, $numColors); 
} 


/**
 * When image is uploaded, process it and get the color palette (most used colors) 
 * and then update the image's meta data fields with those colors, to be able to 
 * use in the theme when showing the image.
 */
add_action('add_attachment', 'eo_process_images');
add_action('edit_attachment', 'eo_process_images');

function eo_process_images($results) {
	//Get url to uploaded file
	$imageMeta = wp_get_attachment_metadata($results);
	$imageFile = $imageMeta['file'];
	$uploadDir = wp_upload_dir();
	$uploadDir = $uploadDir['baseurl'];
	$imageFile = $uploadDir . '/' . $imageFile;
	
	//Get color palette 
	$palette = eo_colorPalette($imageFile, 8, 50); 
	$imageColors = implode(",", $palette);
	
	//Insert palette
	update_post_meta($results, 'img_color_palette', $imageColors);
	
	//Insert dominant color	if one is not already set.
	//If it's already set, it could be changed by the user.
	$imageCustomFields = get_post_custom($results);
	$imageCurrentDominatingColor = $imageCustomFields['img_dominant_color'][0];
	if(strlen($imageCurrentDominatingColor) < 1) {
		update_post_meta($results, 'img_dominant_color', '#' . $palette[0]);		
	}
	
	
}

/**
 * Add javascript to admin panel to be able to use colorselector when adding/editing posts
 */
/*add_action( 'admin_enqueue_scripts', 'eo_add_admin_scripts', 10, 1 );

function eo_add_admin_scripts( $hook ) {
	if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
    
    	//Enqueue WP built in color-picker
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_style( 'wp-color-picker' );

		//Enqueue our own js to enable color picker at the post page.
        wp_enqueue_script('enable-color-selector', get_stylesheet_directory_uri().'/library/js/libs/eo.post.enable-color-selector.js' );
	}
}*/


/**
 * When uploading a picture, a user can define how s/he wants that image to be cropped
 * when used as a lead image. This function validates that the number is e.g. "20%" or "10"
 * and returns a class to be added to the HTML (which then the CSS can pick up and
 * style accordingly).
 *
 */
function eo_get_class_from_leadimg_crop($crop) {
    preg_match('/(\d+)\%?/', $crop, $matches);
    if(!empty($matches)) {
        $crop = roundToAny($matches[1], 5); //Round to closes 5.

        //Can only crop 0-50%.
        if($crop < 0 || $crop > 50) {
            $crop = "default";
        }

    } else {
        $crop = "default";
    }

    return $crop;
}

function roundToAny($n,$x) {
    return $x * round($n / $x);
}

/**
 * Add 'Source' and 'Credits' field to images
 * Also add fields for color palette to be able to use in theme,
 * populate those color palette fields with the dominant colors of the image.
 */

function eo_helper_set_default_values($str, $default) {
    if(empty($str)) { $str = $default; }
    return $str;
}

add_filter("attachment_fields_to_edit", "eo_add_image_attachment_fields", 10, 2);

function eo_add_image_attachment_fields($form_fields, $post) {

    //Lead image crop fields.

    $form_fields["leadimg_crop_top"] = array(
        "label" => __("Crop Top"),
        "input" => "text",
        "value" => get_post_meta($post->ID, "leadimg_crop_top", true),
    );
    $form_fields["leadimg_crop_bottom"] = array(
        "label" => __("Crop Bottom"),
        "input" => "text",
        "value" => get_post_meta($post->ID, "leadimg_crop_bottom", true),
    );
    $form_fields["leadimg_start_article_at"] = array(
        "label" => __("Start Article At"),
        "input" => "text",
        "value" => get_post_meta($post->ID, "leadimg_start_article_at", true),
    );


	$form_fields["source_url"] = array(
		"label" => __("Source URL"),
		"input" => "text",
		"value" => get_post_meta($post->ID, "source_url", true)
	);
	$form_fields["credits"] = array(
		"label" => __("Credits"),
		"input" => "text",
		"value" => get_post_meta($post->ID, "credits", true)
	);
	$currentColorPalette = get_post_meta($post->ID, "img_color_palette", true);

	//Color palette will contain the most used X colors in the picture
	$form_fields["img_color_palette"] = array(
		"label" => __("Color Palette"),
		'input'      => 'html',
		'html'       => "<input type='text' class='text color-palette' id='attachments-$post->ID-img_color_palette' name='attachments[$post->ID][img_color_palette]' value='" . 
		$currentColorPalette . "' /><br />"
	);			
	
	//Create a string with javascript to populate the color picker with the most
	//dominant colors of the just uploaded picture.
	$currentColorPaletteArray = explode(",", $currentColorPalette);
    $colorPickerOptions = "";
	if (count($currentColorPaletteArray) > 0) {
		$selectableColors = implode("', '#", $currentColorPaletteArray);
		$selectableColors = "['#" . $selectableColors . "']";
		
		$colorPickerOptions = "<script>
		dominantColorPickerOptions = {
		    palettes: " . $selectableColors . "
		}
		</script>
		";
		
	}
	
	//Dominant color will contain either;
	//	By default: The most dominant color (hexcode) of the picture, or
	//	If edited by the user: A custom color (hexcode)
	$form_fields["img_dominant_color"] = array(
		"label" => __("Dom. Color"),
		'input'      => 'html',
		'html'       => $colorPickerOptions .
                        "<input type='text' class='text color-input' id='attachments-$post->ID-img_dominant_color' name='attachments[$post->ID][img_dominant_color]' value='" .
		get_post_meta($post->ID, "img_dominant_color", true) . "' /><br />"
		


	);			
	
 	return $form_fields;
}


/**
 * Make sure custom fields are saved back to database
 */
add_filter("attachment_fields_to_save", "save_image_source_url", 10 , 2);

function save_image_source_url($post, $attachment) {
    if (isset($attachment['leadimg_crop_top'])) {
        update_post_meta($post['ID'], 'leadimg_crop_top', $attachment['leadimg_crop_top']);
    }
    if (isset($attachment['leadimg_crop_bottom'])) {
        update_post_meta($post['ID'], 'leadimg_crop_bottom', $attachment['leadimg_crop_bottom']);
    }
    if (isset($attachment['leadimg_start_article_at'])) {
        update_post_meta($post['ID'], 'leadimg_start_article_at', $attachment['leadimg_start_article_at']);
    }
	if (isset($attachment['source_url'])) {
		update_post_meta($post['ID'], 'source_url', esc_url($attachment['source_url']));
	}
	if (isset($attachment['credits'])) {
		update_post_meta($post['ID'], 'credits', $attachment['credits']);
	}	
	if (isset($attachment['img_dominant_color'])) {
		update_post_meta($post['ID'], 'img_dominant_color', $attachment['img_dominant_color']);
	}	

	return $post;
}


/**
 * If we're on the start page or on a category pages, insert <style> in <head> to
 * show the category image
 *
 */
function eo_print_category_image_positions() {
    if(is_home() || is_category()) {

        $options = get_option('eo_theme_options');
        $catID = "";
        $catImagePosSidebar = "50%";
        $catImagePosTop = "50%";

        if(is_home()) {
            $catID = 'home';
        } elseif(is_category()) {
            $catID = eo_get_current_cat_id();
        }

        $catImageId = $options['catimage-' . $catID];
        $catImagePosition    = eo_get_category_image_background_positions($options['catimage-position-' . $catID]);

        //If a category image is not set, check to see if a default is set
        if(empty($catImageId)) {
            $catImageId = $options['catimage-catdefault'];
            $catImagePosition    = eo_get_category_image_background_positions($options['catimage-position-catdefault']);
        }



        if(!empty($catImageId)) {
            $image500 = eo_get_image_in_specific_size_from_id($catImageId, 'image-500');
            $image800 = eo_get_image_in_specific_size_from_id($catImageId, 'image-800');
            $image1000 = eo_get_image_in_specific_size_from_id($catImageId, 'image-1000');
            $image1200 = eo_get_image_in_specific_size_from_id($catImageId, 'image-1200');
            $image2000 = eo_get_image_in_specific_size_from_id($catImageId, 'image-2000');

            $catImagePosSidebar  = $catImagePosition['sidebar'];
            $catImagePosTop      = $catImagePosition['top'];

            $outputCSS = <<<EOT
                <style type="text/css">
                .section-image { background-image: url('$image500'); background-position: 50% $catImagePosTop; }
                @media only screen and (min-width: 500px)  { .section-image { background-image: url('$image800'); } }
                @media only screen and (min-width: 800px)  { .section-image { background-image: url('$image1000'); background-position: $catImagePosSidebar 50%; } }
                @media only screen and (min-width: 1000px) { .section-image { background-image: url('$image1200'); } }
                @media only screen and (min-width: 1200px) { .section-image { background-image: url('$image2000'); } }
                </style>
EOT;

            echo $outputCSS;
        }
    }
}


add_action( 'wp_head', 'eo_print_category_image_positions' );





/************* SHORTCODES ********************/
add_action( 'init', 'eo_register_shortcodes');

function eo_register_shortcodes(){
   add_shortcode('e2e', 'eo_shortcode_embed_bleed');
   add_shortcode('big', 'eo_shortcode_embed_big');
   add_shortcode('caption', 'eo_shortcode_embed_caption');  
}

function eo_shortcode_embed_big($atts, $content = null) {
	return eo_build_embed($content, false, "bulge");
}

function eo_shortcode_embed_bleed($atts, $content = null) {
	return eo_build_embed($content, true, "edge-to-edge");
}

function eo_shortcode_embed_caption($atts, $content = null) {
	return eo_build_embed($content, false, "edge-to-edge");
}

function eo_build_embed($content = null, $bleed = true, $displayType) {
	$isLocalImage 		= false;
	$isExternalContent	= false;

    $contentCaption      = "";
    $figcaptionAside     = "";
    $figcaptionStandard  = "";
    $captionClass        = "";
    $imageCreditsClasses = "";
    $iframe              = "";
    $output              = "";

	if(true == $bleed) {
		$bleedClass = "bleed";
	} else {
		$bleedClass = "";
	}

	//Find out what content the shortcode has. Is it an image or an iframe?
	$html = new simple_html_dom();
	$html->load($content);
	
	//Is it an image?
	$ret = $html->find('img');

	foreach($ret as $tag) {
    	$originalImgUrl = $tag->src;
    	$isLocalImage = true;

    	//If it is an image, is it a local image (which we could later on get in different file sizes
    	//Or is it an external (embeded image)?
    	//Check by seeing if the image has an ID
    	$imageId = eo_get_image_id_by_link($originalImgUrl);
    	if(!is_numeric($imageId) ) {
			$iframe = $tag->parent();
			$contentCaption = str_replace($iframe, '', $content);
			$isExternalContent = true;
			$isLocalImage = false;
    	}

        $contentCaption = preg_replace('/<(\s*)img[^<>]*>/i', '', $content);

    	break;
    }

    
    //Is it an iframely embedd?
	$ret = $html->find('div.iframely-outer-container');
	foreach($ret as $tag) {
    	$iframe = $tag->outertext;
		$contentCaption = str_replace($iframe, '', $content);
    	$isExternalContent = true;
    	break;
    }
	$ret = $html->find('div.iframely-widget-container');
	foreach($ret as $tag) {
    	$iframe = $tag->outertext;
		$contentCaption = str_replace($iframe, '', $content);
    	$isExternalContent = true;
    	break;
    }        
    
    //Clean the possible caption.
    $contentCaption = str_replace('<p>', '', $contentCaption);
    $contentCaption = str_replace('</p>', '', $contentCaption);
	$contentCaption = str_replace("&nbsp;", "", $contentCaption);
	$contentCaption = trim($contentCaption);

    
    if($isLocalImage) { 
	    //Based on the src url of the embedded image, find the image id
		$imageId = eo_get_image_id_by_link($originalImgUrl);
		
		//Based on the image ID, get the sources for the images
		$imageSrcSizeFull = wp_get_attachment_image_src($imageId, 'full');
		$imageSrcSizeFull = $imageSrcSizeFull[0];
		
		$imageSrcSize680 = wp_get_attachment_image_src($imageId, 'image-inline');
		$imageSrcSize680 = $imageSrcSize680[0];

		$imageSrcSize1000 = wp_get_attachment_image_src($imageId, 'image-1000');
		$imageSrcSize1000 = $imageSrcSize1000[0];

		$imageSrcSize800 = wp_get_attachment_image_src($imageId, 'image-800');
		$imageSrcSize800 = $imageSrcSize800[0];

		$imageSrcSize500 = wp_get_attachment_image_src($imageId, 'image-500');
		$imageSrcSize500 = $imageSrcSize500[0];		
		
		//Based on the image ID, get the image and then get the HEX codes of the colors in that image. 
		//Also get get credits, source url, and alt-text
		$imageCustomFields = get_post_custom($imageId);
		$imageDominantColor = $imageCustomFields['img_dominant_color'][0];
		$imageCredits = $imageCustomFields['credits'][0];
		$imageSourceUrl = $imageCustomFields['source_url'][0];
		$imageAltText = $imageCustomFields['_wp_attachment_image_alt'][0];
	
		
		
		//###
		//TODO: REMOVE A HREF ASWELL! AND CHANGE SO THAT IMAGES ARE NOT LINKDE BY DEFAULT IN WP.
		
		//<a(.*?)href="(.*?)"(.*?)>
		

		//Create credits/source link
		if(strlen($imageSourceUrl) > 0 ) {
			if(strlen($imageCredits) > 0 ) {
				$imageCredits = '<a href="' . $imageSourceUrl . '">' . $imageCredits . '</a>';
			} else {
				$imageCredits = $imageSourceUrl;
			}
		}
		
		
		//Merge caption and credits
		if(strlen($imageCredits) > 0 ) {
			if(strlen($contentCaption) > 0 ) { //If image has caption, add a class we can add to the credits div
				$imageCreditsClasses = 'has-credits';
			}
					
		
			$imageCredits = '<div class="credits ' . $imageCreditsClasses . '">' .
							'<span class="heart" style="color: ' . $imageDominantColor . '">&hearts;</span> ' . 
								$imageCredits . 
							'</div>';
			
			$contentCaption .= $imageCredits;
			
		}
		
		//Does it have a caption, if so, add a class
		if(strlen($contentCaption) > 0) {
			$captionClass = "has-caption";
		}		

		//Enclose in <figcaption> with the right color border, extracted from the image
		if(strlen($contentCaption) > 0 ) {
			$figcaptionAside = '<figcaption class="image caption aside" style="border-color: ' . 
								$imageDominantColor . 							
								'">' . 
								$contentCaption . 
								'</figcaption>';
								
			$figcaptionStandard = '<figcaption class="image caption standard" style="border-color: ' . 
								$imageDominantColor . 							
								'">' . 
								$contentCaption . 
								'</figcaption>';	
		}
	
		//Depending on which display type this is; 
		//Enclose in 'edge-to-edge' or 'bulge' div or and include original image.
		if($displayType == "edge-to-edge") {
			$output = 	'<div class="image edge-to-edge">' . 
	   						'<figure class="' . $captionClass . '">' . 
		   						$figcaptionAside . 
		   						'<div class="' . $bleedClass . '">' . 
			   						'<a href="' . $imageSrcSizeFull . '" class="image-popup">' .			   						
										'<span class="picturefill" data-picture data-alt="' . $imageAltText . '">' . 
										'	<span data-src="' . $imageSrcSize500 . '"></span>' . 
										'	<span data-src="' . $imageSrcSize680 . '"     data-media="(min-width: 500px)"></span>' . 
										'	<span data-src="' . $imageSrcSize800 . '"     data-media="(min-width: 680px)"></span>' . 
										'	<noscript>' . 
										'		<img src="' . $imageSrcSize680 . '" alt="' . $imageAltText . '">' . 
										'	</noscript>' . 
										'</span>' .	   						
				   					'</a>' .
				   				'</div>' . 
				   				$figcaptionStandard . 
				   			'</figure>' . 
				   		'</div>';
		} elseif ($displayType == "bulge") {
			$output = 	'<div class="image bulge">' . 
	   						'<figure class="' . $captionClass . '">' . 
		   						'<div>' . 
			   						'<a href="' . $imageSrcSizeFull . '" class="image-popup">' .
										'<span class="picturefill" data-picture data-alt="' . $imageAltText . '">' . 
										'	<span data-src="' . $imageSrcSize500 . '"></span>' . 
										'	<span data-src="' . $imageSrcSize680 . '"     data-media="(min-width: 500px)"></span>' . 
										'	<span data-src="' . $imageSrcSize800 . '"     data-media="(min-width: 680px)"></span>' . 
										'	<span data-src="' . $imageSrcSize1000 . '"     data-media="(min-width: 800px)"></span>' . 
										'	<noscript>' . 
										'		<img src="' . $imageSrcSize680 . '" alt="' . $imageAltText . '">' . 
										'	</noscript>' . 
										'</span>' .
				   					'</a>' .
				   				'</div>' . 
				   				$figcaptionStandard . 
				   			'</figure>' . 
				   		'</div>';			
		}
	   						
	} elseif($isExternalContent) {
		
		//Enclose in <figcaption>
		if(strlen($contentCaption) > 0) {

            if($displayType == "edge-to-edge") {
                $figcaptionAside = '<figcaption class="embed caption aside">' .
                                    $contentCaption .
                                    '</figcaption>';
            } else {
                $figcaptionAside = "";
            }
								
			$figcaptionStandard = '<figcaption class="embed caption standard">' . 
								$contentCaption . 
								'</figcaption>';	
		}	
	
		$output =	'<div class="embed ' . $displayType . '">' . 
	   					'<figure class="' . $captionClass . '">' .
	   					$figcaptionAside .
	   					'<div class="embed ' . $bleedClass . '">' .
							$iframe . 
		   				'</div>' .
		   				$figcaptionStandard . 
	   					'</figure>' . 		   							
	   				'</div>';
	   					
	} else {
		
		$output =	'<div class="' . $displayType . '">' . 
	   					'<div class="' . $bleedClass . '">' . 	
							$content . 
		   				'</div>' .
	   				'</div>';		
		
	}
   
   return $output;
   

}


/*
	extract(shortcode_atts( array(
		'caption' => '',
	), $atts, 'e2e' ) );
*/

/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function bones_register_sidebars() {
/*
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'bonestheme' ),
		'description' => __( 'The first (primary) sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));
*/

	/*
	to add more sidebars or widgetized areas, just copy
	and edit the above sidebar code. In order to call
	your new sidebar just use the following code:

	Just change the name to whatever your new
	sidebar's id is, for example:

	register_sidebar(array(
		'id' => 'sidebar2',
		'name' => __( 'Sidebar 2', 'bonestheme' ),
		'description' => __( 'The second (secondary) sidebar.', 'bonestheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	To call the sidebar in your template, you can just copy
	the sidebar.php file and rename it to your sidebar's name.
	So using the above example, it would be:
	sidebar-sidebar2.php

	*/
} // don't remove this bracket!

/************* COLOR FUNCTIONS *****************/
/**
 * Takes a post ID, finds the lead image (if any) and checks the custom field
 * and returns the dominant color.
 */
function eo_get_dominant_color_from_post_id($postId) {
    $output = "";
    $postThumbnailId = get_post_thumbnail_id($postId);
    if(is_numeric($postThumbnailId)) {
        $imageCustomFields = get_post_custom($postThumbnailId);
        $imageDominantColor = $imageCustomFields['img_dominant_color'][0];

        if(strlen($imageDominantColor) == 7 ) {
            $output = $imageDominantColor;
        }
    }

    return $output;
}

function eo_css_selector_and_value_to_inline_css($selector, $value) {
    $output = "";
    if(strlen($value) > 0) {
        $output = ' style="' . $selector. ': ' . $value. ';"';
    }
    return $output;
}


function eo_color_inverse($color){
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6){ return '000000'; }
    $rgb = '';
    for ($x=0;$x<3;$x++){
        $c = 255 - hexdec(substr($color,(2*$x),2));
        $c = ($c < 0) ? 0 : dechex($c);
        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
    }
    return '#'.$rgb;
}

/************* SEARCH FORM LAYOUT *****************/

// Search Form
/*
function bones_wpsearch($form) {
	$form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
	<label class="screen-reader-text" for="s">' . __( 'Search for:', 'bonestheme' ) . '</label>
	<input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . esc_attr__( 'Search the Site...', 'bonestheme' ) . '" />
	<input type="submit" id="searchsubmit" value="' . esc_attr__( 'Search' ) .'" />
	</form>';
	return $form;
} // don't remove this bracket!
*/


/************* Facebook Open Graph *****************/
// 
add_action('wp_head', 'add_fb_open_graph_tags');
function add_fb_open_graph_tags() {
	if (is_single()) {
		global $post;
		if(get_the_post_thumbnail($post->ID, 'thumbnail')) {
			$thumbnail_id = get_post_thumbnail_id($post->ID);
			$thumbnail_object = get_post($thumbnail_id);
			$image = $thumbnail_object->guid;
		} else {	
			$image = ''; // Change this to the URL of the logo you want beside your links shown on Facebook
		}
		//$description = get_bloginfo('description');
		$description = my_excerpt( $post->post_content, $post->post_excerpt );
		$description = strip_tags($description);
		$description = str_replace("\"", "'", $description);
?>
<meta property="og:title" content="<?php the_title(); ?>" />
<meta property="og:type" content="article" />
<meta property="og:image" content="<?php echo $image; ?>" />
<meta property="og:url" content="<?php the_permalink(); ?>" />
<meta property="og:description" content="<?php echo $description ?>" />
<meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>" />

<?php 	}
}

function my_excerpt($text, $excerpt){
	
    if ($excerpt) return $excerpt;

    $text = strip_shortcodes( $text );

    $text = apply_filters('the_content', $text);
    $text = str_replace(']]>', ']]&gt;', $text);
    $text = strip_tags($text);
    $excerpt_length = apply_filters('excerpt_length', 55);
    $excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
    $words = preg_split("/[\n
	 ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
    if ( count($words) > $excerpt_length ) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
    } else {
            $text = implode(' ', $words);
    }

    return apply_filters('wp_trim_excerpt', $text);
}


/************* MENUES *************/
function eo_main_menu() {
    $menuArgs = array(
        'theme_location'  => 'main-nav',
        'menu'            => '',
        'container'       => false,
        'container_class' => '',
        'container_id'    => '',
        'menu_class'      => '',
        'menu_id'         => '',
        'echo'            => true,
        'fallback_cb'     => 'wp_page_menu',
        'before'          => '',
        'after'           => '',
        'link_before'     => '',
        'link_after'      => '',
        'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
        'depth'           => -1,
        'walker'          => ''
    );
    wp_nav_menu($menuArgs);
}

/************* REMOVE ID/CLASS FROM MENU *************/
function eo_remove_css_attributes($var) {
    return is_array($var) ? array() : '';
}
add_filter('nav_menu_css_class', 'eo_remove_css_attributes', 100, 1);
add_filter('nav_menu_item_id', 'eo_remove_css_attributes', 100, 1);

/************* MOVE ADMIN BAR TO BOTTOM *************/
function eo_DEBUG_move_admin_bar() {
    if ( is_user_logged_in() ) {
        echo '
        <style type="text/css">
        body {
        margin-top: -28px;
        padding-bottom: 28px;
        }
        body.admin-bar #wphead {
           padding-top: 0;
        }
        body.admin-bar #footer {
           padding-bottom: 28px;
        }
        #wpadminbar {
            top: auto !important;
            bottom: 0;
        }
        #wpadminbar .quicklinks .menupop ul {
            bottom: 28px;
        }
        </style>';
    }
}

add_action( 'wp_head', 'eo_DEBUG_move_admin_bar' );

/************* CATEGORY IMAGE FRONT END FUNCTIONS *****************/

/**
 * Takes the value from $options['catimage-position-<category-id>']; and make sure
 * that the values valid (and if not return default '50') and add percentage sign
 * to output a string such as '50% 50%' to be used in 'background-position: 50% 50%'
 *
 */
function eo_get_category_image_background_position_from_theme_settings($catImagePosition) {
    preg_match_all( '/(\d{1,3})\ (\d{1,3})/', $catImagePosition, $catImagePositions );
    $catImagePosition = isNumericMinMaxDefault($catImagePositions[1][0], 0, 100, 50) . '% ' .
                        isNumericMinMaxDefault($catImagePositions[2][0], 0, 100, 50) . '%';
    return $catImagePosition;
}

function eo_get_category_image_background_positions($catImagePosition) {
    preg_match_all( '/(\d{1,3})\ (\d{1,3})/', $catImagePosition, $catImagePositions );

    return array(
        'sidebar' => isNumericMinMaxDefault($catImagePositions[1][0], 0, 100, 50) . '%',
        'top' => isNumericMinMaxDefault($catImagePositions[2][0], 0, 100, 50) . '%'
    );

}

/************* SINGlE POST IMAGE *****************/

function eo_print_single_post_main_image($postId, $leadImageMeta, $sectionId, $sectionClass = '', $printCredits = true) {
    if($printCredits === true && strlen($leadImageMeta['credits'][0]) > 0) {
        ?>
        <div class="leadimage-credits">&hearts;
            <? if(strlen($leadImageMeta['source_url'][0]) > 0) { ?>
                <a href="<?php print $leadImageMeta['source_url'][0] ?>" target="_blank">
                    <?php print $leadImageMeta['credits'][0] ?>
                </a>
            <? } else { ?>
                <?php print $leadImageMeta['credits'][0] ?>
            <? } ?>
        </div>
    <?php } ?>

    <section id="<?php print $sectionId ?>" class="no-print <?php print $sectionClass ?>">
        <div class="container">
            <figure class="bottom-crop-<?php
            print eo_get_class_from_leadimg_crop($leadImageMeta['leadimg_crop_bottom'][0]);
            print ' top-crop-';
            print eo_get_class_from_leadimg_crop($leadImageMeta['leadimg_crop_top'][0]);
            ?>">
                <?php
                //TODO Om användaren får redigera artikeln, lägg till funktioner för att snabbt kunna
                //TODO ändra på croppingen så att bilden ser snygg ut

                $leadImg2000 = wp_get_attachment_image_src(get_post_thumbnail_id($postId), 'image-2000');
                $leadImg1200 = wp_get_attachment_image_src(get_post_thumbnail_id($postId), 'image-1200');
                $leadImg1000 = wp_get_attachment_image_src(get_post_thumbnail_id($postId), 'image-1000');
                $leadImg800 = wp_get_attachment_image_src(get_post_thumbnail_id($postId), 'image-800');
                $leadImg500 = wp_get_attachment_image_src(get_post_thumbnail_id($postId), 'image-500');
                $LeadAltText = get_post_meta(get_post_thumbnail_id($postId), '_wp_attachment_image_alt', true);
                ?>

                <span data-picture data-alt="<?php print $LeadAltText; ?>">
                    <span data-src="<?php print $leadImg500['0']  ?>"></span>
                    <span data-src="<?php print $leadImg800['0']  ?>" data-media="(min-width: 500px)"></span>
                    <span data-src="<?php print $leadImg1000['0'] ?>" data-media="(min-width: 800px)"></span>
                    <span data-src="<?php print $leadImg1200['0'] ?>" data-media="(min-width: 1000px)"></span>
                    <span data-src="<?php print $leadImg2000['0'] ?>" data-media="(min-width: 1200px)"></span>

                    <noscript>
                        <img src="<?php print $leadImg800['0'] ?>"
                             alt="<?php print $LeadAltText; ?>">
                    </noscript>
                </span>

            </figure>
        </div>
    </section>
<?php
}


/************* HELPER FUNCTIONS *****************/



/**
 * Get the current category ID.
 *
 */
function eo_get_current_cat_id(){
    global $wp_query;
    $catID = 0;
    if(is_category() || is_single()){
        $catID = get_query_var('cat');
    }
    return $catID;
}

/**
 * Print categories
 *
 */
function eoPrintCategories($categories, $container = 'span') {
    $output = '';
    if($categories){
        foreach($categories as $category) {
            $output .= '<a href="'.get_category_link( $category->term_id ).'">'. replaceSpaceWithNbsp($category->cat_name).'</a> ';
        }
        $output = '<' . $container . ' class="tags-title tags">' . trim($output) . '</' . $container . '>';
    }
    return $output;
}

/**
 * Check a variable to see if it's a number within a min/max-span and if not
 * return a default value
 */
function isNumericMinMaxDefault($num, $min, $max, $default) {
    if(is_numeric($num)) {
        if($num < $min || $num > $max) {
            $output = $default;
        } else {
            $output = $num;
        }
    } else {
        $output = $default;
    }

    return $output;
}

/**
 * Replace blank space with nbsp
 *
 */
function replaceSpaceWithNbsp($str) {
    return str_replace(' ', '&nbsp;', $str);
}



/************* LOG FUNCTION FOR DEBUGGING *****************/
if (!function_exists('logme')) {
    function logme ( $log )  {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log('WPLOG: ' . print_r( $log, true ) );
            } else {
                error_log('WPLOG: ' .  $log );
            }
        }
    }
}


?>
