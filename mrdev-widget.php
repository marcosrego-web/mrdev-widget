<?php
 /*
	Plugin Name: Mr.Dev.'s Widget
	Plugin URI:  https://marcosrego.com/web-en/mrdev-en
	Description: Mr.Dev. is your provider of developing tools! He gives you a powerful widget to display your content with many customizable layouts and options.
	Version:     0.9.436
	Author:      Marcos Rego
	Author URI:  https://marcosrego.com
	License:     GNU Public License version 2 or later
	License URI: http://www.gnu.org/licenseses/gpl-2.0.html
*/
/* Copyright 2021 Mr.Dev. by Marcos Rego (email : web@marcosrego.com)
Mr.Dev. is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
Mr.Dev. is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with Mr.Dev. If not, see http://www.gnu.org/licenseses/gpl-2.0.html.
*/
defined('ABSPATH') or die;
require 'tools/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://github.com/marcosrego-web/mrdev-widget/',
	__FILE__,
	'mrdev-widget'
);
global $mrdev_config_betatest;
if($mrdev_config_betatest === 1) {
	$myUpdateChecker->setBranch('develop');
} else {
	$myUpdateChecker->setBranch('master');
}
if(is_admin()) {
	$url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	if (strpos($url,'taxonomy=category') !== false || strpos($url,'taxonomy=post_tag') !== false) {
		function mrdev_add_style() {
			/*---Corrects the Visual Term Description Editor appearing above the categories list in some resolutions---*/
			wp_enqueue_style( 'mrdevWidget_admin', plugin_dir_url( __DIR__ ).'mrdev-widget/assets/css/admin.css',array(),'0.9.436');
		}
		add_action('admin_footer', 'mrdev_add_style');
	}
}
// Disables the block editor from managing widgets in the Gutenberg plugin.
add_filter( 'gutenberg_use_widgets_block_editor', '__return_false' );
// Disables the block editor from managing widgets.
add_filter( 'use_widgets_block_editor', '__return_false' );
/*---Clean Mr.Dev. - Widget HTML cache on post save---*/
function mrdev_cleanwidgetcache() {
	$cached_html_files = glob(WP_CONTENT_DIR.'/cache/mrdev/widgets/html/*');
	foreach($cached_html_files as $cached_file){
	  if (is_file($cached_file) && strpos($cached_file, 'index') === false) {
		unlink($cached_file);
	  }
	}
}
add_action( 'save_post', 'mrdev_cleanwidgetcache' );
add_action( 'edit_term', 'mrdev_cleanwidgetcache');

function mrdev_load_widget() {
    register_widget( 'mr_developer' );
}
add_action( 'widgets_init', 'mrdev_load_widget' );
class mr_developer extends WP_Widget {
	function __construct() {
		parent::__construct(
			'mr_developer', 
			__('Mr.Dev.', 'mr_developer'), 
			array( 
				'description' => __( 'Displays categories, pages, posts, tags, custom items and more in a variety of layouts and custom options.', 'mr_developer'),
			)
		);
	}
	/*------WIDGET FRONT------*/
	public function widget( $args, $instance ) {
		$lang =	get_locale();
		$cache_dir = WP_CONTENT_DIR.'/cache';
		$cache_url = WP_CONTENT_URL.'/cache';
		if(isset($instance['widgetid'])) {
			$widgetid = htmlspecialchars($instance['widgetid']);//isset($getpluginsettings['mrdeveloper_content']) ? $getpluginsettings['mrdeveloper_content'] : '';
			$theme = htmlspecialchars($instance['theme']);
			$technical = array_filter($instance['technical'], 'is_numeric');
			$groupexclude = array_map("htmlspecialchars", $instance['groupexclude']);
			$taxonomies_groupexclude = array_map("htmlspecialchars", $instance['taxonomies_groupexclude']);
			$posttypes_groupexclude = array_map("htmlspecialchars", $instance['posttypes_groupexclude']);
			if($theme == 'none') {
				$layout = 'None';
				$layoutoptions = array();
			} else {
				$layout = htmlspecialchars($instance['layout']);
				$layoutoptions = array_map("htmlspecialchars",$instance['layoutoptions']);
			}
			/*
			Main title depends of the selected display option.
			Widget title is the default.
			*/
			$parentcats = array_filter($instance['parentcats'], 'is_numeric');
			if(isset($args['before_widget'])) {
				echo $args['before_widget'];
			}
			/*
			Check if html cache exists.
			*/
			if(file_exists($cache_dir.'/mrdev/widgets/html/'.$widgetid.'_'.$lang)) {
				$content = file_get_contents($cache_dir.'/mrdev/widgets/html/'.$widgetid.'_'.$lang);
			} else {
				global $mrdev_widget_contentoverride;
				/*--- Get all instances into variables ---*/
				$perline = intval($instance['perline']);
				$perpage = intval($instance['perpage']);
				$autoplay = intval($instance['autoplay']);
				$pagetransition = htmlspecialchars($instance['pagetransition']);
				$tabs = intval($instance['tabs']);
				$tabsposition = htmlspecialchars($instance['tabsposition']);
				$pagetoggles = array_filter($instance['pagetoggles'], 'is_numeric');
				$globallayoutoptions = array_map("htmlspecialchars", $instance['globallayoutoptions']);
				$contenttypes = htmlspecialchars($instance['contenttypes']);
				$parenttags = array_filter($instance['parenttags'], 'is_numeric');
				$parentpages = array_filter($instance['parentpages'], 'is_numeric');
				$orderby = intval($instance['orderby']);
				$order = intval($instance['order']);
				$manualordering = array_map("htmlspecialchars", $instance['manualordering']);
				$pageorder = array_map("htmlspecialchars", $instance['pageorder']);
				$pin = array_filter($instance['pin'], 'is_numeric');
				$excludeinclude = intval($instance['excludeinclude']);
				$itemselect = array_filter($instance['itemselect'], 'is_numeric');
				$backgroundcolor = htmlspecialchars($instance['backgroundcolor']);
				$titlescolor = htmlspecialchars($instance['titlescolor']);
				$descscolor = htmlspecialchars($instance['descscolor']);
				$titlesfont = htmlspecialchars($instance['titlesfont']);
				$descsfont = htmlspecialchars($instance['descsfont']);
				$titlessize = htmlspecialchars($instance['titlessize']);
				$descssize = htmlspecialchars($instance['descssize']);
				$titleoverride = array_map("htmlspecialchars", $instance['titleoverride']);
				$dateoverride = array_map("htmlspecialchars", $instance['dateoverride']);
				$authoroverride = array_filter($instance['authoroverride'], 'is_numeric');
				$textoverride = array_map("htmlspecialchars_decode", $instance['textoverride']);
				$imageoverride = array_map("htmlspecialchars", $instance['imageoverride']);
				$linkoverride = array_map("htmlspecialchars", $instance['linkoverride']);
				$bottomlinkoverride = array_map("htmlspecialchars", $instance['bottomlinkoverride']);
				$itemlinktargetoverride = array_map("htmlspecialchars", $instance['itemlinktargetoverride']);
				$itembackgroundcolor = array_map("htmlspecialchars", $instance['backgroundoverride']);
				$itemtitlescolor = array_map("htmlspecialchars", $instance['titlescoloroverride']);
				$itemdescscolor = array_map("htmlspecialchars", $instance['desccoloroverride']);
				$itemtitlesfont = array_map("htmlspecialchars", $instance['titlesfontoverride']);
				$itemdescsfont = array_map("htmlspecialchars", $instance['descsfontoverride']);
				$itemtitlessize = array_map("htmlspecialchars", $instance['titlessizeoverride']);
				$itemdescssize = array_map("htmlspecialchars", $instance['descssizeoverride']);
				$itemsnumber = intval($instance['itemsnumber']);
				$bottomlink = htmlspecialchars($instance['bottomlink']);
				$imagestypes = array_map("htmlspecialchars", $instance['imagestype']);
				$itemimage = intval($instance['itemimage']);
				$fallbackimage = htmlspecialchars($instance['fallbackimage']);
				$imagemaxwidth = intval($instance['imagemaxwidth']);
				$imagemaxheight = intval($instance['imagemaxheight']);
				$itemstitle = intval($instance['itemstitle']);
				$itemstitlemax = intval($instance['itemstitlemax']);
				$itemdatelabel = htmlspecialchars($instance['itemdatelabel']);
				$itemsdate = intval($instance['itemsdate']);
				$itemauthorlabel = htmlspecialchars($instance['itemauthorlabel']);
				$itemsauthor = intval($instance['itemsauthor']);
				$itemtaxonomieslabel = htmlspecialchars($instance['itemtaxonomieslabel']);
				$itemstaxonomies = intval($instance['itemstaxonomies']);
				$itemdesc = intval($instance['itemdesc']);
				$itemdescmax = intval($instance['itemdescmax']);
				$itemlink = intval($instance['itemlink']);
				$itemlinktarget = htmlspecialchars($instance['itemlinktarget']);
				$itemoptions = array_map("htmlspecialchars",$instance['itemoptions']);
				$titletag = htmlspecialchars($instance['titletag']);
				$bottomlinkclasses = htmlspecialchars($instance['bottomlinkclasses']);
				$widgetclasses = htmlspecialchars($instance['widgetclasses']);
				$customcss = htmlspecialchars($instance['customcss']);
				$lastactivedetails = htmlspecialchars($instance['lastactivedetails']);
				$getpluginsettings = get_option('mrdev-widget-options');
				if($theme == "default") {
					include plugin_dir_path( __DIR__ ).'mrdev-widget/themes/'.$theme.'/index.php';
				} else if($theme == "none") {
				} else {
					include ABSPATH.'wp-content/themes/mrdev/widget/themes/'.$theme.'/index.php';
				}
				require trailingslashit( plugin_dir_path( __FILE__ )).'/items.php';
				if(file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/cache.php')) {
					include trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/cache.php';
				}
			}
			/*
			Check if a Javascript cache exists. If not then load the entire javascript.
			Only load one javascript to avoid repetition and conflicts.
			*/
			if(!wp_script_is('mrdev_utils','registered')) {
				wp_register_script( 'mrdev_utils', plugin_dir_url( __DIR__ ).'mrdev-widget/tools/mr-utils/js/utils.js',array(),'0.9.436');
			}
			if(file_exists($cache_dir.'/mrdev/widgets/js/'.$widgetid.'.js') && !wp_script_is('mrdev_widget')) {
				wp_enqueue_script( 'mrdev_widget', $cache_url.'/mrdev/widgets/js/'.$widgetid.'.js', array('mrdev_utils'),'0.9.436');
			} else {
				wp_deregister_script('mrdev_widget');
				wp_enqueue_script( 'mrdev_widget', plugin_dir_url( __DIR__ ).'mrdev-widget/assets/js/widget.js', array('mrdev_utils'),'0.9.436');
			}
			/*
			Check if a css cache exists. If not then load the entire theme's css.
			A css file with the theme's name is mandatory.
			*/
			if(!wp_style_is('mrdev_utils','registered')) {
				wp_register_style( 'mrdev_utils', plugin_dir_url( __DIR__ ).'mrdev-widget/tools/mr-utils/css/utils.css',array(),'0.9.436');
			}
			/*global ${"mrdev_breakpoint_desktop"},${"mrdev_breakpoint_tablet"},${"mrdev_breakpoint_phone"};
			if(!wp_style_is('mrdev_utils_desktop','registered')) {
				if(empty(${"mrdev_breakpoint_desktop"})) {
					${"mrdev_breakpoint_desktop"} = '(min-width: 1200px) and (max-width: 100vw)';
				}
				wp_register_style( 'mrdev_utils_desktop', plugin_dir_url( __DIR__ ).'mrdev-widget/tools/mr-utils/css/utils-desktop.css',array(),'0.9.436',${"mrdev_breakpoint_desktop"});
			}
			if(!wp_style_is('mrdev_utils_tablet','registered')) {
				if(empty(${"mrdev_breakpoint_tablet"})) {
					${"mrdev_breakpoint_tablet"} = '(min-width: 768px) and (max-width: 959px)';
				}
				wp_register_style( 'mrdev_utils_tablet', plugin_dir_url( __DIR__ ).'mrdev-widget/tools/mr-utils/css/utils-tablet.css',array(),'0.9.436',${"mrdev_breakpoint_tablet"});
			}
			if(!wp_style_is('mrdev_utils_phone','registered')) {
				if(empty(${"mrdev_breakpoint_phone"})) {
					${"mrdev_breakpoint_phone"} = '(min-width: 0px) and (max-width: 767px)';
				}
				wp_register_style( 'mrdev_utils_phone', plugin_dir_url( __DIR__ ).'mrdev-widget/tools/mr-utils/css/utils-phone.css',array(),'0.9.436',${"mrdev_breakpoint_phone"});
			}*/
			if(file_exists($cache_dir.'/mrdev/widgets/css/'.$widgetid.'.css')) {
				wp_enqueue_style( $widgetid.'_css', $cache_url.'/mrdev/widgets/css/'.$widgetid.'.css', array('mrdev_utils'),'0.9.436');
			} else {
				wp_enqueue_style( 'mrdev_widget', plugin_dir_url( __DIR__ ).'mrdev-widget/assets/css/widget.css', array('mrdev_utils'),'0.9.436');
				if($theme == "default") {
					//Official Themes
					wp_enqueue_style( 'mrdev_'.$theme.'_css', plugin_dir_url( __DIR__ ).'mrdev-widget/themes/'.$theme.'/'.$theme.'.css',array('mrdev_widget'),'0.9.436');
				} else if($theme == "none") {
				} else {
					//Custom Themes
					wp_enqueue_style( 'mrdev_'.$theme.'_css', get_home_url().'/wp-content/themes/mrdev/widget/themes/'.$theme.'/'.$theme.'.css',array('mrdev_widget'),'0.9.436');
				}
				/*
				Custom CSS
				*/
				if(file_exists($cache_dir.'/mrdev/widgets/css/'.$widgetid.'_custom.css')) {
					wp_enqueue_style( $widgetid.'_customcss', $cache_url.'/mrdev/widgets/css/'.$widgetid.'_custom.css','0.9.436');
				}
			}
			echo __( $content, 'mr_developer' );
			if(isset($args['after_widget'])) {
				echo $args['after_widget'];
			}
		}
	}
	/*------WIDGET ADMIN------*/
	public function form( $instance ) {
		global $mrdev_widget_content,$mrdev_widget_content_access,$mrdev_get_user,$mrdev_get_username,$mrdev_get_userrole,$mrdev_widget_contentoverride;
		//$widgetoptions = $this->get_settings()[$this->number];
		$getpluginsettings = get_option('mrdev-widget-options');
		$appearance_access = isset($getpluginsettings['mrdeveloper_appearance']) ? $getpluginsettings['mrdeveloper_appearance'] : '';
		$pagination_access = isset($getpluginsettings['mrdeveloper_pagination']) ? $getpluginsettings['mrdeveloper_pagination'] : '';
		$display_access = isset($getpluginsettings['mrdeveloper_display']) ? $getpluginsettings['mrdeveloper_display'] : '';
		$options_access = isset($getpluginsettings['mrdeveloper_options']) ? $getpluginsettings['mrdeveloper_options'] : '';
		$advanced_access = isset($getpluginsettings['mrdeveloper_advanced']) ? $getpluginsettings['mrdeveloper_advanced'] : '';
		if(!isset($mrdev_get_user)) {
			$mrdev_get_user = wp_get_current_user();
		}
		if(!isset($mrdev_get_userrole)) {
			$mrdev_get_userrole = $mrdev_get_user->roles[0];
		}
		if(!isset($mrdev_get_username)) {
			$mrdev_get_username = $mrdev_get_user->user_login;
		}
		if(!isset($mrdev_widget_contentoverride)) {
			$mrdev_widget_contentoverride = 0;
		}
		if(!isset($mrdev_widget_content_access)) {
			if(!is_array( $mrdev_widget_content ) && $mrdev_widget_content == $mrdev_get_username || is_array( $mrdev_widget_content ) && in_array( $mrdev_get_username , $mrdev_widget_content ) || !is_array( $mrdev_widget_content ) && $mrdev_widget_content == $mrdev_get_userrole || is_array( $mrdev_widget_content ) && in_array( $mrdev_get_userrole , $mrdev_widget_content )) {
				$mrdev_widget_content_access = 'Denied';
			} else {
				$mrdev_widget_content_access = 'Allowed';
			}
		}
		if(!is_array( $appearance_access ) && $appearance_access == $mrdev_get_username || is_array( $appearance_access ) && in_array( $mrdev_get_username , $appearance_access ) || !is_array( $appearance_access ) && $appearance_access == $mrdev_get_userrole || is_array( $appearance_access ) && in_array( $mrdev_get_userrole , $appearance_access )) {
			$appearance_access = 'Denied';
		} else {
			$appearance_access = 'Allowed';
		}
		if(!is_array( $pagination_access ) && $pagination_access == $mrdev_get_username || is_array( $pagination_access ) && in_array( $mrdev_get_username , $pagination_access ) || !is_array( $pagination_access ) && $pagination_access == $mrdev_get_userrole || is_array( $pagination_access ) && in_array( $mrdev_get_userrole , $pagination_access )) {
			$pagination_access = 'Denied';
		} else {
			$pagination_access = 'Allowed';
		}
		if(!is_array( $display_access ) && $display_access == $mrdev_get_username || is_array( $display_access ) && in_array( $mrdev_get_username , $display_access ) || !is_array( $display_access ) && $display_access == $mrdev_get_userrole || is_array( $display_access ) && in_array( $mrdev_get_userrole , $display_access )) {
			$display_access = 'Denied';
		} else {
			$display_access = 'Allowed';
		}
		if(!is_array( $options_access ) && $options_access == $mrdev_get_username || is_array( $options_access ) && in_array( $mrdev_get_username , $options_access ) || !is_array( $options_access ) && $options_access == $mrdev_get_userrole || is_array( $options_access ) && in_array( $mrdev_get_userrole , $options_access )) {
			$options_access = 'Denied';
		} else {
			$options_access = 'Allowed';
		}
		if(!is_array( $advanced_access ) && $advanced_access == $mrdev_get_username || is_array( $advanced_access ) && in_array( $mrdev_get_username , $advanced_access ) || !is_array( $advanced_access ) && $advanced_access == $mrdev_get_userrole || is_array( $advanced_access ) && in_array( $mrdev_get_userrole , $advanced_access )) {
			$advanced_access = 'Denied';
		} else {
			$advanced_access = 'Allowed';
		}
		wp_enqueue_style( 'mrdevWidget_admin', plugin_dir_url( __DIR__ ).'mrdev-widget/assets/css/admin.css',array(),'0.9.436');
		?>
		<div class="mr-admin">
		<p class="mr-section"><a href="https://marcosrego.com/web-en/mrdev-en/" target="_blank">
		<img style="margin-bottom: -7px;" src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjI0cHgiIGhlaWdodD0iMjRweCIgdmlld0JveD0iMCAwIDQzLjg0NSA0My44NDUiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDQzLjg0NSA0My44NDU7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8cGF0aCBkPSJNMjQuNTUyLDI5LjQ5MWMwLDAuNTUzLTEuMTE3LDEtMi41LDFjLTEuMzgxLDAtMi41LTAuNDQ3LTIuNS0xczEuMTE5LTEsMi41LTFDMjMuNDM1LDI4LjQ5MSwyNC41NTIsMjguOTM5LDI0LjU1MiwyOS40OTF6ICAgIE00My41NTIsMjcuMjgyYzAsMi45MzgtMS44NDgsNS40MzYtNC4zODksNi4yNjhjLTMuMjU4LDYuMjU1LTkuODExLDEwLjI5NS0xNy4wOCwxMC4yOTVjLTcuMjYzLDAtMTMuODA3LTQuMDI4LTE3LjA2OC0xMC4yNzEgICBjLTIuNTc4LTAuODA3LTQuNDYzLTMuMzIxLTQuNDYzLTYuMjkxYzAtMS44MTUsMC43MS0zLjQ1NywxLjg0OC00LjY0NmMtMC44LTAuODE5LTEuNDA3LTEuODY2LTEuNzYxLTMuMDk0ICAgYy0wLjU5OS0yLjA3Ny0wLjQxNi00LjQxMSwwLjUxNi02LjU3MmMxLjExMy0yLjU4NiwzLjE2NC00LjUzLDUuNDY2LTUuMzM1YzAuNTItNS43NTcsMTAuMjQ4LTcuODIyLDE2LjY4Mi03LjYyMiAgIGM5LjA5MywwLjI4NCwxMS4yMjQsNC40MTgsMTEuNjAyLDcuMjgxYzIuOTE2LDAuMzI2LDUuNjg1LDIuNTExLDcuMDQ3LDUuNjc2YzAuOTMxLDIuMTYxLDEuMTE0LDQuNDk1LDAuNTE3LDYuNTcyICAgYy0wLjI4MywwLjk4My0wLjczMSwxLjg1MS0xLjMwOSwyLjU3OUM0Mi42MTQsMjMuMzIxLDQzLjU1MiwyNS4xOTEsNDMuNTUyLDI3LjI4MnogTTI0LjcxNCwzOC41OTJMMjQuMjQsMzYuNDdoLTQuMzQ2ICAgbC0wLjQ4NywyLjExM2MwLjY2Ni0wLjI5OSwxLjYwMi0wLjQ4OCwyLjY0NS0wLjQ4OEMyMy4xMDYsMzguMDk1LDI0LjA0NiwzOC4yODgsMjQuNzE0LDM4LjU5MnogTTM5LjU1MiwyNy4yODIgICBjMC0xLjQxNi0wLjk5OC0yLjU2My0yLjI0LTIuNTYzYy0wLjAwMSwwLTAuMDA0LDAtMC4wMDYsMC4wMDFjLTAuMzQsMC41NDctMC43MjksMS4wNzQtMS4xNywxLjU3NSAgIGMwLjI3Ny0wLjg5NiwwLjQ2MS0xLjgxLDAuNTYxLTIuNzI2Yy0wLjAwMS0wLjAxNS0wLjAwMy0wLjAyNi0wLjAwNC0wLjAzOWMtMS4zMDMtMC42OTQtMi42NDEtMi4xOTItMy41NjEtNC4xNjYgICBjLTAuOTU5LTIuMDU5LTEuMjMzLTQuMTMzLTAuODY0LTUuNTg4Yy0zLjY5OSwyLjM0Ni05Ljk2NCwxLjIzMi05Ljk2NCwxLjIzMmMtMy43NSwwLjI1LTQuNzUsNS00Ljc1LDUgICBjLTEuNzM5LTAuMzg3LTMuNTUtMS41OTYtNS4xMjItMi45MzRjLTAuMTY5LDAuNzQ0LTAuNDMsMS41MTctMC43OSwyLjI4N2MtMS4wNzMsMi4zMDItMi43MTMsMy45NTctNC4yMDIsNC40NDIgICBjMC4xMDUsMC44MzYsMC4yNzgsMS42NywwLjUzMSwyLjQ4N2MtMC40NDEtMC41MDEtMC44MzEtMS4wMjgtMS4xNy0xLjU3NWMtMC4wMDIsMC0wLjAwMy0wLjAwMS0wLjAwNS0wLjAwMSAgIGMtMS4yNDQsMC0yLjI0MiwxLjE0Ny0yLjI0MiwyLjU2M3MwLjk5OCwyLjU2MiwyLjI0MiwyLjU2MmMwLjMxMSwwLDAuNjA1LTAuMDcxLDAuODc0LTAuMjAxYzAuMjcxLDAuNzkxLDAuNjEyLDEuNTUyLDEuMDA3LDIuMjc3ICAgYzIuMjI2LDAuNzg2LDUuNDcsMi4zNDQsNy4wMTIsNC45OTNsMC42NjctMi44OTVjMC4yMDktMC45MDcsMS4wMTctMS41NTIsMS45NDktMS41NTJoNy41NDFjMC45MzgsMCwxLjc1LDAuNjQ5LDEuOTUzLDEuNTY1ICAgbDAuNjM2LDIuODUzYzEuNTY1LTIuNjY1LDQuODU0LTQuMjIsNy4wNzUtNC45OTVjMC4zODYtMC43MTEsMC43MTgtMS40NTUsMC45ODMtMi4yMjljMC4yNTQsMC4xMTMsMC41MjgsMC4xODIsMC44MTcsMC4xODIgICBDMzguNTU0LDI5Ljg0NSwzOS41NTIsMjguNjk2LDM5LjU1MiwyNy4yODJ6IE0xNS4yNiwyMS43NjFjLTEuMjQxLDAtMi4yNSwxLjAxLTIuMjUsMi4yNWMwLDEuMjQsMS4wMDksMi4yNSwyLjI1LDIuMjUgICBjMS4yNDEsMCwyLjI1LTEuMDEsMi4yNS0yLjI1QzE3LjUxLDIyLjc3MSwxNi41MDIsMjEuNzYxLDE1LjI2LDIxLjc2MXogTTI4Ljg0NSwyMS43NjFjLTEuMjQsMC0yLjI1LDEuMDEtMi4yNSwyLjI1ICAgYzAsMS4yNCwxLjAxLDIuMjUsMi4yNSwyLjI1czIuMjUtMS4wMSwyLjI1LTIuMjVDMzEuMDk1LDIyLjc3MSwzMC4wODUsMjEuNzYxLDI4Ljg0NSwyMS43NjF6IiBmaWxsPSIjMDAwMDAwIi8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" alt="Mr.Dev. Logo" title="Icon made by Freepik from flaticon.com"/> <strong style="font-weight:700" title="Click to know Mr.Dev.">Mr.Dev.</strong></a>
		</p>
		<?php
			if ( isset( $instance[ 'title' ] ) ) {
				$title = $instance[ 'title' ];
			} else {
				$title = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'theme' ] ) ) {
				$theme = $instance[ 'theme' ];
			} else {
				$theme = __( 'default', 'mr_developer' );
			}
			if ( isset( $instance[ 'layout' ] ) ) {
				$layout = $instance[ 'layout' ];
			} else {
				$layout = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'backgroundcolor' ] ) ) {
				$backgroundcolor = $instance[ 'backgroundcolor' ];
			} else {
				$backgroundcolor = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'titlescolor' ] ) ) {
				$titlescolor = $instance[ 'titlescolor' ];
			} else {
				$titlescolor = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'descscolor' ] ) ) {
				$descscolor = $instance[ 'descscolor' ];
			} else {
				$descscolor = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'titlesfont' ] ) ) {
				$titlesfont = $instance[ 'titlesfont' ];
			} else {
				$titlesfont = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'descsfont' ] ) ) {
				$descsfont = $instance[ 'descsfont' ];
			} else {
				$descsfont = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'titlessize' ] ) ) {
				$titlessize = $instance[ 'titlessize' ];
			} else {
				$titlessize = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'descssize' ] ) ) {
				$descssize = $instance[ 'descssize' ];
			} else {
				$descssize = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'imagestype' ] ) ) {
				$imagestypes = $instance[ 'imagestype' ];
			} else {
				$imagestypes = __( array('background'), 'mr_developer' );
			}
			if ( isset( $instance[ 'globallayoutoptions' ] ) ) {
				$globallayoutoptions = $instance[ 'globallayoutoptions' ];
			} else {
				$globallayoutoptions = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'layoutoptions' ] ) ) {
				$layoutoptions = $instance[ 'layoutoptions' ];
			} else {
				$layoutoptions = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'contenttypes' ] ) ) {
				$contenttypes = $instance[ 'contenttypes' ];
			} else {
				$contenttypes = __( 'taxonomy_category', 'mr_developer' );
			}
			if ( isset( $instance[ 'parentcats' ] ) ) {
				$parentcats = $instance[ 'parentcats' ];
			} else {
				$parentcats = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'parenttags' ] ) ) {
				$parenttags = $instance[ 'parenttags' ];
			} else {
				$parenttags = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'parentpages' ] ) ) {
				$parentpages = $instance[ 'parentpages' ];
			} else {
				$parentpages = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'orderby' ] ) ) {
				$orderby = $instance[ 'orderby' ];
			} else {
				$orderby = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'order' ] ) ) {
				$order = $instance[ 'order' ];
			} else {
				$order = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'manualordering' ] ) ) {
				$manualordering= $instance[ 'manualordering' ];
			} else {
				$manualordering= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'pageorder' ] ) ) {
				$pageorder= $instance[ 'pageorder' ];
			} else {
				$pageorder= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'pin' ] ) ) {
				$pin = $instance[ 'pin' ];
			} else {
				$pin = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'posttypes_groupexclude' ] ) ) {
				$posttypes_groupexclude = $instance[ 'posttypes_groupexclude' ];
			} else {
				$posttypes_groupexclude = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'taxonomies_groupexclude' ] ) ) {
				$taxonomies_groupexclude = $instance[ 'taxonomies_groupexclude' ];
			} else {
				$taxonomies_groupexclude = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'groupexclude' ] ) ) {
				$groupexclude = $instance[ 'groupexclude' ];
			} else {
				$groupexclude = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'excludeinclude' ] ) ) {
				$excludeinclude = $instance[ 'excludeinclude' ];
			} else {
				$excludeinclude = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'itemselect' ] ) ) {
				$itemselect = $instance[ 'itemselect' ];
			} else {
				$itemselect = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'titleoverride' ] ) ) {
				$titleoverride= $instance[ 'titleoverride' ];
			} else {
				$titleoverride= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'dateoverride' ] ) ) {
				$dateoverride= $instance[ 'dateoverride' ];
			} else {
				$dateoverride= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'authoroverride' ] ) ) {
				$authoroverride= $instance[ 'authoroverride' ];
			} else {
				$authoroverride= __( array(0), 'mr_developer' );
			}
			if ( isset( $instance[ 'textoverride' ] ) ) {
				$textoverride= $instance[ 'textoverride' ];
			} else {
				$textoverride= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'imageoverride' ] ) ) {
				$imageoverride= $instance[ 'imageoverride' ];
			} else {
				$imageoverride= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'linkoverride' ] ) ) {
				$linkoverride= $instance[ 'linkoverride' ];
			}
			else {
				$linkoverride= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'bottomlinkoverride' ] ) ) {
				$bottomlinkoverride= $instance[ 'bottomlinkoverride' ];
			}
			else {
				$bottomlinkoverride= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'itemlinktargetoverride' ] ) ) {
				$itemlinktargetoverride= $instance[ 'itemlinktargetoverride' ];
			}
			else {
				$itemlinktargetoverride= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'backgroundoverride' ] ) ) {
				$itembackgroundcolor= $instance[ 'backgroundoverride' ];
			}
			else {
				$itembackgroundcolor= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'titlescoloroverride' ] ) ) {
				$itemtitlescolor= $instance[ 'titlescoloroverride' ];
			}
			else {
				$itemtitlescolor= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'desccoloroverride' ] ) ) {
				$itemdescscolor= $instance[ 'desccoloroverride' ];
			}
			else {
				$itemdescscolor= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'titlesfontoverride' ] ) ) {
				$itemtitlesfont= $instance[ 'titlesfontoverride' ];
			}
			else {
				$itemtitlesfont= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'descsfontoverride' ] ) ) {
				$itemdescsfont= $instance[ 'descsfontoverride' ];
			}
			else {
				$itemdescsfont= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'titlessizeoverride' ] ) ) {
				$itemtitlessize= $instance[ 'titlessizeoverride' ];
			}
			else {
				$itemtitlessize= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'descssizeoverride' ] ) ) {
				$itemdescssize= $instance[ 'descssizeoverride' ];
			}
			else {
				$itemdescssize= __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'itemsnumber' ] ) ) {
				$itemsnumber = $instance[ 'itemsnumber' ];
			}
			else {
				$itemsnumber = __( '', 'mr_developer' );
			}
			if(!$itemsnumber && $contenttypes == 'custom_items') { 
				$itemsnumber = 1;
			}
			if ( isset( $instance[ 'maintitle' ] ) ) {
				$maintitle = $instance[ 'maintitle' ];
			}
			else {
				$maintitle = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'itemimage' ] ) ) {
				$itemimage = $instance[ 'itemimage' ];
			}
			else {
				$itemimage = __( 1, 'mr_developer' );
			}
			if ( isset( $instance[ 'fallbackimage' ] ) ) {
				$fallbackimage = $instance[ 'fallbackimage' ];
			}
			else {
				$fallbackimage = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'imagemaxwidth' ] ) ) {
				$imagemaxwidth = $instance[ 'imagemaxwidth' ];
			} else {
				$imagemaxwidth = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'imagemaxheight' ] ) ) {
				$imagemaxheight = $instance[ 'imagemaxheight' ];
			}
			else {
				$imagemaxheight = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'itemstitle' ] ) ) {
				$itemstitle = $instance[ 'itemstitle' ];
			} else {
				$itemstitle = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'itemstitlemax' ] ) ) {
				$itemstitlemax = $instance[ 'itemstitlemax' ];
			} else {
				$itemstitlemax = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'itemdatelabel' ] ) ) {
				$itemdatelabel = $instance[ 'itemdatelabel' ];
			} else {
				$itemdatelabel = __( 'Written on ', 'mr_developer' );
			}
			if ( isset( $instance[ 'itemsdate' ] ) ) {
				$itemsdate = $instance[ 'itemsdate' ];
			} else {
				$itemsdate = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'itemauthorlabel' ] ) ) {
				$itemauthorlabel = $instance[ 'itemauthorlabel' ];
			} else {
				$itemauthorlabel = __( 'by ', 'mr_developer' );
			}
			if ( isset( $instance[ 'itemsauthor' ] ) ) {
				$itemsauthor = $instance[ 'itemsauthor' ];
			} else {
				$itemsauthor = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'itemtaxonomieslabel' ] ) ) {
				$itemtaxonomieslabel = $instance[ 'itemtaxonomieslabel' ];
			} else {
				$itemtaxonomieslabel = __( 'in ', 'mr_developer' );
			}
			if ( isset( $instance[ 'itemstaxonomies' ] ) ) {
				$itemstaxonomies = $instance[ 'itemstaxonomies' ];
			} else {
				$itemstaxonomies = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'itemdesc' ] ) ) {
				$itemdesc = $instance[ 'itemdesc' ];
			} else {
				$itemdesc = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'itemdescmax' ] ) ) {
				$itemdescmax = $instance[ 'itemdescmax' ];
			} else {
				$itemdescmax = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'itemlink' ] ) ) {
				$itemlink = $instance[ 'itemlink' ];
			} else {
				$itemlink = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'itemlinktarget' ] ) ) {
				$itemlinktarget = $instance[ 'itemlinktarget' ];
			} else {
				$itemlinktarget = __( 'self', 'mr_developer' );
			}
			if ( isset( $instance[ 'bottomlink' ] ) ) {
				$bottomlink = $instance[ 'bottomlink' ];
			} else {
				$bottomlink = __( 'Know more...', 'mr_developer' );
			}
			if ( isset( $instance[ 'perline' ] ) ) {
				$perline = $instance[ 'perline' ];
			}
			else {
				$perline = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'perpage' ] ) ) {
				$perpage = $instance[ 'perpage' ];
			}
			else {
				$perpage = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'autoplay' ] ) ) {
				$autoplay = $instance[ 'autoplay' ];
			}
			else {
				$autoplay = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'pagetransition' ] ) ) {
				$pagetransition = $instance[ 'pagetransition' ];
			}
			else {
				$pagetransition = __( 'fade', 'mr_developer' );
			}
			if ( isset( $instance[ 'pagetoggles' ] ) ) {
				$pagetoggles = $instance[ 'pagetoggles' ];
			} else {
				$pagetoggles = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'tabs' ] ) ) {
				$tabs = $instance[ 'tabs' ];
			} else {
				$tabs = __( 0, 'mr_developer' );
			}
			if ( isset( $instance[ 'tabsposition' ] ) ) {
				$tabsposition = $instance[ 'tabsposition' ];
			} else {
				$tabsposition = __( 'tabstop', 'mr_developer' );
			}
			if ( isset( $instance[ 'itemoptions' ] ) ) {
				$itemoptions = $instance[ 'itemoptions' ];
			} else {
				$itemoptions = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'technical' ] ) ) {
				$technical = $instance[ 'technical' ];
			} else {
				$technical = __( array(), 'mr_developer' );
			}
			if ( isset( $instance[ 'titletag' ] ) ) {
				$titletag = $instance[ 'titletag' ];
			} else {
				$titletag = __( 'h3', 'mr_developer' );
			}
			if ( isset( $instance[ 'bottomlinkclasses' ] ) ) {
				$bottomlinkclasses = $instance[ 'bottomlinkclasses' ];
			} else {
				$bottomlinkclasses = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'widgetclasses' ] ) ) {
				$widgetclasses = $instance[ 'widgetclasses' ];
			} else {
				$widgetclasses = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'customcss' ] ) ) {
				$customcss = $instance[ 'customcss' ];
			} else {
				$customcss = __( '', 'mr_developer' );
			}
			if ( isset( $instance[ 'lastactivedetails' ] ) ) {
				$lastactivedetails = $instance[ 'lastactivedetails' ];
			} else {
				$lastactivedetails = __( '', 'mr_developer' );
			}
			?>
			<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label><br>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<details class="widgetDetails" <?php if(esc_attr( $lastactivedetails ) == 'widgetDetails') { echo 'open="open"'; } if($mrdev_widget_content_access == 'Denied') { echo 'style="display:none; opacity: 0.5;"'; } ?>>
			<summary class="mr-section">Content</summary>
			<p>
			<label  for="<?php echo $this->get_field_id( 'contenttypes' ); ?>"><?php _e( 'Type:' ); ?></label><br>
			<select <?php if($mrdev_widget_content_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-contenttypes" id="<?php echo $this->get_field_id('contenttypes'); ?>" name="<?php echo $this->get_field_name('contenttypes'); ?>">
				<?php
					echo '<option value="taxonomy_category"', $contenttypes == 'taxonomy_category' ? ' selected="selected"' : '', '>Categories</option>
					<option value="posttype_post"', $contenttypes == 'posttype_post' ? ' selected="selected"' : '', '>Posts</option>';
					if(file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/contenttypes.php')) {
						include trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/contenttypes.php';
					}
				?>
			</select><br>
				</p>
				<p class="mr-sortcontainer" <?php if($contenttypes == 'custom_items') { echo 'style="display:none;';  } ?>>
				<label  for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Sort by:' ); ?></label><br>
						<select <?php if($mrdev_widget_content_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-halfsize" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
							<?php
								echo '<option value="0"', $orderby == 0 ? ' selected="selected"' : '', '>Creation</option>
								<option value="1"', $orderby == 1 ? ' selected="selected"' : '', '>Title</option>
								<option value="2"', $orderby == 2 ? ' selected="selected"' : '', '>Parent</option>
								<option value="3"', $orderby == 3 ? ' selected="selected"' : '', '>Post count</option>
								<option value="4"', $orderby == 4 ? ' selected="selected"' : '', '>Slug</option>';
							?>
						</select>
						<select <?php if($mrdev_widget_content_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-halfsize" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
							<?php
								echo '<option value="0"', $order == 0 ? ' selected="selected"' : '', '>Descending</option>
								<option value="1"', $order == 1 ? ' selected="selected"' : '', '>Ascending</option>';
							?>
				</select><br>
				</p>
				<p><?php _e( 'Items:' ); ?></p>
				<p class="mr-saveexcludeinclude" style="display: none;">
					<?php _e( 'Please save the changes when you want to see all the options considered by your selection.' ); ?>
				</p>
				<?php 
					if(file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/parents.php')) {
						include trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/parents.php';
					}
				?>
				<details class="mr-widget-itemscontainer" style="position: relative;" <?php if($contenttypes == 'custom_items') { echo 'active open'; } ?>>
				<summary style='cursor:pointer;' title="See and customize the items considered by your selected options."><?php _e( 'Customize content' ); ?><br></summary>
				<p class="mr-heading">	
					<label  for="<?php echo $this->get_field_id( 'manualordering' ); ?>"><?php _e( 'Order:' ); ?></label>
				<?php 
					if(file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/orderpin.php')) {
				?>
					<label  for="<?php echo $this->get_field_id( 'pin' ); ?>"><?php _e( 'Pin:' ); ?></label>
				<?php } ?>
				<select <?php if($mrdev_widget_content_access == 'Denied') { echo 'disabled'; } ?> class="mr-excludeinclude" id="<?php echo $this->get_field_id('excludeinclude'); ?>" name="<?php echo $this->get_field_name('excludeinclude'); ?>">
							<?php
								echo '<option value="0"', $excludeinclude == 0 ? ' selected="selected"' : '', '>Exclude</option>
								<option value="1"', $excludeinclude == 1 ? ' selected="selected"' : '', '>Include</option>';
							?>
				</select>
				<div class="mr-list <?php if($excludeinclude == 1) { echo 'including '; } ?>" >
					<?php
						if(file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/groupexclude.php')) {
							include trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/groupexclude.php';
						}
						include 'items.php';
					?>
				</div>
				</details>
				<p class="mr-widget-itemsnumber"><input <?php if($mrdev_widget_content_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-pagination-input" id="<?php echo $this->get_field_id( 'itemsnumber' ); ?>" name="<?php echo $this->get_field_name( 'itemsnumber' ); ?>" type="number" <?php if($contenttypes == 'custom_items') { echo 'placeholder="1" min="1" title="Choose the number of items to display."'; } else { echo 'placeholder="∞" title="Choose the number of items to display. Leave empty or choose 0 to include all items."'; }?>  value="<?php if(!esc_attr( $itemsnumber )) {  } else { echo esc_attr( $itemsnumber ); } ?>" /> items</p>
			</p>
			</details>
			<details class="appearanceDetails" <?php if(esc_attr( $lastactivedetails ) == 'appearanceDetails') { echo 'open="open"'; }  if($appearance_access == 'Denied') { echo 'style="display:none; opacity: 0.5;"'; } ?>>
			<summary class="mr-section">Appearance</summary>
			<p>
			Theme:<br>
					<select <?php if($appearance_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-themes" id="<?php echo $this->get_field_id('theme'); ?>" name="<?php echo $this->get_field_name('theme'); ?>">
						<?php
							$options = array('default','none');
							foreach ( $options as $option ) {
								echo '<option value="' . $option . '" id="' . $option . '"', $theme == $option ? ' selected="selected"' : '', '>' . $option . '</option>';
							}
							$customOptions = array_map('basename', glob(ABSPATH.'wp-content/themes/mrdev/*' , GLOB_ONLYDIR));
							foreach ( $customOptions as $option ) {
								echo '<option value="' . $option . '" id="' . $option . '"', $theme == $option ? ' selected="selected"' : '', '>' . $option . '</option>';
							}
						?>
					</select>
					<div class="mr-themeoptions">
					<?php
					if($theme == "default") {
						include 'themes/'.$theme.'/index.php';
					} else if($theme == "none") {
					} else {
						include ABSPATH.'wp-content/themes/mrdev/widget/themes/'.$theme.'/index.php';
					}
					?>
					</div>
					<?php
					if(file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/styles.php')) {
						include trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/styles.php';
					}
					?>
					<div class="mr-savetheme" style="display: none;">
					<?php _e( 'Please save the changes when you want to see all the options considered by your selection.' ); ?>
					</div>
			</p>
			</details>
			<details class="paginationDetails" <?php if(esc_attr( $lastactivedetails ) == 'paginationDetails') { echo 'open="open"'; } if($pagination_access == 'Denied') { echo 'style="display:none; opacity: 0.5;"'; } ?>>
			<summary class="mr-section">Pagination</summary>
			<p>
			<select <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> class="mr-pagination-input mr-perline-input" id="<?php echo $this->get_field_id('perline'); ?>" name="<?php echo $this->get_field_name('perline'); ?>" title="Choose the number of items per line">
						<?php
							echo '<option value="0"', $perline == 0 ? ' selected="selected"' : '', '>∞</option>
							<option value="1"', $perline == 1 ? ' selected="selected"' : '', '>1</option>
							<option value="2"', $perline == 2 ? ' selected="selected"' : '', '>2</option>
							<option value="3"', $perline == 3 ? ' selected="selected"' : '', '>3</option>
							<option value="4"', $perline == 4 ? ' selected="selected"' : '', '>4</option>
							<option value="5"', $perline == 5 ? ' selected="selected"' : '', '>5</option>
							<option value="6"', $perline == 6 ? ' selected="selected"' : '', '>6</option>
							<option value="7"', $perline == 7 ? ' selected="selected"' : '', '>7</option>
							<option value="8"', $perline == 8 ? ' selected="selected"' : '', '>8</option>
							<option value="9"', $perline == 9 ? ' selected="selected"' : '', '>9</option>
							<option value="10"', $perline == 10 ? ' selected="selected"' : '', '>10</option>
							<option value="11"', $perline == 11 ? ' selected="selected"' : '', '>11</option>
							<option value="12"', $perline == 12 ? ' selected="selected"' : '', '>12</option>';
						?>
			</select> items per line<br>
			<input <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> class="mr-pagination-input mr-widget-pages-input" type="number" id="<?php echo $this->get_field_id( 'perpage' ); ?>" name="<?php echo $this->get_field_name( 'perpage' ); ?>" type="text" placeholder="∞" title="Choose the number of items per page. Leave empty or type '0' to show all items on the same page." value="<?php if(esc_attr( $perpage ) == "" || esc_attr( $perpage ) <= 0) { } else { echo esc_attr( $perpage ); } ?>" /> items per page
			</p>
			<p>
						<label  for="<?php echo $this->get_field_id( 'pagetoggles' ); ?>"><?php _e( 'Toggles:' ); ?></label> <br>
						<label ><input <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'pagetoggles' ) ); ?>[]" value="0" <?php checked( ( is_array($pagetoggles) AND in_array( 0, $pagetoggles ) ) ? 0 : '', 0 ); ?> /> <?php _e( 'Arrows' ); ?></label><br>
						<label ><input <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'pagetoggles' ) ); ?>[]" value="1" <?php checked( ( is_array($pagetoggles) AND in_array( 1, $pagetoggles ) ) ? 1 : '', 1 ); ?> /> <?php _e( 'Select' ); ?></label><br>
						<label ><input <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'pagetoggles' ) ); ?>[]" value="2" <?php checked( ( is_array($pagetoggles) AND in_array( 2, $pagetoggles ) ) ? 2 : '', 2 ); ?> /> <?php _e( 'Radio' ); ?></label><br>
						<label ><input <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'pagetoggles' ) ); ?>[]" value="5" <?php checked( ( is_array($pagetoggles) AND in_array( 5, $pagetoggles ) ) ? 5 : '', 5 ); ?> /> <?php _e( 'Keyboard' ); ?></label><br>
						<label ><input <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'pagetoggles' ) ); ?>[]" value="3" <?php checked( ( is_array($pagetoggles) AND in_array( 3, $pagetoggles ) ) ? 3 : '', 3 ); ?> /> <?php _e( 'Below' ); ?></label><br>
						<label ><input <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'pagetoggles' ) ); ?>[]" value="4" <?php checked( ( is_array($pagetoggles) AND in_array( 4, $pagetoggles ) ) ? 4 : '', 4 ); ?> /> <?php _e( 'Scroll' ); ?></label><br>
			</p>
			<p>
				<label  for="<?php echo $this->get_field_id( 'tabs' ); ?>"><?php _e( 'Tabs:' ); ?></label><br>
				<select <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-halfsize" id="<?php echo $this->get_field_id('tabs'); ?>" name="<?php echo $this->get_field_name('tabs'); ?>">
							<?php
								echo '<option value="0" id="notabs"', $tabs == 0 ? ' selected="selected"' : '', '>None</option>
								<option value="1" id="itemstabs"', $tabs == 1 ? ' selected="selected"' : '', '>Items</option>
								<option value="2" id="parenttabs"', $tabs == 2 ? ' selected="selected"' : '', '>Items parent</option>
								<option value="3" id="categorytabs"', $tabs == 3 ? ' selected="selected"' : '', '>Categories</option>
								<option value="4" id="tagstabs"', $tabs == 4 ? ' selected="selected"' : '', '>Tags</option>';
							?>
				</select>
				<select <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-halfsize" id="<?php echo $this->get_field_id('tabsposition'); ?>" name="<?php echo $this->get_field_name('tabsposition'); ?>">
							<?php
								echo '<option value="tabstop" id="tabstop"', $tabsposition == 'tabstop' ? ' selected="selected"' : '', '>Top</option>
								<option value="tabsright" id="tabsright"', $tabsposition == 'tabsright' ? ' selected="selected"' : '', '>Right</option>
								<option value="tabsbottom" id="tabsbottom"', $tabsposition == 'tabsbottom' ? ' selected="selected"' : '', '>Bottom</option>
								<option value="tabsleft" id="tabsleft"', $tabsposition == 'tabsleft' ? ' selected="selected"' : '', '>Left</option>';
							?>
				</select><br>
			</p>
			<p>
			<label  for="<?php echo $this->get_field_id( 'pagetransition' ); ?>"><?php _e( 'Page transition:' ); ?></label><br>
			<select <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> class="widefat" id="<?php echo $this->get_field_id('pagetransition'); ?>" name="<?php echo $this->get_field_name('pagetransition'); ?>">
						<?php
							echo '<option value="fade" id="fade"', $pagetransition == 'fade' ? ' selected="selected"' : '', '>Fade</option>
							<option value="slide" id="slide"', $pagetransition == 'slide' ? ' selected="selected"' : '', '>Slide</option>
							<option value="scale" id="scale"', $pagetransition == 'scale' ? ' selected="selected"' : '', '>Scale</option>
							<option value="zoom" id="zoom"', $pagetransition == 'zoom' ? ' selected="selected"' : '', '>Zoom</option>
							<option value="notransition" id="notransition"', $pagetransition == 'notransition' ? ' selected="selected"' : '', '>None</option>';
						?>
					</select><br>
			</p>
			<p>
			<label  for="<?php echo $this->get_field_id( 'autoplay' ); ?>"><?php _e( 'Autoplay:' ); ?></label><br>
			<input <?php if($pagination_access == 'Denied') { echo 'disabled'; } ?> class="mr-pagination-input mr-autoplay-input" type="number" id="<?php echo $this->get_field_id( 'autoplay' ); ?>" name="<?php echo $this->get_field_name( 'autoplay' ); ?>" type="text" placeholder="∞" title="Choose how many seconds the autoplay should take to change page. Leave empty or choose '0' to turn off autoplay." value="<?php if(esc_attr( $autoplay ) == "" || esc_attr( $autoplay ) <= 0) { } else { echo esc_attr( $autoplay ); } ?>" /> seconds per page
			</p>
			</details>
			<details class="displayDetails" <?php if(esc_attr( $lastactivedetails ) == 'displayDetails') { echo 'open="open"'; } if($display_access == 'Denied') { echo 'style="display:none; opacity: 0.5;"'; } ?>>
			<summary class="mr-section">Display</summary>
			<p>
				<label  for="<?php echo $this->get_field_id( 'maintitle' ); ?>"><?php _e( 'Main title:' ); ?></label><br>
				<select <?php if($display_access == 'Denied') { echo 'disabled'; } ?> class="widefat" id="<?php echo $this->get_field_id('maintitle'); ?>" name="<?php echo $this->get_field_name('maintitle'); ?>">
							<?php
								echo '<option value="0" id="widgettitle"', $maintitle == 0 ? ' selected="selected"' : '', '>Widget title</option>
								<option value="1" id="parentstitle"', $maintitle == 1 ? ' selected="selected"' : '', '>Parent title</option>
								<option value="2" id="allparentstitles"', $maintitle == 2 ? ' selected="selected"' : '', '>All parents titles</option>
								<option value="3" id="themeandlayouttitle"', $maintitle == 3 ? ' selected="selected"' : '', '>Theme and layout title</option>
								<option value="4" id="themetitle"', $maintitle == 4 ? ' selected="selected"' : '', '>Theme title</option>
								<option value="5" id="layouttitle"', $maintitle == 5 ? ' selected="selected"' : '', '>Layout title</option>
								<option value="6" id="nomaintitle"', $maintitle == 6 ? ' selected="selected"' : '', '>No main title</option>';
							?>
				</select><br>
			</p>
			<p style="margin-bottom: 0 !important;">
				<label  for="<?php echo $this->get_field_id( 'itemimage' ); ?>"><?php _e( 'Images:' ); ?></label><br>
				<select <?php if($display_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-widget-itemimage" id="<?php echo $this->get_field_id('itemimage'); ?>" name="<?php echo $this->get_field_name('itemimage'); ?>">
							<?php
								echo '<option value="1"', $itemimage == 1 ? ' selected="selected"' : '', '>Item image</option>
								<option value="8"', $itemimage == 8 ? ' selected="selected"' : '', '>Description first image</option>
								<option value="2"', $itemimage == 2 ? ' selected="selected"' : '', '>Latest sticky post image</option>
								<option value="5"', $itemimage == 5 ? ' selected="selected"' : '', '>Latest post image</option>
								<option value="9"', $itemimage == 9 ? ' selected="selected"' : '', '>No image</option>';
							?>
				</select>
			</p>
			<div class="wp-media-buttons mr-media mr-fallbackimage" <?php if($itemimage == 9) { echo 'style="display: none;"'; } ?> title="To use a fallback image insert the image's ID or the URL to an external image">
				<input id="<?php echo $this->get_field_id( 'fallbackimage' ); ?>" class="widefat" name="<?php echo $this->get_field_name( 'fallbackimage' ); ?>" type="text"  placeholder="Fallback image (ID or URL)" value="<?php echo esc_attr( $fallbackimage ); ?>" />
				<button type="button" class="button mr-mediabtn dashicons-before dashicons-admin-media">
				</button>
				<?php 
					if(!empty($fallbackimage)) {
						if(is_numeric($fallbackimage)) {
							$imgSrc = wp_get_attachment_image_src($fallbackimage, 'thumbnail')[0];
						} else {
							$imgSrc = esc_url($fallbackimage);
						}
						echo '<img loading="lazy" width="150" height="150" src="'.$imgSrc.'">';
					}
				?>
			</div>
			<p>
				<label  for="<?php echo $this->get_field_id( 'itemstitle' ); ?>"><?php _e( 'Titles:' ); ?></label><br>
				<select <?php if($display_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-widget-itemstitleinput" id="<?php echo $this->get_field_id('itemstitle'); ?>" name="<?php echo $this->get_field_name('itemstitle'); ?>">
							<?php
								echo '<option value="0"', $itemstitle == 0 ? ' selected="selected"' : '', '>Linked item title</option>
								<option value="2"', $itemstitle == 2 ? ' selected="selected"' : '', '>Item title</option>
								<option value="1"', $itemstitle == 1 ? ' selected="selected"' : '', '>No title</option>';
							?>
						</select><br>
				<span class="mr-widget-itemstitlemax" <?php if($itemstitle && $itemstitle == 1) { echo 'style="display: none;"'; } ?>>	
				<input <?php if($display_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-pagination-input" id="<?php echo $this->get_field_id( 'itemstitlemax' ); ?>" name="<?php echo $this->get_field_name( 'itemstitlemax' ); ?>" type="number" placeholder="∞" value="<?php if(!esc_attr( $itemstitlemax )) {  } else { echo esc_attr( $itemstitlemax ); } ?>" /> max. characters
				</span>
			</p>
			<?php
			if(file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/itemsmeta.php')) {
				include trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/itemsmeta.php';
			}
			?>
			<p>
				<label  for="<?php echo $this->get_field_id( 'itemdesc' ); ?>"><?php _e( 'Descriptions:' ); ?></label><br>
				<select <?php if($display_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-widget-itemdescinput" id="<?php echo $this->get_field_id('itemdesc'); ?>" name="<?php echo $this->get_field_name('itemdesc'); ?>">
							<?php
								echo '<option value="0"', $itemdesc == 0 ? ' selected="selected"' : '', '>Item description</option>
								<option value="4"', $itemdesc == 4 ? ' selected="selected"' : '', '>Item excerpt</option>
								<option value="2"', $itemdesc == 2 ? ' selected="selected"' : '', '>Item intro text</option>
								<option value="3"', $itemdesc == 3 ? ' selected="selected"' : '', '>Item full text</option>
								<option value="1"', $itemdesc == 1 ? ' selected="selected"' : '', '>No description</option>';
							?>
						</select><br>
				<span class="mr-widget-itemdescmax" <?php if($itemdesc && $itemdesc == 1) { echo 'style="display: none;"'; } ?>>
				<input <?php if($display_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-pagination-input" id="<?php echo $this->get_field_id( 'itemdescmax' ); ?>" name="<?php echo $this->get_field_name( 'itemdescmax' ); ?>" type="number" placeholder="∞" value="<?php if(!esc_attr( $itemdescmax )) {  } else { echo esc_attr( $itemdescmax ); } ?>" /> max. characters
				</span>
			</p>
			<p>
				<label  for="<?php echo $this->get_field_id( 'itemlink' ); ?>"><?php _e( 'Links:' ); ?></label><br>
				<select <?php if($display_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-bottomlinkinput" id="<?php echo $this->get_field_id('itemlink'); ?>" name="<?php echo $this->get_field_name('itemlink'); ?>">
							<?php
								echo '<option value="0"', $itemlink == 0 ? ' selected="selected"' : '', '>Item link</option>
								<option value="1"', $itemlink == 1 ? ' selected="selected"' : '', '>No bottom link</option>';
							?>
				</select><br>
				<span class="mr-bottomlinktext" <?php if($itemlink && $itemlink == 1) { echo 'style="display: none;"'; } ?>>
					<input <?php if($display_access == 'Denied') { echo 'disabled'; } ?> class="widefat mr-halfsize" id="<?php echo $this->get_field_id( 'bottomlink' ); ?>" name="<?php echo $this->get_field_name( 'bottomlink' ); ?>" type="text" placeholder="Bottom link text" title="Bottom link text" value="<?php if(esc_attr( $bottomlink ) == "") { echo "Know more..."; } else { echo esc_attr( $bottomlink ); } ?>" />
					<select <?php if($display_access == 'Denied') { echo 'disabled'; } ?>  class="widefat mr-bottomlinktarget mr-halfsize" id="<?php echo $this->get_field_id('itemlinktarget'); ?>" name="<?php echo $this->get_field_name('itemlinktarget'); ?>" title="Select how to open the link.">
							<?php
								echo '<option value="self"', $itemlinktarget == 'self' ? ' selected="selected"' : '', '>Open in the same frame</option>
								<option value="blank"', $itemlinktarget == 'blank' ? ' selected="selected"' : '', '>Open in a new window/tab</option>
								<option value="parent"', $itemlinktarget == 'parent' ? ' selected="selected"' : '', '>Open in the parent frame</option>
								<option value="top"', $itemlinktarget == 'top' ? ' selected="selected"' : '', '>Open in the full body</option>';
							?>
				</select>
				</span>
			</p>
			</details>
			<details class="optionsDetails" <?php if(esc_attr( $lastactivedetails ) == 'optionsDetails') { echo 'open="open"'; } if($options_access == 'Denied') { echo 'style="display:none; opacity: 0.5;"'; } ?>>
			<summary class="mr-section">Options</summary>
			<p class="mr-imageoptionscontainer" <?php if(!file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/imagestype.php')) { echo 'style="display:none; opacity: 0.5;"'; } ?>>
				<label  for="<?php echo $this->get_field_id( 'imagestype' ); ?>"><?php _e( 'Images type:' ); ?></label> <br>
				<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'imagestype' ) ); ?>[]" value="background" <?php checked( ( is_array( $imagestypes ) AND in_array( 'background', $imagestypes ) OR !$imagestypes ) ? 'background' : '', 'background' ); ?> /> <?php _e( 'Background' ); ?></label><br>
				<?php
					if(file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/imagestype.php')) {
						include trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/imagestype.php';
					}
				?>
			</p>
			<p>
						<label  for="<?php echo $this->get_field_id( 'globallayoutoptions' ); ?>"><?php _e( "Items options:" ); ?></label> <br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'globallayoutoptions' ) ); ?>[]" value="windowheight" <?php checked( ( is_array( $globallayoutoptions ) AND in_array( "windowheight", $globallayoutoptions ) ) ? "windowheight" : '', "windowheight" ); ?> /> <?php _e( 'Window height' ); ?></label><br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'globallayoutoptions' ) ); ?>[]" value="onlyactives" <?php checked( ( is_array($globallayoutoptions ) AND in_array( "onlyactives", $globallayoutoptions ) ) ? "onlyactives" : '', "onlyactives" ); ?> /> <?php _e( 'Only show actives' ); ?></label><br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'globallayoutoptions' ) ); ?>[]" value="hideinactives" <?php checked( ( is_array( $globallayoutoptions ) AND in_array( "hideinactives", $globallayoutoptions ) ) ? "hideinactives" : '', "hideinactives" ); ?> /> <?php _e( 'On active hide inactives' ); ?></label><br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'globallayoutoptions' ) ); ?>[]" value="keepactive" <?php checked( ( is_array( $globallayoutoptions ) AND in_array( "keepactive", $globallayoutoptions ) ) ? "keepactive" : '', "keepactive" ); ?> /> <?php _e( 'Keep other actives opened' ); ?></label><br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'globallayoutoptions' ) ); ?>[]" value="subitemactive" <?php checked( ( is_array($globallayoutoptions ) AND in_array( "subitemactive", $globallayoutoptions ) ) ? "subitemactive" : '', "subitemactive" ); ?> /> <?php _e( 'Only show subitems of active' ); ?></label><br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'globallayoutoptions' ) ); ?>[]" value="contentpagination" <?php checked( ( is_array( $globallayoutoptions ) AND in_array( "contentpagination", $globallayoutoptions ) ) ? "contentpagination" : '', "contentpagination" ); ?> /> <?php _e( 'Pagination inside content' ); ?></label><br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'globallayoutoptions' ) ); ?>[]" value="donotinactive" <?php checked( ( is_array( $globallayoutoptions ) AND in_array( "donotinactive", $globallayoutoptions ) ) ? "donotinactive" : '', "donotinactive" ); ?> /> <?php _e( 'Do not inactive on click' ); ?></label><br>
			</p>
			<p>
						<label  for="<?php echo $this->get_field_id( 'itemoptions' ); ?>"><?php _e( 'Other options:' ); ?></label> <br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'itemoptions' ) ); ?>[]" value="artcount" <?php checked( ( is_array($itemoptions) AND in_array( "artcount", $itemoptions ) ) ? "artcount" : '', "artcount" ); ?> /> <?php _e( 'Show number of posts' ); ?></label><br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'itemoptions' ) ); ?>[]" value="hover" <?php checked( ( is_array( $itemoptions ) AND in_array( "hover", $itemoptions ) ) ? "hover" : '', "hover" ); ?> /> <?php _e( 'Active on mouseover' ); ?></label><br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'itemoptions' ) ); ?>[]" value="autoscroll" <?php checked( ( is_array( $itemoptions ) AND in_array( "autoscroll", $itemoptions ) ) ? "autoscroll" : '', "autoscroll" ); ?> /> <?php _e( 'Auto scroll to active' ); ?></label><br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'itemoptions' ) ); ?>[]" value="url" <?php checked( ( is_array( $itemoptions ) AND in_array( "url", $itemoptions ) ) ? "url" : '', "url" ); ?> /> <?php _e( 'Change URL on active' ); ?></label><br>
						<label ><input <?php if($options_access == 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'itemoptions' ) ); ?>[]" value="remember" <?php checked( ( is_array( $itemoptions ) AND in_array( "remember", $itemoptions ) ) ? "remember" : '', "remember" ); ?> /> <?php _e( 'Remember last active <small>(<i>uses cookies</i>)</small>' ); ?></label><br>
			</p>
			</details>
			<?php if(file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/advanced.php')) {
				include trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/advanced.php';
			} else { ?>
				<details class="featuresDetails" <?php if(esc_attr( $lastactivedetails ) == 'featuresDetails') { echo 'open="open"'; } ?>>
					<summary class="mr-section"><strong>DO YOU NEED MORE FEATURES?</strong></summary>
					<p>
					If you need more features then you need <strong>Mr.Dev.'s Framework</strong>:</p>
					<ol>
					<li><strong>Hide widget sections</strong> to specific users or roles.</li>
					<li>Insert widgets inside the content section on posts/pages/categories using <strong>blocks, classic editor button or shortcodes</strong>.</li>
					<li><strong>More content types</strong> such as pages, tags and some compatibility with other third-party registered terms/post-types (such as events and products).</li>
					<li><strong>Override the content</strong> of each item per widget, without affecting the original content.</li>
					<li>Create and edit <strong>custom items</strong> directly on the widgets.</li>
					<li>Choose <strong>items' parents such as parent categories, categories and tags</strong> to only display their childs.</li>
					<li>Manually <strong>reorder items</strong>.</li>
					<li><strong>Pin</strong> to choose the ones starting active.</li>
					<li><strong>Auto exclude</strong> Subcategories, Categories with no posts, same link, different link and more.</li>
					<li>More image options such as <strong>thumbnails and parallax</strong>.</li>
					<li>Choose a <strong>fallback image</strong>.</li>
					<li>Choose <strong>images maximum size</strong> together with <strong>srcset and native lazyload</strong>.</li>
					<li><strong>More options for tabs</strong> such as Categories and Tags.</li>
					<li>Other <strong>Advanced</strong> options such as lazyloading pages, content HTML cache, generate CSS and JS minifying it per widget, choose the titles tag (h2, h3, h4, p, etc), load polyfill for IE compatibility and add custom classes.</li>
					</ol>
					<p>And more...</p>
					<p><a class="button button-primary" href="https://marcosrego.com/en/web-en/mrdev-en/" target="_blank">Get Mr.Dev.'s Framework</a></p>
				</details>
			<?php } ?>
			<input class="widefat lastactivedetails" id="<?php echo $this->get_field_id( 'lastactivedetails' ); ?>" name="<?php echo $this->get_field_name( 'lastactivedetails' ); ?>" type="text" placeholder="Last Active Admin Details/Option" title="Last Active Admin Details/Option" value="<?php if(esc_attr( $lastactivedetails ) != "") { echo esc_attr( $lastactivedetails ); } ?>" readonly hidden />
			<?php
				wp_enqueue_editor();
				wp_enqueue_script('wplink');
				wp_enqueue_script( 'media-upload' );
				wp_enqueue_media();
				wp_enqueue_script( 'mrdevWidget_admin', plugin_dir_url( __DIR__ ).'mrdev-widget/assets/js/admin.js', array('jquery'),'0.9.436');
			?>
			</div>
			<?php
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['widgetid'] = strip_tags($this->id);
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['theme'] = ( !empty( $new_instance['theme'] ) ) ? strip_tags( $new_instance['theme'] ) : 'default';
		if($instance['theme'] == 'none') {
			$instance['layout'] = '';
			$instance['layoutoptions'] = array();
		} else {
			$instance['layout'] = ( !empty( $new_instance['layout'] ) ) ? strip_tags( $new_instance['layout'] ) : '';
			$instance['layoutoptions'] = ( ! empty ( $new_instance['layoutoptions'] ) ) ? array_map( 'sanitize_text_field',(array)$new_instance['layoutoptions']) : array();
		}
		$instance['backgroundcolor'] = ( ! empty( $new_instance['backgroundcolor'] ) ) ? strip_tags( $new_instance['backgroundcolor'] ) : '';
		$instance['titlescolor'] = ( ! empty( $new_instance['titlescolor'] ) ) ? strip_tags( $new_instance['titlescolor'] ) : '';
		$instance['descscolor'] = ( ! empty( $new_instance['descscolor'] ) ) ? strip_tags( $new_instance['descscolor'] ) : '';
		$instance['titlesfont'] = ( ! empty( $new_instance['titlesfont'] ) ) ? strip_tags( $new_instance['titlesfont'] ) : '';
		$instance['descsfont'] = ( ! empty( $new_instance['descsfont'] ) ) ? strip_tags( $new_instance['descsfont'] ) : '';
		$instance['titlessize'] = ( ! empty( $new_instance['titlessize'] ) ) ? strip_tags( $new_instance['titlessize'] ) : '';
		$instance['descssize'] = ( ! empty( $new_instance['descssize'] ) ) ? strip_tags( $new_instance['descssize'] ) : '';
        $instance['perline'] = ( ! empty( $new_instance['perline'] ) ) ? strip_tags( $new_instance['perline'] ) : 0;
		$instance['perpage'] = ( ! empty( $new_instance['perpage'] ) ) ? strip_tags( absint( $new_instance['perpage'] ) ) : 0;
		$instance['autoplay'] = ( ! empty( $new_instance['autoplay'] ) ) ? strip_tags( absint( $new_instance['autoplay'] ) ) : 0;
		$instance['tabs'] = ( ! empty( $new_instance['tabs'] ) ) ? strip_tags( $new_instance['tabs'] ) : 0;
        $instance['tabsposition'] = ( !empty( $new_instance['tabsposition'] ) ) ? strip_tags( $new_instance['tabsposition'] ) : 'tabstop';
		$instance['pagetransition'] = ( ! empty( $new_instance['pagetransition'] ) ) ? strip_tags( $new_instance['pagetransition'] ) : 'fade';
		$instance['pagetoggles'] = ( ! empty ( $new_instance['pagetoggles'] ) ) ? (array) $new_instance['pagetoggles'] : array();
		$instance['pagetoggles'] = array_map( 'sanitize_text_field', $instance['pagetoggles'] );
        $instance['pagetoggles'] = array_map( 'intval', $instance['pagetoggles'] );
			$instance['contenttypes'] = ( !empty( $new_instance['contenttypes'] ) ) ? strip_tags( $new_instance['contenttypes'] ) : 'taxonomy_category';
			$instance['excludeinclude'] = ( !empty( $new_instance['excludeinclude'] ) ) ? strip_tags( absint( $new_instance['excludeinclude'] ) ) : 0;
			$instance['parentcats'] = ( ! empty ( $new_instance['parentcats'] ) ) ? (array) $new_instance['parentcats'] : array();
			$instance['parentcats'] = array_map( 'sanitize_text_field', $instance['parentcats'] );
			$instance['parenttags'] = ( ! empty ( $new_instance['parenttags'] ) ) ? (array) $new_instance['parenttags'] : array();
			$instance['parenttags'] = array_map( 'sanitize_text_field', $instance['parenttags'] );
			$instance['parentpages'] = ( ! empty ( $new_instance['parentpages'] ) ) ? (array) $new_instance['parentpages'] : array();
			$instance['parentpages'] = array_map( 'sanitize_text_field', $instance['parentpages'] );
			$instance['orderby'] = ( !empty( $new_instance['orderby'] ) ) ? strip_tags( absint( $new_instance['orderby'] ) ) : 0;
			$instance['order'] = ( !empty( $new_instance['order'] ) ) ? strip_tags( absint( $new_instance['order'] ) ) : 0;
			$instance['manualordering'] = ( ! empty ( $new_instance['manualordering'] ) ) ? (array) $new_instance['manualordering'] : array();
			$instance['manualordering'] = array_map( 'sanitize_text_field', $instance['manualordering']);
			$instance['manualordering'] = array_map( 'intval', $instance['manualordering'] );
			if(in_array(1,$new_instance['pageorder']) && in_array(count($new_instance['pageorder']),$new_instance['pageorder']) && count($new_instance['pageorder']) === count(array_flip($new_instance['pageorder'])) /*&& max($new_instance['pageorder']) > count($new_instance['pageorder'])*/ /*&& array_sum($new_instance['pageorder']) <= count($new_instance['pageorder'])*/) {
				$instance['pageorder'] = ( ! empty ( $new_instance['pageorder'] ) ) ? (array) $new_instance['pageorder'] : array();
			} else {
				$instance['pageorder'] = range(1,sizeof($instance['pageorder']));
			}
			$instance['pageorder'] = array_map( 'sanitize_text_field', $instance['pageorder']);
			$instance['pageorder'] = array_map( 'intval', $instance['pageorder'] );
			$instance['pageorder'] = array_unique($instance['pageorder']);
			$instance['pin'] = ( ! empty ( $new_instance['pin'] ) ) ? (array) $new_instance['pin'] : array();
			$instance['pin'] = array_map( 'sanitize_text_field', $instance['pin'] );
			$instance['posttypes_groupexclude'] = ( ! empty ( $new_instance['posttypes_groupexclude'] ) ) ? (array) $new_instance['posttypes_groupexclude'] : array();
			$instance['posttypes_groupexclude'] = array_map( 'sanitize_text_field', $instance['posttypes_groupexclude'] );
			$instance['taxonomies_groupexclude'] = ( ! empty ( $new_instance['taxonomies_groupexclude'] ) ) ? (array) $new_instance['taxonomies_groupexclude'] : array();
			$instance['taxonomies_groupexclude'] = array_map( 'sanitize_text_field', $instance['taxonomies_groupexclude'] );
			$instance['groupexclude'] = ( ! empty ( $new_instance['groupexclude'] ) ) ? (array) $new_instance['groupexclude'] : array();
			$instance['groupexclude'] = array_map( 'sanitize_text_field', $instance['groupexclude'] );
			$instance['itemselect'] = ( ! empty ( $new_instance['itemselect'] ) ) ? (array) $new_instance['itemselect'] : array();
			$instance['itemselect'] = array_map( 'sanitize_text_field', $instance['itemselect'] );
			if(current_user_can( 'edit_posts' )) {
				$instance['titleoverride'] = ( ! empty ( $new_instance['titleoverride'] ) ) ? (array) $new_instance['titleoverride'] : array();
				$instance['titleoverride'] = array_map( 'sanitize_text_field', $instance['titleoverride']);
				$instance['dateoverride'] = ( ! empty ( $new_instance['dateoverride'] ) ) ? (array) $new_instance['dateoverride'] : array();
				$instance['dateoverride'] = array_map( 'sanitize_text_field', $instance['dateoverride']);
				$instance['authoroverride'] = ( ! empty ( $new_instance['authoroverride'] ) ) ? (array) $new_instance['authoroverride'] : array(0);
				$instance['authoroverride'] = array_map( 'intval', $instance['authoroverride']);
				$instance['textoverride'] = ( ! empty ( $new_instance['textoverride'] ) ) ? (array) $new_instance['textoverride'] : array();
				$instance['textoverride'] = array_map( 'htmlspecialchars', $instance['textoverride']);
				$instance['imageoverride'] = ( ! empty ( $new_instance['imageoverride'] ) ) ? (array) $new_instance['imageoverride'] : array();
				$instance['imageoverride'] = array_map( 'sanitize_text_field', $instance['imageoverride']);
				$instance['linkoverride'] = ( ! empty ( $new_instance['linkoverride'] ) ) ? (array) $new_instance['linkoverride'] : array();
				$instance['linkoverride'] = array_map( 'sanitize_text_field', $instance['linkoverride']);
				$instance['bottomlinkoverride'] = ( ! empty ( $new_instance['bottomlinkoverride'] ) ) ? (array) $new_instance['bottomlinkoverride'] : array();
				$instance['bottomlinkoverride'] = array_map( 'sanitize_text_field', $instance['bottomlinkoverride']);
				$instance['itemlinktargetoverride'] = ( ! empty ( $new_instance['itemlinktargetoverride'] ) ) ? (array) $new_instance['itemlinktargetoverride'] : array();
				$instance['itemlinktargetoverride'] = array_map( 'sanitize_text_field', $instance['itemlinktargetoverride']);
				$instance['backgroundoverride'] = ( ! empty ( $new_instance['backgroundoverride'] ) ) ? (array) $new_instance['backgroundoverride'] : array();
				$instance['backgroundoverride'] = array_map( 'sanitize_text_field', $instance['backgroundoverride']);
				$instance['titlescoloroverride'] = ( ! empty ( $new_instance['titlescoloroverride'] ) ) ? (array) $new_instance['titlescoloroverride'] : array();
				$instance['titlescoloroverride'] = array_map( 'sanitize_text_field', $instance['titlescoloroverride']);
				$instance['desccoloroverride'] = ( ! empty ( $new_instance['desccoloroverride'] ) ) ? (array) $new_instance['desccoloroverride'] : array();
				$instance['desccoloroverride'] = array_map( 'sanitize_text_field', $instance['desccoloroverride']);
				$instance['titlesfontoverride'] = ( ! empty ( $new_instance['titlesfontoverride'] ) ) ? (array) $new_instance['titlesfontoverride'] : array();
				$instance['titlesfontoverride'] = array_map( 'sanitize_text_field', $instance['titlesfontoverride']);
				$instance['descsfontoverride'] = ( ! empty ( $new_instance['descsfontoverride'] ) ) ? (array) $new_instance['descsfontoverride'] : array();
				$instance['descsfontoverride'] = array_map( 'sanitize_text_field', $instance['descsfontoverride']);
				$instance['titlessizeoverride'] = ( ! empty ( $new_instance['titlessizeoverride'] ) ) ? (array) $new_instance['titlessizeoverride'] : array();
				$instance['titlessizeoverride'] = array_map( 'sanitize_text_field', $instance['titlessizeoverride']);
				$instance['descssizeoverride'] = ( ! empty ( $new_instance['descssizeoverride'] ) ) ? (array) $new_instance['descssizeoverride'] : array();
				$instance['descssizeoverride'] = array_map( 'sanitize_text_field', $instance['descssizeoverride']);
			}
			$instance['itemsnumber'] = ( ! empty( $new_instance['itemsnumber'] ) ) ? strip_tags( absint( $new_instance['itemsnumber'] ) ) : '';
		$instance['maintitle'] = ( !empty( $new_instance['maintitle'] ) ) ? strip_tags( $new_instance['maintitle'] ) : 0;
		$instance['itemimage'] = ( !empty( $new_instance['itemimage'] ) ) ? strip_tags( absint( $new_instance['itemimage'] ) ) : 1;
		$instance['fallbackimage'] = ( ! empty( $new_instance['fallbackimage'] ) ) ? strip_tags( $new_instance['fallbackimage'] ) : '';
		$instance['imagestype'] = ( ! empty ( $new_instance['imagestype'] ) ) ? (array) $new_instance['imagestype'] : array('background');
		$instance['imagestype'] = array_map( 'sanitize_text_field', $instance['imagestype'] );
		$instance['imagemaxwidth'] = ( ! empty( $new_instance['imagemaxwidth'] ) ) ? strip_tags( absint( $new_instance['imagemaxwidth'] ) ) : '';
		$instance['imagemaxheight'] = ( ! empty( $new_instance['imagemaxheight'] ) ) ? strip_tags( absint( $new_instance['imagemaxheight'] ) ) : '';
		$instance['itemstitle'] = ( !empty( $new_instance['itemstitle'] ) ) ? strip_tags( absint( $new_instance['itemstitle'] ) ) : 0;
		$instance['itemstitlemax'] = ( ! empty( $new_instance['itemstitlemax'] ) ) ? strip_tags( absint( $new_instance['itemstitlemax'] ) ) : '';
		$instance['itemdatelabel'] = ( ! empty( $new_instance['itemdatelabel'] ) ) ? strip_tags( $new_instance['itemdatelabel'] ) : 'Written on ';
		$instance['itemsdate'] = ( !empty( $new_instance['itemsdate'] ) ) ? strip_tags( absint( $new_instance['itemsdate'] ) ) : 0;
		$instance['itemauthorlabel'] = ( ! empty( $new_instance['itemauthorlabel'] ) ) ? strip_tags( $new_instance['itemauthorlabel'] ) : 'by ';
		$instance['itemsauthor'] = ( !empty( $new_instance['itemsauthor'] ) ) ? strip_tags( absint( $new_instance['itemsauthor'] ) ) : 0;
		$instance['itemtaxonomieslabel'] = ( ! empty( $new_instance['itemtaxonomieslabel'] ) ) ? strip_tags( $new_instance['itemtaxonomieslabel'] ) : 'in ';
		$instance['itemstaxonomies'] = ( !empty( $new_instance['itemstaxonomies'] ) ) ? strip_tags( absint( $new_instance['itemstaxonomies'] ) ) : 0;
		$instance['itemdesc'] = ( !empty( $new_instance['itemdesc'] ) ) ? strip_tags( absint( $new_instance['itemdesc'] ) ) : 0;
		$instance['itemdescmax'] = ( ! empty( $new_instance['itemdescmax'] ) ) ? strip_tags( absint( $new_instance['itemdescmax'] ) ) : '';
		$instance['itemlink'] = ( !empty( $new_instance['itemlink'] ) ) ? strip_tags( absint($new_instance['itemlink'] ) ) : 0;
		$instance['itemlinktarget'] = ( !empty( $new_instance['itemlinktarget'] ) ) ? strip_tags( $new_instance['itemlinktarget'] ) : 'self';
		$instance['bottomlink'] = ( ! empty( $new_instance['bottomlink'] ) ) ? strip_tags( $new_instance['bottomlink'] ) : 'Know more...';
		$instance['itemoptions'] = ( ! empty ( $new_instance['itemoptions'] ) ) ? (array) $new_instance['itemoptions'] : array();
		$instance['itemoptions'] = array_map( 'sanitize_text_field', $instance['itemoptions'] );
		$instance['globallayoutoptions'] = ( ! empty ( $new_instance['globallayoutoptions'] ) ) ? (array) $new_instance['globallayoutoptions'] : array();
		$instance['globallayoutoptions'] = array_map( 'sanitize_text_field', $instance['globallayoutoptions'] );
		$instance['technical'] = ( ! empty ( $new_instance['technical'] ) ) ? (array) $new_instance['technical'] : array();
		$instance['technical'] = array_map( 'sanitize_text_field', $instance['technical'] );
		$instance['technical'] = array_map( 'intval', $instance['technical'] );
		$instance['titletag'] = ( !empty( $new_instance['titletag'] ) ) ? strip_tags( $new_instance['titletag'] ) : 'h3';
		$instance['bottomlinkclasses'] = ( ! empty( $new_instance['bottomlinkclasses'] ) ) ? strip_tags( $new_instance['bottomlinkclasses'] ) : '';
		$instance['widgetclasses'] = ( ! empty( $new_instance['widgetclasses'] ) ) ? strip_tags( $new_instance['widgetclasses'] ) : '';
		$instance['customcss'] = ( ! empty( $new_instance['customcss'] ) ) ? strip_tags( $new_instance['customcss'] ) : '';
		$instance['lastactivedetails'] = ( ! empty( $new_instance['lastactivedetails'] ) ) ? strip_tags( $new_instance['lastactivedetails'] ) : '';
		if(file_exists(trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/cache.php')) {
			include trailingslashit(plugin_dir_path( __DIR__ ) ).'mrdev-framework_wp/settings/widget/cache.php';
		}
		return $instance;
	}
}
?>