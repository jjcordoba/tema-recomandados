<?php
// Mostrar opciones de variación en la página de tienda y categorías.
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_single_add_to_cart', 30 );
