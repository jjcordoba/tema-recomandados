<?php
function slider_autoreproducido_shortcode() {
    ob_start();
    ?>
    <style>
    .slider {
        overflow: hidden;
        text-align: center;
    }

    .slide {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 100%;
        display: none;
        color: white;
        animation: slide-in-out 5s infinite;
    }

    .slide.active {
        display: block;
    }

    @keyframes slide-in-out {
        0% {
            left: 100%;
        }
        10% {
            left: 0;
        }
        90% {
            left: 0;
        }
        100% {
            left: -100%;
        }
    }
    </style>

    <div class="slider">
        <div class="slide active">Texto 1</div>
        <div class="slide">Texto 2</div>
        <div class="slide">Texto 3</div>
    </div>

    <script>
    (function($) {
        $(document).ready(function() {
            var slides = $('.slide');
            var currentSlide = 0;

            function showSlide() {
                slides.removeClass('active');
                slides.eq(currentSlide).addClass('active');

                currentSlide++;
                if (currentSlide >= slides.length) {
                    currentSlide = 0;
                }
            }

            showSlide();
            setInterval(showSlide, 5000);
        });
    })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('slider_autoreproducido', 'slider_autoreproducido_shortcode');
