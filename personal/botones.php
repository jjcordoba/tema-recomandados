<?php
add_action('woocommerce_before_quantity_input_field', 'btn_before_input_qty_field');
function btn_before_input_qty_field(){
    echo '<button type="button" class="button button-qty" data-quantity="minus">-</button>';
}

add_action('woocommerce_after_quantity_input_field', 'btn_after_input_qty_field');
function btn_after_input_qty_field(){
    echo '<button type="button" class="button button-qty" data-quantity="plus">+</button>';
}

// Función para agregar el código JavaScript en WordPress
function add_custom_js() {
?>
    <script>
        (function( $ ) {
            'use strict';

            // pa_color = es el ID del atributo de selección que vamos a cambiar
            create_options_buttons('pa_color');

            function create_options_buttons( el ){
                $('#btns-' + el).remove();
                $('<div id="btns-' + el + '" class="recomand-btns"></div>').insertAfter('#' + el);
                $('#' + el).hide();
                $('#' + el + ' option').each(function(i,e){
                    $('<input type="radio" />')
                            .attr('value', $(this).val())
                            .attr('name', 'r' + el )
                            .attr('id', 'r' + el + i)
                            .attr('checked', $(this).is(':selected'))
                            .click( function() {
                                $('#' + el).val($(this).val()).trigger('change');
                            })
                        .add($('<label for="'+ 'r' + el + i +'">'+ this.text +'</label>'))
                        .appendTo('#btns-' + el);
                });
            }

            $('.reset_variations').click(function(){
                $('.recomand-btns input:first-child').prop('checked', true);
            });

        })( jQuery );
        (function( $ ) {

$('.button-qty').click(function(e){
    e.preventDefault();
    const inputQty = $(this).parent().find('input')[0];

    if ( $(this).data('quantity') === 'plus' ) {
        inputQty.stepUp();
    } else {
        inputQty.stepDown();
    }

    $(inputQty).trigger('change');

});

})( jQuery );
    </script>
<?php
}

// Agregar el código JavaScript al pie de página
add_action('wp_footer', 'add_custom_js');

// Agregar el código CSS personalizado
function add_custom_css() {
    ?>
    <style>
      .cart input[type="number"] {
    -webkit-appearance: textfield;
    -moz-appearance: textfield;
    appearance: textfield;
}
  
.cart input[type=number]::-webkit-inner-spin-button,
.cart input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
}

.cart button[data-field = 'quantity']{
    background-color:#e6e6e6;
}
        .recomand-btns {
            margin: 10px auto;
        }

        .recomand-btns input {
            display: none;
        }

        .recomand-btns label {
            cursor: pointer;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 10px;
            color: grey;
            padding: 3px 10px;
        }

        .recomand-btns input:checked + label {
            background-color: grey;
            color: white;
        }
    </style>
    <?php
}

// Agregar el código CSS al encabezado
add_action('wp_head', 'add_custom_css');
add_filter( 'woocommerce_loop_add_to_cart_link', 'dcms_add_quantity_field', 10, 2 );
function dcms_add_quantity_field($html, $product) {
    if($product&&
        $product->is_type('simple')&&
        $product->is_purchasable()&&
        $product->is_in_stock()&&
        !$product->is_sold_individually()){

        $html='<form action="'. esc_url($product->add_to_cart_url()).'" class="cart" method="post" enctype="multipart/form-data">';
        $html.= woocommerce_quantity_input(array(),$product,false);
        $html.='<button type="submit" data-quantity="1" data-product_id="'.$product->get_id().'" class="button alt ajax_add_to_cart add_to_cart_button product_type_simple">'. esc_html($product->add_to_cart_text()).'</button>';
        $html.='</form>';
    }
    return$html;
}

//Agreamos código javascript
add_action( 'init', 'dcms_quantity_change' );
    function dcms_quantity_change() {
    wc_enqueue_js('
        (function( $ ) {
            $("form.cart").on("change", "input.qty", function() {
                $(this.form).find("[data-quantity]").attr("data-quantity", this.value);
            });
        })( jQuery );
    ');
}