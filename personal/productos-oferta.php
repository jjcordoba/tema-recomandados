<?php

function mostrar_productos_aleatorios($atts) {
  $atts = shortcode_atts(array(
      'cantidad' => '5'
  ), $atts);

  $cantidad = intval($atts['cantidad']);

  $args = array(
      'post_type' => 'product',
      'posts_per_page' => $cantidad,
      'orderby' => 'rand',
  );

  $products = new WP_Query($args);

  ob_start();
  ?>

  <style>
  .productos-container {
      display: flex;
      flex-wrap: wrap;
  }

  .producto {
      width: 25%;
      margin-bottom: 20px;
      padding: 10px;
      box-sizing: border-box;
      transition: box-shadow 0.3s ease;
      display: flex;
      align-items: center;
  }

  .producto:hover {
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
  }

  .imagen {
      margin-right: 10px;
  }

  .detalles {
      flex-grow: 1;
  }

  .categoria {
      font-weight: bold;
  }

  .precio {
      font-weight: bold;
  }

  .precio-oferta {
      text-decoration: line-through;
  }

  .agregar-carrito,
  .agregar-deseados {
      display: inline-block;
      padding: 5px 10px;
      background-color: #f0f0f0;
      border: none;
      cursor: pointer;
      text-decoration: none;
      margin-top: 10px;
  }
  </style>

  <script>
  jQuery(document).ready(function($) {
      $('.agregar-deseados').on('click', function(e) {
          e.preventDefault();
          var productID = $(this).closest('.producto').find('.agregar-carrito').data('product-id');
          var addToWishlistUrl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>?action=add_to_wishlist&product_id=' + productID;

          $.ajax({
              url: addToWishlistUrl,
              method: 'GET',
              success: function(response) {
                  window.location.href = response.wishlist_url;
              }
          });
      });
  });
  </script>

  <?php
  if ($products->have_posts()) {
      echo '<div class="productos-container">';

      while ($products->have_posts()) {
          $products->the_post();

          global $product;
          ?>
          <div class="producto">
              <div class="imagen"><?php echo $product->get_image(); ?></div>

              <div class="detalles">
                  <span class="categoria"><?php echo $product->get_categories(); ?></span>
                  <h3 class="nombre"><?php echo get_the_title(); ?></h3>
                  <?php if ($product->is_on_sale()) : ?>
                      <span class="precio-oferta">
                          <?php echo get_woocommerce_currency_symbol(); ?>
                          <?php echo $product->get_sale_price(); ?>
                      </span>
                  <?php else: ?>
                      <span class="precio">
                          <?php echo get_woocommerce_currency_symbol(); ?>
                          <?php echo $product->get_regular_price(); ?>
                      </span>
                  <?php endif; ?>
                  <div>
                    <a href="#" class="agregar-carrito" data-product-id="<?php echo $product->get_id(); ?>" data-quantity="1">Agregar al carrito</a>
                  </div>
                  <div>
                    <a href="#" class="agregar-deseados">Agregar a la lista de deseos</a>
                  </div>
              </div>
          </div>
          <?php
      }

      echo '</div>';
  } else {
      echo 'No se encontraron productos.';
  }

  wp_reset_postdata();

  return ob_get_clean();
}
add_shortcode('productos_aleatorios', 'mostrar_productos_aleatorios');

// Función para procesar la acción de agregar a la lista de deseos mediante AJAX
add_action('wp_ajax_add_to_wishlist', 'agregar_a_lista_deseos_ajax');
add_action('wp_ajax_nopriv_add_to_wishlist', 'agregar_a_lista_deseos_ajax');

function agregar_a_lista_deseos_ajax() {
  $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

  if ($product_id > 0) {
    $wishlist_url = woocommerce_add_to_wishlist($product_id);
    wp_send_json_success(array('wishlist_url' => $wishlist_url));
  } else {
    wp_send_json_error();
  }
  die();
}
