<?php
/**
 * @package FlyingLeafe
 * @version 1.0
 */
/*
Plugin Name: Random Aphorism
Plugin URI: http://burningweb.ru
Description: Добавляет случайный афоризм на страницу.
Armstrong: My Plugin
Author: Flying Leafe
Version: 1.0
Author URI: http://burningweb.ru
*/

/**************УСТАНОВКА ПЛАГИНА***************/
function aphorism_install()
{
 				 global $wpdb;
 				 //Создаем таблицу в базе данных
				 $table_aphorisms = $wpdb->prefix.aphorisms;
				 $sql =
				 "
				 		 CREATE TABLE IF NOT EXISTS `".$table_aphorisms."` (
						 `id` int auto_increment,
						 `quote` varchar(1000) not null,
						 `author` varchar(100) not null,
						 PRIMARY KEY (id)
						 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				 ";
				 $wpdb->query($sql);
				 
				 //Вставляем цитату по умолчанию
				 $text1 = __('Лучше красиво делать, чем красиво говорить.', 'random_aphorism');
				 $author1 = __('Бенджамин Франклин', 'random_aphorism');
				 $wpdb->insert(
				 				$table_aphorisms,
								array('quote' => $text1, 'author' => $author1),
								array('%s', '%s')
				 );
}

function aphorism_textdomain() {
    load_plugin_textdomain('random_aphorism', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}

/**************ВСТАВЛЕНИЕ АФОРИЗМА В ДОКУМЕНТ***************/
function aphorism_show($atts)
{
 				 global $wpdb;
				 $table_aphorisms = $wpdb->prefix.aphorisms;
				 
				 $items = $wpdb->get_results("SELECT * FROM `" . $table_aphorisms . "`;");
				 $n = rand(0, count($items)-1);
				 
				 $aphorism = 
				 "
				 <div class='random-aphorism-block'>
				 	<blockquote class='random-aphorism-quote'>".$items[$n]->quote."</blockquote>
				 	<p class='random-aphorism-author'>".$items[$n]->author."</p>
				 </div>
				 ";
				 
				 return $aphorism;
}
				 
function aphorism_load_styles()
{
	echo "<link rel='stylesheet' href='" . plugins_url('style.css', __FILE__) . "' type='text/css'>";
}	 
				 
/**************ВЫЗОВ ПУНКТА В МЕНЮ АДМИНКИ***************/
function aphorism_add_admin_page()
{
 				 //Добавляем страницу настроек
				 add_options_page(__('Random Aphorism', 'random_aphorism'), __('Random Aphorism', 'random_aphorism'), 8, 'aphorism', 'aphorism_options');
}

/**************ФОРМИРОВАНИЕ СТРАНИЦЫ НАСТРОЕК***************/
function aphorism_options()
{
 				echo "<h2>" . __('Random Aphorism', 'random_aphorism') . "</h2>";
				echo "<h4>" . __('Add new aphorism into list:', 'random_aphorism') . "</h4>";
				//Выводим форму для добавления афоризма
				aphorism_option_add_new();
				echo "<h4>" . __('Added aphorisms:', 'random_aphorism') . "</h4>";
				//Выводим имеющиеся афоризмы
				aphorism_show_added();
				echo "<h4>" . __('Plugin settings:', 'random_aphorism') . "</h4>";
				//Выводим форму настроек плагина
				aphorism_show_options();
}

/**************ФОРМА ДОБАВЛЕНИЯ НОВОГО АФОРИЗМА***************/
function aphorism_option_add_new() 
{
 				 global $wpdb;
				 $table_aphorisms = $wpdb->prefix.aphorisms;
				 
				 //Проверяем, была ли произведена добавка, если да то вставляем новый афоризм в базу данных.
 				 if(isset($_POST['aphorism_add_btn'])) {
				 					if(function_exists('current_user_can') && !current_user_can('manage_options')) die('Доступ запрещен');
				 					if(function_exists('check_admin_referer')) {
            		 					check_admin_referer('aphorism_add_form');
        				  }
									
									$aphorism_text 			= mysql_real_escape_string($_POST['aphorism_text']);
									$aphorism_author		= mysql_real_escape_string($_POST['aphorism_author']);
									
									$wpdb->insert(
												 $table_aphorisms,
												 array('quote' => $aphorism_text, 'author' => $aphorism_author),
												 array('%s', '%s')
									);
				 }
									
				 echo "<form name='add_aphorism' method='post' action='".$_SERVER['PHP_SELF']."?page=aphorism&amp;updated=true'>";
				 if (function_exists('wp_nonce_field') )
				 {
				 			 wp_nonce_field('aphorism_add_form'); 
				 }
				 
				 echo
				 "
				 <table>
				 			<tr>
									<td>" . __('Aphorism text:', 'random_aphorism') . "</td>
									<td colspan='2'><textarea name='aphorism_text' cols='109' rows='5'></textarea></td>
							</tr><tr>
								  <td>" . __('Author:', 'random_aphorism') . "</td>
									<td><input type='text' size='50' name='aphorism_author' value=''></td>
									<td align='right'><input type='submit' name='aphorism_add_btn' value='" . __('Add', 'random_aphorism') . "'></td>
									</tr>
				 		  </table>
				 </form>
				 ";
}

/**************ВЫВОД И УДАЛЕНИЕ АФОРИЗМОВ В НАСТРОЙКАХ***************/
function aphorism_show_added()
{
 				 global $wpdb;
				 $table_aphorisms = $wpdb->prefix.aphorisms;
				 
				 if(isset($_POST['aphorism_delete_btn'])) {
				 				 if(function_exists('current_user_can') && !current_user_can('manage_options')) die('Доступ запрещен');
								 if(function_exists('check_admin_referer')) {
            		 					check_admin_referer('aphorism_delete_form');
        				 }
								 $aphorism_delete_id = $_POST['aphorism_delete_id'];
								 $sql = "DELETE FROM `".$table_aphorisms."` WHERE `id`=".$aphorism_delete_id.";";
								 $wpdb->query($sql);
				 }
				 
				 $aphorisms = $wpdb->get_results("SELECT * FROM `".$table_aphorisms."`;");
				 
				 if(!$aphorisms) {
				 	echo "<span class='description'>" . __("You don't have any added aphorisms yet", 'random_aphorism') . "</span>";
				 	return;
				 }

				 foreach ($aphorisms as $item)
				 {
								 echo "<form name='delete_aphorism_".$item->id."' method='post' action='".$_SERVER['PHP_SELF']."?page=aphorism&amp;updated=true'>";
				 				 if (function_exists('wp_nonce_field') )
				 				 {
				 				 			wp_nonce_field('aphorism_delete_form'); 
				 				 }
								 
								 echo
								 "
								 <table style='background-color: #cbcbcb; border: 0px; width: 50%; margin: 5px'>
								 			<tr><td>
											<p align=left>".$item->quote."</p>
											<p align=left>".$item->author."</p>
											</td><td>
											<div style='float: right;'>
													 <input type='hidden' name='aphorism_delete_id' value='".$item->id."'>
													 <input type='submit' name='aphorism_delete_btn' value='" . __('Delete', 'random_aphorism') . "'>
											</div>
											</td></tr>
								 </table>
								 </form>
								 ";
								 
				 }
}

/**************ВЫВОД ДРУГИХ НАСТРОЕК ПЛАГИНА***************/
function aphorism_show_options()
{					 
	echo "<p class='description'>" . __("You can show the random aphorism using shortcode <strong>[aphorism]</strong> in Posts or Pages, or hardcode it in PHP code using <strong>&lt;?php do_shortcode('[aphorism]'); ?&gt;</strong>.", 'random_aphorism') . "</p>";	
}

/**************УДАЛЕНИЕ ПЛАГИНА***************/
function aphorism_uninstall()
{
 				 global $wpdb;
				 $table_aphorisms = $wpdb->prefix.aphorisms;
				 
				 $sql = "DROP TABLE `".$table_aphorisms."`;";
				 $wpdb->query($sql);

				 remove_shortcode('aphorism');
}

/**************РЕГИСТРАЦИЯ ХУКОВ WORDPRESS***************/
register_activation_hook( __FILE__, 'aphorism_install');
register_deactivation_hook(__FILE__, 'aphorism_uninstall');
add_action('admin_menu', 'aphorism_add_admin_page');
add_action('wp_head', 'aphorism_load_styles');
add_action('init', 'aphorism_textdomain');
add_shortcode('aphorism', 'aphorism_show');
?>