<?php


function cargar_font_awesome_desde_wordpress() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'cargar_font_awesome_desde_wordpress');

function mostrar_categorias_padre() {
    $args = array(
        'taxonomy' => 'product_cat',
        'parent' => 0,
        'hide_empty' => false,
    );

    $categorias = get_terms($args);

    if (!empty($categorias)) {
        ob_start();
        ?>

        <style>
            .menu-amurgesa {
                position: relative;
                padding: 10px;
            }

            .icono-amurgesa {
                display: flex;
                align-items: center;
                cursor: pointer;
            }

            .icono-amurgesa .fas {
                margin-right: 5px;
            }

            .texto-departamentos {
                display: inline-block;
                margin-left: 5px;
                font-weight: bold;
                cursor: pointer;
            }

            .categorias-padre {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                background-color: #fff;
                padding: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                z-index: 9999;
            }

            .menu-amurgesa.open .categorias-padre {
                display: block;
            }

            .categorias-padre li {
                padding-left: 14px;
                padding-right: 14px;
                margin-bottom: 10px;
                list-style: none;
            }
        </style>

        <script>
            jQuery(document).ready(function($) {
                $('.menu-amurgesa .icono-amurgesa').click(function() {
                    $(this).parent().toggleClass('open');
                    $(this).siblings('.categorias-padre').slideToggle();
                });

                $(document).click(function(event) {
                    if (!$(event.target).closest('.menu-amurgesa').length) {
                        $('.menu-amurgesa').removeClass('open');
                        $('.categorias-padre').slideUp();
                    }
                });
            });
        </script>

        <div class="menu-amurgesa">
            <div class="icono-amurgesa">
                <i class="fas fa-bars"></i>
                <span class="texto-departamentos">Todos los departamentos</span>
            </div>
            <ul class="categorias-padre">
                <?php foreach ($categorias as $categoria) : ?>
                    <li><a href="<?php echo get_term_link($categoria); ?>"><?php echo $categoria->name; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php
        return ob_get_clean();
    }
}

add_shortcode('mostrar_categorias', 'mostrar_categorias_padre');
