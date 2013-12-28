# WP Emil Oberg

> This is very much a work in progress but feel free to download and play around. There are bugs and some thing are a bit rough around the edges. -[Emil](http://emiloberg.se)

A _very_ text focused Wordpress Theme. This is for sites where the text (and its images) is at absolute focus. There are no sidebars, no widget areas. There are however, good typography and big bold images.

* Posts can have big intro images (Featured image).
* All images are responsive and only the smallest, file size-wise, image is served to the browser (to make sure mobile browsing is fast!)
* It is responsive (mobile first).
* It is kind of focused around categories. Each category can have a category image and description.


## Install instructions
1. [Download the theme](https://github.com/emiloberg/wp-emiloberg/archive/master.zip) (Master branch ZIP)
2. Install and activate the theme by going to _Apperance_ > _Themes_ > _Add New_ > _Upload_
3. If you're going to embed material (such as YouTube videos, Flickr images etc), it's highly recomended to install the plugin [Iframely](http://wordpress.org/plugins/iframely/).
4. You'll find theme options under _Apperance_ > _EO Theme Options_. At the moment, the only theme options you find is settings for setting category images (se below)

## Category Images
This theme is focused around categories. To set images for categories (and the front page), go to _Apperance_ > _EO Theme Options_. 

## Images
_(This might be broken at the moment)_

When you upload an image to the Wordpress Media Library, the theme will figure out what the dominant colors in that image are. You then get to pick 1 color from those dominant colors and that color will be on small decors around the image (such as in the heart sign if the image has image credits or as a divider next to the image caption)

If you open the Image Library, you'll find a couple of new fields:

* __Color Palette__ - A string of de most dominant colors. This is calculated automagically, don't change this.

* __Dom. Color__ - The selected dominant color. Click to open a color selector. You'll find all the calculated colors as presets in this selector.

* __Crop Top__ - When the image is used as an _featured image_ should it be cropped? Takes a value 0-50 (which is percentage). E.g. `20` means it should crop 20 % of the image height from the top of the image. Rounded to nearest 5%. Default: 5 %.

* __Crop Bottom__ - Same as _Crop Top_, but how much should we crop from the bottom? Default: 20 %.

* __Start Article At__ - The actuall article overlays the _featured image_ a bit, here we can set the overlay. Takes a value 0-50 (which is percentage). E.g. `30` means the that the article should overlay the 30 % of the bottom of the image. Rounded to nearest 5%. Default: 30 %.

* __Credits__, image attribution. Will be displayed if the image is set as a _featured image_. Remember to give credit where credit is due!

* __Source URL__, address to the image, will make _Credits_ (above) into a link if image is set as a _featured image_.




## Shortcodes
##### \[big\]
Used around an image or other embedds (and optional caption) to make the image/embed bigger (max 1000px) than the standard column (max 600px).

Usually used after inserting an image from Wordpress Media Library.

Basic example:

    [big]
    <img src="http://example.com/wp-content/uploads/2013/01/sample-680x453.jpg" />
    [/big]

Embedded example (with the plugin [Iframely](http://wordpress.org/plugins/iframely/)):

	[big]
	[embed]http://www.flickr.com/photos/bigfez/5012423128/[/embed]
	[/big]
	
Example with caption 1

    [big]
    <img src="http://example.com/wp-content/uploads/2013/01/sample-680x453.jpg" />
    This is my Image Caption. I can also use HTML here, such as <a href="#">links</a>.
    [/big]


Example with caption 2. It does not matter in what order you input the caption and the image. The theme is smart enough to figure it out itself.

    [big]
	This is my Image Caption. I can also use HTML here, such as <a href="#">links</a>.    
    <img src="http://example.com/wp-content/uploads/2013/01/sample-680x453.jpg" />
    [/big]

#### \[e2e\]
Display an image/embed with gray background stretching _"edge to edge"_.
Usage is quite similar to _\[big\]_

Basic example:

    [e2e]
    <img src="http://example.com/wp-content/uploads/2013/01/sample-680x453.jpg" />
    [/e2e]

Example with caption
    
    [e2e]
    <img src="http://example.com/wp-content/uploads/2013/01/sample-680x453.jpg" />
    This is my Image Caption. I can also use HTML here, such as <a href="#">links</a>.
    [/e2e]
    
#### \[caption\]
Not realy done yet, but try it out if you want to.

## Based on
[Bones](http://themble.com/bones) by Eddie Machado

### Special Thanks to:
* Paul Irish & the HTML5 Boilerplate
* Yoast for some WP functions & optimization ideas
* Andrew Rogers for code optimization
* David Dellanave for speed & code optimization
* and several other developers.

## License
__[WTFPL](http://sam.zoy.org/wtfpl/)__
	
However, some libraries used may be under another licence!

* [Magnific Popup](http://dimsemenov.com/plugins/magnific-popup/) (MIT Licence)
* jQuery, MIT Licenced
* Modernizr, MIT Licenced
* Magnific Popup by Dmitry Semenov and contributors, MIT Licenced
* borderMenu by Mary Lou (Codrops), MIT Licenced