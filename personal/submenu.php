<?php
function agregar_submenu_shortcode_temas() {
  add_submenu_page(
      'themes.php',                // Página principal del menú
      'Shortcode Temas',           // Título de la página
      'Shortcode Temas',           // Título del menú
      'manage_options',            // Capacidad requerida para acceder al menú
      'shortcode-temas',           // Identificador único del menú
      'mostrar_contenido_shortcode_temas'  // Función que mostrará el contenido del submenú
  );
}

function mostrar_contenido_shortcode_temas() {
  // Ruta del archivo shortcodes.php dentro de la carpeta del tema info
  $ruta_archivo = get_template_directory() . '/info/shortcode.php';

  // Verificar si el archivo existe
  if (file_exists($ruta_archivo)) {
      // Mostrar el contenido del archivo como una página web
      echo '<html><body>';
      echo file_get_contents($ruta_archivo);
      echo '</body></html>';
  } else {
      echo 'El archivo shortcode.php no se encontró.';
  }
}

// Agregar el submenú al hook 'admin_menu'
add_action('admin_menu', 'agregar_submenu_shortcode_temas');
