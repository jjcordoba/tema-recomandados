<?php

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

?>

<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
	<?php endif; ?>

	<?php
	
	do_action( 'woocommerce_archive_description' );
	?>
</header>

<?php
if ( woocommerce_product_loop() ) {

	do_action( 'woocommerce_before_shop_loop' );
	?>

	<div class="products">
		<?php
		woocommerce_product_loop_start();

		while ( have_posts() ) {
			the_post();

			
			do_action( 'woocommerce_shop_loop' );

			echo '<a href="' . esc_url( get_permalink() ) . '">';
			echo woocommerce_get_product_thumbnail();
			echo '</a>';

			woocommerce_template_single_add_to_cart();

			echo '<a href="' . esc_url( get_permalink() ) . '" class="button add_to_cart_button">' . esc_html__( 'Agregar al carrito', 'woocommerce' ) . '</a>';
		}

		woocommerce_product_loop_end();
		?>
	</div>

	<?php
	
	do_action( 'woocommerce_after_shop_loop' );
} else {
	
	do_action( 'woocommerce_no_products_found' );
}

do_action( 'woocommerce_after_main_content' );

w
do_action( 'woocommerce_sidebar' );

get_footer( 'shop' );
