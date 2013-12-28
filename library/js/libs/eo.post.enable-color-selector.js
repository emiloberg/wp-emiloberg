// Documentation for wpColorPicker:
// http://automattic.github.io/Iris/
jQuery(document).on('focus', '.color-input', function(){
	jQuery('.color-input').wpColorPicker(dominantColorPickerOptions);
} );


/*
var myOptions = {
    // you can declare a default color here,
    // or in the data-default-color attribute on the input
    defaultColor: false,
    // a callback to fire whenever the color changes to a valid color
    change: function(event, ui){},
    // a callback to fire when the input is emptied or an invalid color
    clear: function() {},
    // hide the color picker controls on load
    hide: true,
    // show a group of common colors beneath the square
    // or, supply an array of colors to customize further
    palettes: true
};
 
$('.my-color-field').wpColorPicker(myOptions);







options = {
    color: false,
    mode: 'hsl',
    controls: {
        horiz: 's', // horizontal defaults to saturation
        vert: 'l', // vertical defaults to lightness
        strip: 'h' // right strip defaults to hue
    },
    hide: true, // hide the color picker by default
    border: true, // draw a border around the collection of UI elements
    target: false, // a DOM element / jQuery selector that the element will be appended within. Only used when called on an input.
    width: 200, // the width of the collection of UI elements
    palettes: false // show a palette of basic colors beneath the square.
}


$('#palette-example2').iris({
    hide: false,
    palettes: ['#125', '#459', '#78b', '#ab0', '#de3', '#f0f']
});


*/