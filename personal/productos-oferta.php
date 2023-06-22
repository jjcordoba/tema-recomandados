<?php 
function mostrar_productos_al_azar()
{
    $args = array(
        'post_type' => 'product',
        'orderby' => 'rand',
        'posts_per_page' => 8,
    );
    $productos = new WP_Query($args);
    if ($productos->have_posts()) {
        echo '<div class="productos-container">';
        $count = 0;
        while ($productos->have_posts()) {
            $productos->the_post();
            global $product;
            echo '<div class="producto">';
            echo '<img src="' . get_the_post_thumbnail_url() . '" alt="' . get_the_title() . '">';
            echo '<div class="contenido">';
            echo '<p>Categoría: ' . get_the_terms(get_the_ID(), 'product_cat')[0]->name . '</p>';
            echo '<h3>' . get_the_title() . '</h3>';
            echo '<p>Precio: ' . $product->get_price_html() . '</p>';
            echo '<button class="agregar_al_carrito" data-product-id="' . get_the_ID() . '">Agregar al carrito</button>';
            echo '<p class="deseos" data-product-id="' . get_the_ID() . '">Agregar a la lista de deseos</p>';
            echo '</div>';
            echo '</div>';
            $count++;
            if ($count >= 4) { // Máximo 4 productos por fila
                break;
            }
        }
        echo '</div>';
    }
    wp_reset_postdata();
}

function mostrar_productos_al_azar_css()
{
    echo '<style>
        .productos-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
        }
        .producto {
            display: flex;
            align-items: center;
            width: 23%;
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
          }
          
          .producto img {
            width: 30%;
            height: auto;
            margin-right: 10px;
          }
          
          .producto .contenido {
            width: 50%;
          }
          
        .producto p,
        .producto h3 {
            margin-left: 0;
        }
        .producto p {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .producto h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .producto button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .producto button:hover {
            background-color: #0069d9;
            cursor: pointer;
        }
        .producto .deseos {
            font-size: 14px;
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
        }
        
        /* Estilos para dispositivos móviles */
        @media (max-width: 768px) {
            .productos-container {
                justify-content: flex-start;
            }
            .producto {
                width: 100%;
            }
        }
        
        /* Estilos para tabletas */
        @media (min-width: 769px) and (max-width: 1024px) {
            .productos-container {
                justify-content: space-between;
            }
            .producto {
                width: 31%;
            }
        }
        
        /* Estilos para ordenadores */
        @media (min-width: 1025px) {
            .productos-container {
                justify-content: space-between;
            }
            .producto {
                width: 23%;
            }
        }
    </style>';
}

function agregar_al_carrito_ajax()
{
    wp_enqueue_script('jquery');
    $ajax_nonce = wp_create_nonce('agregar-al-carrito-nonce');
    ?>
    <script>
        jQuery(document).ready(function($) {
            $(document).on('click', '.agregar_al_carrito', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'agregar_al_carrito',
                        product_id: productId,
                        security: '<?php echo $ajax_nonce; ?>'
                    },
                    success: function(response) {
                        alert('El producto ha sido agregado al carrito.');
                    }
                });
            });
             $(document).on('click', '.deseos', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'agregar_a_lista_deseos',
                        product_id: productId,
                        security: '<?php echo $ajax_nonce; ?>'
                    },
                    success: function(response) {
                        alert('El producto ha sido agregado a la lista de deseos.');
                    }
                });
            });
        });
    </script>
    <?php
}
 function agregar_al_carrito()
{
    check_ajax_referer('agregar-al-carrito-nonce', 'security');
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        WC()->cart->add_to_cart($product_id);
    }
    wp_die();
}
 function agregar_a_lista_deseos()
{
    check_ajax_referer('agregar-al-carrito-nonce', 'security');
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        // Agregar el código para agregar a la lista de deseos aquí
    }
    wp_die();
}
  
add_shortcode('productos_oferta', 'mostrar_productos_al_azar');
add_action('wp_head', 'mostrar_productos_al_azar_css');
