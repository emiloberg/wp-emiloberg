jQuery(document).ready(function($) {


    /*
     * Actions on scroll
     */
    var lastScroll = 0;
	$(window).scroll(function(){
        var currentScroll = $(window).scrollTop();


        //Set opacity on lead image
		var fadeOutAt = $('#leadimage-wide').height();
		fadeOutAt = fadeOutAt - (fadeOutAt / 3);
		var imgOpacity = (fadeOutAt - currentScroll) / fadeOutAt;
		if(currentScroll<fadeOutAt){
			$('#leadimage-wide').stop(true,true).fadeTo("fast", imgOpacity);
		} else {
			$('#leadimage-wide').stop(true,true).fadeTo("slow", imgOpacity);
		}

        //Fade out/in menu trigger button
        var fadeOutMenuTriggerAt = 300;
        if(currentScroll>fadeOutMenuTriggerAt){
            if(currentScroll < lastScroll) {
                $('.bt-menu-close #menu-trigger-bar').fadeIn();
            } else {
                $('.bt-menu-close #menu-trigger-bar').fadeOut("slow");
            }

        } else {
            $('.bt-menu-close #menu-trigger-bar').fadeIn();
        }
        lastScroll = currentScroll;
	});

	
	/**
	 * Initialize magnificPopup for article image popups
	 */
    $('.image-popup').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        closeBtnInside: false,
        mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
        image: {
            verticalFit: true
        },
        zoom: {
            enabled: true,
            duration: 250 // don't foget to change the duration also in CSS
        },
        titleSrc: 'title'
    });


}); /* end of as page load scripts */



/**
 * ======================================================== HELPER FUNCTIONS ========================================================
 *
 */


/*! A fix for the iOS orientationchange zoom bug.
 Script by @scottjehl, rebound by @wilto.
 MIT License.
*/
(function(w){
	// This fix addresses an iOS bug, so return early if the UA claims it's something else.
	if( !( /iPhone|iPad|iPod/.test( navigator.platform ) && navigator.userAgent.indexOf( "AppleWebKit" ) > -1 ) ){ return; }
    var doc = w.document;
    if( !doc.querySelector ){ return; }
    var meta = doc.querySelector( "meta[name=viewport]" ),
        initialContent = meta && meta.getAttribute( "content" ),
        disabledZoom = initialContent + ",maximum-scale=1",
        enabledZoom = initialContent + ",maximum-scale=10",
        enabled = true,
		x, y, z, aig;
    if( !meta ){ return; }
    function restoreZoom(){
        meta.setAttribute( "content", enabledZoom );
        enabled = true; }
    function disableZoom(){
        meta.setAttribute( "content", disabledZoom );
        enabled = false; }
    function checkTilt( e ){
		aig = e.accelerationIncludingGravity;
		x = Math.abs( aig.x );
		y = Math.abs( aig.y );
		z = Math.abs( aig.z );
		// If portrait orientation and in one of the danger zones
        if( !w.orientation && ( x > 7 || ( ( z > 6 && y < 8 || z < 8 && y > 6 ) && x > 5 ) ) ){
			if( enabled ){ disableZoom(); } }
		else if( !enabled ){ restoreZoom(); } }
	w.addEventListener( "orientationchange", restoreZoom, false );
	w.addEventListener( "devicemotion", checkTilt, false );
})( this );