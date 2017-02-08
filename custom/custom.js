jQuery(document).ready(function($){
    // Custom jQuery goes here
    $("img").removeAttr("title");
    $("div.up-sells.upsells.products > h2").html("You may also like");
    
    // Fix "remove product category" bug of stationery theme
    if ( ! $( ".storefront-product-section.storefront-product-categories" ).length ) {
        $( '.storefront-product-section' ).first().before(
            '<div class="storefront-product-section storefront-product-categories" style="display:none"></div>'
        );
    }
});

