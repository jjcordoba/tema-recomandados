<?php
require_once 'personal/funcionprincipal.php';
require_once 'personal/shortcode1.php';
require_once 'personal/cuenta.php';
require_once 'personal/buscador.php';
require_once 'personal/departamentos.php';
require_once 'personal/slider-categorias.php';
require_once 'personal/mostrarvariaciones.php';
require_once 'personal/botones.php';
require_once 'personal/productos-oferta.php';
require_once 'personal/productos.php';
require_once 'personal/submenu.php';


add_action('wp_head', 'dcms_show_template_file_name');
function dcms_show_template_file_name() {
    $template = get_page_template();
    $html = "<div style='background:#23282d;padding:4px 10px;color:#eee;font-size:13px;'> ➜ ";
    $html .= $template;
    $html .= "</div>";
    echo $html;
}