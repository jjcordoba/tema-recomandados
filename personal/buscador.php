<?php

function buscador_productos_shortcode() {
    ob_start();
    ?>

    <div class="search-container">
        <form class="search-form">
            <?php
            $categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => false,
            ));

            if ($categories) :
            ?>
                <select class="search-category">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <input type="text" class="search-input" placeholder="Busca productos, marcas y más">

            <button type="submit" class="search-submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.784 22.8l-6.168-6.144c1.584-1.848 2.448-4.176 2.448-6.576 0-5.52-4.488-10.032-10.032-10.032-5.52 0-10.008 4.488-10.008 10.008s4.488 10.032 10.032 10.032c2.424 0 4.728-0.864 6.576-2.472l6.168 6.144c0.144 0.144 0.312 0.216 0.48 0.216s0.336-0.072 0.456-0.192c0.144-0.12 0.216-0.288 0.24-0.48 0-0.192-0.072-0.384-0.192-0.504zM18.696 10.080c0 4.752-3.888 8.64-8.664 8.64-4.752 0-8.64-3.888-8.64-8.664 0-4.752 3.888-8.64 8.664-8.64s8.64 3.888 8.64 8.664z"></path>
                </svg>
            </button>
        </form>
    </div>

    <div id="search-results"></div>

    <script>
        (function($) {
            $(document).ready(function() {
                function searchProducts() {
                    var searchValue = $('.search-input').val();
                    var categoryValue = $('.search-category').val();

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
                            $('#search-results').empty().append('<p>Cargando resultados...</p>');
                        },
                        success: function(response) {
                            $('#search-results').html(response);
                        },
                        error: function() {
                            $('#search-results').html('<p>Ocurrió un error al cargar los resultados.</p>');
                        }
                    });
                }

                $('.search-form').on('submit', function(e) {
                    e.preventDefault();
                    searchProducts();
                });

                $('.search-input').on('input', function() {
                    var searchValue = $(this).val();
                    if (searchValue.length >= 3) {
                        searchProducts();
                    } else {
                        $('#search-results').empty();
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

                $(document).on('click', '.ver-todos', function(e) {
                    e.preventDefault();
                    var searchValue = $('.search-input').val();
                    var categoryValue = $('.search-category').val();
                    var searchUrl = '<?php echo home_url("/search"); ?>';
                    searchUrl += '?search=' + encodeURIComponent(searchValue);
                    if (categoryValue) {
                        searchUrl += '&category=' + encodeURIComponent(categoryValue);
                    }
                    window.location.href = searchUrl;
                });
            });
        })(jQuery);
    </script>

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
        's' => '"' . $search . '"', // Búsqueda de palabras completas
    );

    if (!empty($category)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $category,
            ),
        );
    }

    $products = new WP_Query($args);

    ob_start();

    if ($products->have_posts()) {
        $result_count = 0;

        echo '<div class="search-results">';
        
        while ($products->have_posts()) {
            $products->the_post();
            $product = wc_get_product(get_the_ID());

            $image = $product->get_image();
            $name = $product->get_name();
            $price = $product->get_price();
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();
            $product_url = get_permalink($product->get_id()); // Obtener URL del producto

            echo '<div class="product" data-product-url="' . $product_url . '">';
            echo '<div class="product-info">';
            echo '<div class="product-image">' . $image . '</div>';
            echo '<div class="product-name">' . $name . '</div>';
            echo '</div>';
            echo '<div class="product-price">';
            if ($regular_price !== $sale_price) {
                echo '<span class="regular-price">' . wc_price($regular_price) . '</span>';
            }
            echo '<span class="sale-price">' . wc_price($price) . '</span>';
            echo '</div>';
            echo '</div>';

            $result_count++;
        }

        echo '</div>';

        if ($result_count > 7) {
            $search_url = home_url('/?s=' . urlencode($search) . '&category=' . $category);
            echo '<div class="view-all-results">';
            echo '<a href="' . $search_url . '">Ver todos</a>';
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
