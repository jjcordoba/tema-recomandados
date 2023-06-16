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
      $categories_per_row = 6;
      $row_count = ceil($category_count / $categories_per_row);
      ?>
      <div class="category-slider">
          <div class="slider-container">
              <div class="slider-wrapper">
                  <ul class="slides">
                      <?php $counter = 0; foreach ($categories as $category) : ?>
                          <li class="<?php if ($counter >= $categories_per_row) { echo 'hidden-slide'; } ?>">
                              <div class="category">
                                  <?php
                                      $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                                      $image_url = wp_get_attachment_url($thumbnail_id);
                                  ?>
                                  <a href="<?php echo get_term_link($category); ?>">
                                      <img src="<?php echo $image_url; ?>" alt="<?php echo $category->name; ?>" />
                                      <h3><?php echo $category->name; ?></h3>
                                  </a>
                              </div>
                          </li>
                          <?php $counter++; endforeach; ?>
                  </ul>
              </div>
          </div>
          <div class="slider-nav">
              <span class="slider-prev">◀</span>
              <span class="slider-next">▶</span>
          </div>
      </div>
      <style>
    .category-slider {
        position: relative;
        width: 100%;
        overflow: hidden;
    }
    
    .slider-container {
        width: 100%;
        overflow: hidden;
        margin-bottom: 20px;
    }
    
    .slider-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .category-slider .slides {
        display: flex;
        flex-wrap: wrap;
        padding: 0;
        margin: 0;
        list-style: none;
        transform: translateX(0);
        transition: transform 0.5s ease-in-out;
    }
    
    .category-slider .slides li {
        width: calc(100% / <?php echo $categories_per_row; ?>);
        padding: 0 10px;
        box-sizing: border-box;
    }
    
    .category-slider .category {
        text-align: center;
        
        padding: 20px;
    }
    
    .category-slider .category h3 {
        margin-top: 0;
    }
    
    .category-slider .slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        display: flex;
        justify-content: space-between;
        width: 100%;
        padding: 0 20px;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }
    
    .category-slider:hover .slider-nav {
        opacity: 1;
    }
    
    .category-slider .slider-nav span {
        cursor: pointer;
    }
    
    .category-slider .hidden-slide {
        display: none;
    }
</style>

      <script>
          jQuery(document).ready(function($) {
              var sliderContainer = $('.slider-container');
              var slideWrapper = $('.slider-wrapper');
              var slides = $('.slides');
              var slideWidth = sliderContainer.width();
              var slideCount = $('.slides li').length;
              var totalWidth = slideWidth * slideCount;
              var autoPlayInterval;
          
              slides.css('width', totalWidth);
          
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
                  slides.animate({marginLeft: slideWidth}, 500, function() {
                      slides.css('margin-left', 0);
                      slides.find('li:last').prependTo(slides);
                      startAutoPlay();
                  });
              });
          
              $('.slider-next').click(function() {
                  stopAutoPlay();
                  slides.animate({marginLeft: -slideWidth}, 500, function() {
                      slides.css('margin-left', 0);
                      slides.find('li:first').appendTo(slides);
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
