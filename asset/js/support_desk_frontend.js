jQuery(document).ready(function(e){
    jQuery(document).on('click', 'div#history a', function(e){
        e.preventDefault();
        window.open( jQuery(this).attr('href'), "_blank"); 
    });
}); // End Document Ready function 