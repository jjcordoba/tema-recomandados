<?php

defined( 'ABSPATH' ) || exit;

?>
<li>
	<?php do_action( 'woocommerce_widget_product_review_item_start', $args ); ?>

	<?php
	// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>

	<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
		<?php echo $product->get_image(); ?>
		<span class="product-title"><?php echo wp_kses_post( $product->get_name() ); ?></span>
	</a>

	<?php echo wc_get_rating_html( intval( get_comment_meta( $comment->comment_ID, 'rating', true ) ) ); ?>

	<span class="reviewer">
	<?php
	/* translators: %s: Comment author. */
	echo sprintf( esc_html__( 'by %s', 'woocommerce' ), get_comment_author( $comment->comment_ID ) );
	?>
	</span>

	<?php
	// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped
	?>

	<?php do_action( 'woocommerce_widget_product_review_item_end', $args ); ?>
</li>
