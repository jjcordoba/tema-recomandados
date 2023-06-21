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

  .agregar-carrito {
      display: inline-block;
      padding: 5px 10px;
      background-color: #f0f0f0;
      border: none;
      cursor: pointer;
      text-decoration: none;
  }

  .agregar-deseados {
      display: inline-block;
      padding: 5px 10px;
      background-color: #f0f0f0;
      border: none;
      cursor: pointer;
      text-decoration: none;
  }
  </style>

<script>
  jQuery(document).ready(function($) {
      $('.agregar-carrito').on('click', function(e) {
          e.preventDefault();
          var productID = $(this).closest('.producto').find('.agregar-carrito').data('product-id');
          var addToWishlistUrl = '<?php echo esc_js(wc_get_account_endpoint_url('wishlist')); ?>?add_to_wishlist=' + productID;

                  // Eliminar el mensaje después de unos segundos
                  setTimeout(function() {
                      successMessage.remove();
                  }, 3000);
              },
              error: function(jqXHR, textStatus, errorThrown) {
                  // Aquí puedes mostrar un mensaje de error o realizar acciones adicionales
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
                    <span class="agregar-deseados">Agregar a la lista de deseos</span>
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

// Función para procesar la acción de agregar al carrito mediante AJAX
add_action('wp_ajax_agregar_al_carrito', 'agregar_al_carrito_ajax');
add_action('wp_ajax_nopriv_agregar_al_carrito', 'agregar_al_carrito_ajax');

function agregar_al_carrito_ajax() {
  $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
  $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

  if ($product_id > 0) {
    WC()->cart->add_to_cart($product_id, $quantity);
  }

  die();
}
