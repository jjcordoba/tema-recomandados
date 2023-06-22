<?php

function buscador_productos_shortcode() {
    ob_start();
    ?>
<style>
    
    .product {
width: auto;  
}
input.search-input:placeholder-shown {
  font-size: 18px;
}
@media only screen and (min-width: 600px) {
form.search-form {
  width: 820px;
}
}


    select.search-category {
  background: #ffffff;
}

form.search-form {
  background: #ffffff;
}
button.search-submit:hover {
  background: #2D8A36;
}
form button svg:hover {
  color: #ffffff;
  }
  form button svg {
  color: #000000;
  }
input.search-input {
  border-top: 0;
  border-right: 0;
  border-bottom: 0;
}
  .search-form {
      display: flex;
      align-items: center;
      background-color: #ffffff;
    border-radius: 20px;
      padding: 5px;
  }

  .search-category {
    font-family: 'poppins', sans-serif;
      flex-grow: 1;
      border: none;
      outline: none;
      padding: 5px;
      font-size: 16px;
  }

  .search-input {
      border: none;
      outline: none;
      padding: 5px;
      font-size: 14px;
  }

  .search-submit {
      border: none;
      background-color: transparent;
      cursor: pointer;
      padding: 5px;
  }

  .search-results {
      margin-top: 10px;
      position: absolute;
      z-index: 999;
      background-color: #ffffff;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      max-height: 300px;
      overflow-y: auto;
  }

  .search-results .product {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      cursor: pointer; /* Added */
  }

  .product-image {
      width: 50px;
      height: 50px;
      object-fit: cover;
      margin-right: 10px;
  }

  .product-info {
      display: flex; /* Added */
      align-items: center; /* Added */
  }

  .product-name {
      font-weight: bold;
  }

  .product-price {
      margin-top: 5px;
  }

  .product-price .regular-price {
      text-decoration: line-through;
      color: red;
      margin-right: 5px;
  }

  .product-price .sale-price {
      font-weight: bold;
      color: green;
  }

.view-all-results {
       position: fixed;
    bottom: 321px;
    left: 50%;
    /* transform: translateX(-50%); */
    z-index: 999;
}

    </style>
<div class="search-container">
    <form class="search-form">
        <input type="text" class="search-input" placeholder="Busca productos, marcas y más">
    </form>
    <div id="search-results" class="search-results"></div>
</div>
<script>
    (function($) {
        $(document).ready(function() {
            function searchProducts() {
                var searchValue = $('.search-input').val();

                if (searchValue.length >= 3) {
                    // Realizar la búsqueda utilizando AJAX
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        method: 'POST',
                        data: {
                            action: 'buscar_productos',
                            search: searchValue,
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
                } else {
                    $('#search-results').empty();
                }
            }

            $('.search-input').on('input', function() {
                searchProducts();
            });

            $(document).on('click', '.product', function() {
                var productUrl = $(this).data('product-url');
                if (productUrl) {
                    window.location.href = productUrl;
                }
            });

            $(document).on('click', function(event) {
                var searchContainer = $('.search-container');

                if (!searchContainer.is(event.target) && searchContainer.has(event.target).length === 0) {
                    $('#search-results').empty();
                }
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

    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        's' => $search,
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
            echo '<div class="product-image">' . $image . '</div>';
            echo '<div class="product-name">' . $name . '</div>';
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
?>
