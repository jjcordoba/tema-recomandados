<?php
function mostrar_todos_los_productos()
{
    $args = array(
        'post_type' => 'product',
        'orderby' => 'rand',
        'posts_per_page' => 8,
    );
    $productos = new WP_Query($args);
    if ($productos->have_posts()) {
        echo '<div class="todos-productos_content">';
        while ($productos->have_posts()) {
            $productos->the_post();
            global $product;
            echo '<div class="product">';
            echo '<img src="' . get_the_post_thumbnail_url() . '" alt="' . get_the_title() . '">';
            echo '<div class="produccontent">';
            echo '<p>Categoría: ' . get_the_terms(get_the_ID(), 'product_cat')[0]->name . '</p>';
            echo '<h3>' . get_the_title() . '</h3>';
            echo '<p>Precio: ' . $product->get_price_html() . '</p>';
            echo '<button class="agregar-carrito" data-product-id="' . get_the_ID() . '">Agregar al carrito</button>';
            echo '<p class="deseos" data-product-id="' . get_the_ID() . '">Agregar a la lista de deseos</p>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        // Agregar el botón "Ver más"
        echo '<div class="ver-mas-wrapper">';
        echo '<button class="ver-mas">Ver más</button>';
        echo '</div>';
    }
    wp_reset_postdata();
}

function todos_los_productos_css()
{
    echo '<style>
        /* Estilos previos */

        .todos-productos_content {
          display: flex;
          flex-wrap: wrap;
          justify-content: space-between;
      }
      

        .product {
            width: 23%;
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .product img {
            width: 100%;
            height: auto;
            display: block;
            margin-bottom: 10px;
            float: left;
            margin-right: 10px;
        }
        .product .produccontent {
            margin-left: 10px;
            float: left;
            width: 75%;
        }
        .product p,
        .product h3 {
            margin-left: 0;
        }
        .product p {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .product h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .product button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .product button:hover {
            background-color: #0069d9;
            cursor: pointer;
        }
        .product .deseos {
            font-size: 14px;
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
        }
        
        /* Estilos para dispositivos móviles */
        @media (max-width: 768px) {
            .todos-productos_content {
                justify-content: flex-start;
            }
            .product {
                width: 100%;
            }
        }
        
        /* Estilos para tabletas */
        @media (min-width: 769px) and (max-width: 1024px) {
            .todos-productos_content {
                justify-content: space-between;
            }
            .product {
                width: 31%;
            }
        }
        
        /* Estilos para ordenadores */
        @media (min-width: 1025px) {
            .todos-productos_content {
                justify-content: space-between;
            }
            .product {
                width: 23%;
            }
        }

        /* Estilos para el botón "Ver más" */
        .ver-mas-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .ver-mas {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .ver-mas:hover {
            background-color: #0069d9;
        }
    </style>';
}

function agregar_a_carrito_ajax()
{
    wp_enqueue_script('jquery');
    ?>
    <script>
        jQuery(document).ready(function($) {
            $(document).on('click', '.agregar-carrito', function(e) {
                e.preventDefault();
                var productId = $(this).data('product-id');
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'agregar_a_carrito',
                        product_id: productId
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
                        action: 'agregar_lista_deseos',
                        product_id: productId
                    },
                    success: function(response) {
                        alert('El producto ha sido agregado a la lista de deseos.');
                    }
                });
            });

            // Función para cargar más productos
            $(document).on('click', '.ver-mas', function(e) {
                e.preventDefault();
                var currentCount = $('.product').length;
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'cargar_mas_productos',
                        current_count: currentCount
                    },
                    success: function(response) {
                        $('.ver-mas-wrapper').before(response);
                    }
                });
            });
        });
    </script>
    <?php
}

function agregar_a_carrito()
{
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        WC()->cart->add_to_cart($product_id);
    }
    wp_die();
}

function agregar_lista_deseos()
{
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        // Agregar el código para agregar a la lista de deseos aquí
    }
    wp_die();
}

function cargar_mas_productos()
{
    if (isset($_POST['current_count'])) {
        $current_count = intval($_POST['current_count']);
        $args = array(
            'post_type' => 'product',
            'orderby' => 'rand',
            'posts_per_page' => 8,
            'offset' => $current_count
        );
        $productos = new WP_Query($args);
        if ($productos->have_posts()) {
            echo '<div class="todos-productos_content">'; // Agregar este contenedor
            while ($productos->have_posts()) {
                $productos->the_post();
                global $product;
                echo '<div class="product">';
                echo '<img src="' . get_the_post_thumbnail_url() . '" alt="' . get_the_title() . '">';
                echo '<div class="produccontent">';
                echo '<p>Categoría: ' . get_the_terms(get_the_ID(), 'product_cat')[0]->name . '</p>';
                echo '<h3>' . get_the_title() . '</h3>';
                echo '<p>Precio: ' . $product->get_price_html() . '</p>';
                echo '<button class="agregar-carrito" data-product-id="' . get_the_ID() . '">Agregar al carrito</button>';
                echo '<p class="deseos" data-product-id="' . get_the_ID() . '">Agregar a la lista de deseos</p>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>'; // Cerrar el contenedor
        }
        wp_reset_postdata();
    }
    wp_die();
}


add_shortcode('todos_los_productos', 'mostrar_todos_los_productos');
add_action('wp_head', 'todos_los_productos_css');
add_action('wp_ajax_cargar_mas_productos', 'cargar_mas_productos');
add_action('wp_ajax_nopriv_cargar_mas_productos', 'cargar_mas_productos');
add_action('wp_ajax_agregar_a_carrito', 'agregar_a_carrito');
add_action('wp_ajax_nopriv_agregar_a_carrito', 'agregar_a_carrito');
add_action('wp_ajax_agregar_lista_deseos', 'agregar_lista_deseos');
add_action('wp_ajax_nopriv_agregar_lista_deseos', 'agregar_lista_deseos');
add_action('wp_footer', 'agregar_a_carrito_ajax');
?>
