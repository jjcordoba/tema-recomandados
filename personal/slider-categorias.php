<?php 

function slider_shortcode($atts) {
  ob_start();
  
  // Establecer los atributos predeterminados del shortcode
  $attributes = shortcode_atts(
      array(
          'category' => '',
      ),
      $atts
  );
  
  // Obtener las categorías padre de WooCommerce
  $args = array(
      'taxonomy' => 'product_cat',
      'parent' => 0,
      'hide_empty' => false
  );
  $categories = get_terms($args);
  
  if (!empty($categories)) {
      $category_count = count($categories);
      $categories_per_slide = 6;
      $slide_count = ceil($category_count / $categories_per_slide);
      ?>
      <div class="category-slider">
          <div class="slider-container">
              <div class="slider-wrapper">
                  <?php for ($i = 0; $i < $slide_count; $i++) : ?>
                      <div class="slide">
                          <ul class="slides">
                              <?php $start_index = $i * $categories_per_slide; ?>
                              <?php $end_index = min(($i + 1) * $categories_per_slide, $category_count); ?>
                              <?php for ($j = $start_index; $j < $end_index; $j++) : ?>
                                  <li>
                                      <a href="<?php echo get_term_link($categories[$j]); ?>">
                                          <div class="category">
                                              <?php
                                                  $thumbnail_id = get_term_meta($categories[$j]->term_id, 'thumbnail_id', true);
                                                  $image_url = wp_get_attachment_url($thumbnail_id);
                                              ?>
                                              <div class="category-image">
                                                  <img src="<?php echo $image_url; ?>" alt="<?php echo $categories[$j]->name; ?>" />
                                              </div>
                                              <h3 class="category-title"><?php echo $categories[$j]->name; ?></h3>
                                          </div>
                                      </a>
                                  </li>
                              <?php endfor; ?>
                          </ul>
                      </div>
                  <?php endfor; ?>
              </div>
          </div>
          <div class="slider-nav">
              <span class="slider-prev">&lsaquo;</span>
              <span class="slider-next">&rsaquo;</span>
          </div>
      </div>
      <style>
        .category-slider {
            position: relative;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .slider-container {
            width: 100%;
            overflow: hidden;
            white-space: nowrap; /* Evita que las diapositivas se desplacen a la siguiente línea */
        }
        
        .slider-wrapper {
            display: inline-block; /* Mantiene el ancho ajustado al contenido */
            transition: transform 0.5s ease-in-out; /* Agrega una transición suave al cambiar de diapositiva */
        }
        
        .slide {
            display: inline-block; /* Muestra las diapositivas en línea */
            vertical-align: top; /* Alinea las diapositivas en la parte superior */
            width: 100%; /* Ocupa todo el ancho del contenedor */
        }
        
        .category-slider .slides {
            display: flex;
            padding: 0;
            margin: 0;
            list-style: none;
            width: max-content; /* Ajusta el ancho al contenido */
            transition: transform 0.5s ease-in-out; /* Agrega una transición suave al cambiar de diapositiva */
        }
        
        .category-slider .slides li {
            width: calc(100% / <?php echo $categories_per_slide; ?>);
            padding: 0 10px;
            box-sizing: border-box;
        }
        
        .category-slider .category {
            text-align: center;
            padding: 20px;
        }
        
        .category-slider .category-image img {
            max-width: 100%;
            height: auto;
        }
        
        .category-slider .category-title {
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }
        
        .category-slider .slider-nav {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        
        .category-slider .slider-nav span {
            cursor: pointer;
            margin: 0 5px;
            font-size: 24px;
            color: #999;
        }
        
        .category-slider .slider-nav span:hover {
            color: #555;
        }
      </style>

      <script>
          jQuery(document).ready(function($) {
              var sliderWrapper = $('.slider-wrapper');
              var slides = $('.slide');
              var slideWidth = sliderWrapper.width();
              var slideCount = slides.length;
              var totalWidth = slideWidth * slideCount;
              var autoPlayInterval;
          
              sliderWrapper.css('width', totalWidth);
          
              function startAutoPlay() {
                  autoPlayInterval = setInterval(function() {
                      $('.slider-next').click();
                  }, 5000);
              }
          
              function stopAutoPlay() {
                  clearInterval(autoPlayInterval);
              }
          
              $('.slider-prev').click(function() {
                  stopAutoPlay();
                  sliderWrapper.animate({scrollLeft: '-=' + slideWidth}, 800, function() {
                      sliderWrapper.find('.slide:last').detach().prependTo(sliderWrapper);
                      sliderWrapper.scrollLeft(slideWidth);
                      startAutoPlay();
                  });
              });
          
              $('.slider-next').click(function() {
                  stopAutoPlay();
                  sliderWrapper.animate({scrollLeft: '+=' + slideWidth}, 800, function() {
                      sliderWrapper.find('.slide:first').detach().appendTo(sliderWrapper);
                      sliderWrapper.scrollLeft(slideWidth);
                      startAutoPlay();
                  });
              });
              
              $('.category-slider .slides li').click(function() {
                  var categoryLink = $(this).find('a').attr('href');
                  window.location.href = categoryLink;
              });
          
              startAutoPlay();
          });
      </script>
      <?php
  } else {
      echo 'No se encontraron categorías.';
  }
  
  return ob_get_clean();
}
add_shortcode('category_slider', 'slider_shortcode');
