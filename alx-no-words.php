<?php
/*
  Plugin Name: No foreign words
  Version: v0.6
  Plugin URI: https://manacoa.com
  Author: Isla de Manacoa
  Author URI: https://manacoa.com/
  Description: Borra los archivos de lenguajes de las carpetas de plugins y temas y también los de al raiz de la carpeta /wp-content/lenguages. Además, evita que se vuelvan a instalar en las actualizaciones automáticas. Si el plugin está instalado, hay que actualizar los lenguajes manualmente.
 */

/*
/version log
0.7 - borra también de la raiz

*/
//https://wordpress.stackexchange.com/questions/300764/disabling-translation-update
add_filter( 'option_active_plugins', 'alx_ince_disable_plugin' );
function alx_ince_disable_plugin($plugins){
       if (htmlspecialchars(trim(wp_make_link_relative(get_site_url()),'/')) == htmlspecialchars(trim($_SERVER['REQUEST_URI'],'/'))) {
            $plugins_not_needed = array ('contact-form-7/wp-contact-form-7.php',
			   'email-templates/email-templates.php',
			   'fetch-tweets/fetch-tweets.php',
			   'wp-support-plus-responsive-ticket-system/wp-support-plus.php',
			   'woocommerce/woocommerce.php');
            foreach ( $plugins_not_needed as $plugin ) {
                $key = array_search( $plugin, $plugins );
                if ( false !== $key ) {
                    unset( $plugins[ $key ] );
                }
            }
        }
        return $plugins;
    }
	
if( function_exists('add_filter') ){
	add_filter( 'auto_update_translation', '__return_false' ); 
	add_filter( 'async_update_translation', '__return_false' );
	} 


add_action( 'admin_menu', 'register_alx_no_words_menu_page' );
function register_alx_no_words_menu_page() {
    add_menu_page(
        'Alx No Words m',
        'Alx No Words',
        'manage_options',
        'alx-no-words',
        'borra_archivos_alx_no_words',
        'dashicons-welcome-widgets-menus',
        6
    );
}



function borra_archivos_alx_no_words() {
	$retorno = '';
	
	$crpt = ( isset( $_GET['crpt'] ) && !is_numeric( $_GET['crpt'] ) ) ?  $_GET['crpt']  : 0;
	$tst = ( isset( $_GET['tst'] ) && is_numeric( $_GET['tst'] ) ) ? intval( $_GET['tst'] ) : 0;
	
	if($crpt){
			if($crpt==='raiz'){
				$path_archivo = trailingslashit(WP_LANG_DIR);
			}else{
				$path_archivo = trailingslashit(WP_LANG_DIR).$crpt.'/';
			}
			

			foreach (array_filter(glob($path_archivo.'/*'), 'is_file') as $file){
				if(!strpos($file, 'es_ES') && !strpos($file, 'uk')){
					if(!$tst){
							$retorno .= 'Se borrará: '.$file.'<br>';
						}else{
							$retorno .= 'Borrando: '.$file.'<br>';
							unlink($file);
						}
					$tamano = filesize($file);
					$retorno .= 'Ahorrando : '.$tamano.'bites<br>';
				}
		
			}
	}
	
	echo '<h3>Elegir la carpeta y si es un test o se desea borrar.</h3>
	<table style="padding:5px;text-align:center">
	<tr><td>&nbsp;</td><td>Temas</td><td>Plugins</td><td>Raiz</td></tr>
	<tr><td>Comprobar</td><td><a href="/wp-admin/admin.php?page=alx-no-words&crpt=themes">x</a></td><td><a href="/wp-admin/admin.php?page=alx-no-words&crpt=plugins">x</a></td><td><a href="/wp-admin/admin.php?page=alx-no-words&crpt=raiz">x</a></td></tr>
	<tr><td>Borrar</td><td><a href="/wp-admin/admin.php?page=alx-no-words&crpt=themes&tst=1">x</a></td><td><a href="/wp-admin/admin.php?page=alx-no-words&crpt=plugins&tst=1">x</a></td><td><a href="/wp-admin/admin.php?page=alx-no-words&crpt=raiz&tst=1">x</a></td></tr>';
	echo '</table>';
	if ($crpt){
		if($retorno){
			echo '<h3>Archivos que se borrarán (o borrados) en la carpeta de '.$crpt.'</h3>' .$retorno;
		}else{
			echo '<h3>No se han encontrado archivos para borrar</h3>';
		}
	}
	echo 'el directorio: '.trailingslashit(WP_LANG_DIR);
	
}