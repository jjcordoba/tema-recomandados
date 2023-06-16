<?php

function buscador_productos_shortcode() {
    ob_start();
    ?>
    <style>
        /* Estilos CSS originales aquí */

    </style>

    <div class="search-container">
        <form class="search-form">
            <?php
            $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
            ));

            if ($categories) :
            ?>
                <select class="search-category" name="category">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <input type="text" class="search-input" placeholder="Busca productos, marcas y más">

            <button type="submit" class="search-submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 24 24">
                    <!-- Ícono de búsqueda aquí -->
                </svg>
            </button>
        </form>

        <div class="search-results"></div>

        <script>
            (function($) {
                $(document).ready(function() {
                    $('.search-form').on('submit', function(e) {
                        e.preventDefault();
                        var searchValue = $('.search-input').val();
                        var categoryValue = $('.search-category').val();

                        // Verificar si se ingresaron al menos 3 letras para iniciar la búsqueda AJAX
                        if (searchValue.length >= 3) {
                            // Realizar la búsqueda utilizando AJAX
                            $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                method: 'POST',
                                data: {
                                    action: 'buscar_productos',
                                    search: searchValue,
                                    category: categoryValue,
                                },
                                beforeSend: function() {
                                    $('.search-results').empty().append('<p>Cargando resultados...</p>');
                                },
                                success: function(response) {
                                    $('.search-results').html(response);
                                },
                                error: function() {
                                    $('.search-results').html('<p>Ocurrió un error al cargar los resultados.</p>');
                                }
                            });
                        }
                    });

                    $(document).on('keyup', '.search-input', function() {
                        var searchValue = $(this).val();

                        if (searchValue.length >= 3) {
                            $('.search-form').submit();
                        } else {
                            $('.search-results').empty();
                        }
                    });

                    $(document).on('click', '.search-submit', function() {
                        $('.search-form').submit();
                    });

                    $(document).on('click', '.product', function() {
                        var productUrl = $(this).data('product-url');
                        if (productUrl) {
                            window.location.href = productUrl;
                        }
                    });
                });
            })(jQuery);
        </script>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('buscador_productos', 'buscador_productos_shortcode');

function buscar_productos_ajax_handler() {
    $search = $_POST['search'];
    $category = $_POST['category'];

    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        's' => $search,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_sku',
                's' => '*' . $search . '*',
                'compare' => 'LIKE'
            ),
            array(
                'relation' => 'OR',
                array(
                    'key' => '_thumbnail_id',
                    'value' => $search,
                    'compare' => 'LIKE'
                ),
                array(
                    'key' => '_product_image_gallery',
                    'value' => $search,
                    'compare' => 'LIKE'
                )
            )
        ),
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category,
                'include_children' => true
            )
        )
    );


    $products = new WP_Query($args);

    ob_start();

    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            $product = wc_get_product(get_the_ID());

            $image = $product->get_image();
            $name = $product->get_name();
            $price = $product->get_price();
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();
            $product_url = get_permalink($product->get_id());

            echo '<div class="product" data-product-url="' . $product_url . '">';
            echo '<div class="product-info">';
            echo '<img class="product-image" src="' . $image . '">';
            echo '<div class="product-name">' . $name . '</div>';
            echo '</div>';
            echo '<div class="product-price">';
            if ($regular_price !== $sale_price) {
                echo '<span class="regular-price">' . wc_price($regular_price) . '</span>';
            }
            echo '<span class="sale-price">' . wc_price($price) . '</span>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No se encontraron productos.</p>';
    }

    wp_reset_postdata();

    $content = ob_get_clean();

    echo $content;

    die();
}

add_action('wp_ajax_buscar_productos', 'buscar_productos_ajax_handler');
add_action('wp_ajax_nopriv_buscar_productos', 'buscar_productos_ajax_handler');
