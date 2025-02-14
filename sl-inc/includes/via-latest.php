<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//4/10/17 5:15:25p - last saved

if (!class_exists('ViaLatest')) {
	class ViaLatest {
		

		// Class initialization
		function __construct() {
			if (isset($_GET['show_yoast_widget'])) {
				if ($_GET['show_yoast_widget'] == "true") {
					sl_data( 'show_yoast_widget', 'update', 'noshow' );
				} else {
					sl_data( 'show_yoast_widget', 'update', 'show' );
				}
			} 
		
			// Add the widget to the dashboard
			add_action( 'wp_dashboard_setup', array(&$this, 'register_widget') );
			add_filter( 'wp_dashboard_widgets', array(&$this, 'add_widget') );
		}

		// Register this widget -- we use a hook/function to make the widget a dashboard-only widget
		function register_widget() {
			global $text_domain;
			global $wp_registered_widgets, $wp_registered_widget_controls;
			wp_register_sidebar_widget( 'via_posts', __( 'Latest about Store Locator for WordPress', "store-locator"), array(&$this, 'widget'), array( 'all_link' => 'http://www.viadat.com/category/store-locator', 'feed_link' => 'http://feeds.feedburner.com/viadat', 'edit_link' => 'options.php' ) );
			$wp_registered_widget_controls['via_posts'] = $wp_registered_widgets['via_posts'];
		}

		// Modifies the array of dashboard widgets and adds this plugin's
		function add_widget( $widgets ) {
			global $wp_registered_widgets;
			if (!empty($wp_registered_widgets['via_posts']) && !isset($wp_registered_widgets['via_posts']) ) return $widgets; 
			array_splice( $widgets, 2, 0, 'via_posts' ); 
			return $widgets;
		}

		function widget($args = array()) {
			$show = sl_data('show_yoast_widget');
			if ($show != 'noshow') {
				if (is_array($args))
					extract( $args, EXTR_SKIP );
				//echo $before_widget.$before_title.$widget_name.$after_title;
				
				@include_once(ABSPATH . WPINC . '/rss.php');
				$rss = fetch_rss('http://feeds.feedburner.com/viadat');
				if ($rss) {
					$items = @array_slice($rss->items, 0, 4);
				}
				
				if (empty($items)) { echo '<li>No items</li>'; }
				else {
				echo '<div class="rss-widget"><ul>';
				//<!--div style="float:right"><a href="http://www.viadat.com/"><img style="margin: 0 0 5px 5px;" src="http://www.viadat.com/images/viadat_emblem_white.jpg" alt="Viadat Creations"/></a></div-->
				foreach ( $items as $item ) : ?>
				<li><a class="rsswidget" href='<?php echo $item['link']; ?>' title='<?php echo $item['title']; ?>'><?php echo $item['title']; ?></a>   <span class="rss-date"><?php echo date('F j, Y',strtotime($item['pubdate'])); ?></span><!--br/><br/--> 
				<!--p><?php //$item['description'] /*echo substr($item['description'],0,strpos($item['description'], "This is a post from"))*/; ?></p--></li>
				<?php endforeach;
				print "</ul></div>";
				}
				//echo $after_widget;
			}
		}
	}

	// Start this plugin once all other plugins are fully loaded
	add_action( 'plugins_loaded', function() { global $ViaLatest; $ViaLatest = new ViaLatest(); } );
}
?>