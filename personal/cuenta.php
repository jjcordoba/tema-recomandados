<?php
function mi_shortcode_cuenta() {
  // Verificar si el usuario está logeado
  if (is_user_logged_in()) {
      $mi_cuenta_url = home_url('/mi-cuenta');
      return '<a href="' . $mi_cuenta_url . '"><i class="fas fa-user"></i> Mi cuenta</a>';
  } else {
      // Obtener la URL de inicio de sesión/registro
      $url_login = wp_login_url();
      
      // Retornar el enlace con el icono
      return '<a href="' . $url_login . '"><i class="fas fa-user"></i> Iniciar sesión / Registrarse</a>';
  }
}
add_shortcode('mi_cuenta', 'mi_shortcode_cuenta');