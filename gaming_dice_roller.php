<?php
/*
Version: 1.0
Plugin Name: Gaming Dice Roller
Description: The Gaming Dice Roller lets users roll any number of the standard ployhedral RPG gaming dice and outputs the numbers.  A perfect way to spread the love to the diceless masses!
Author URI: http://dashfien.com
Author: David Dashifen Kees
*/

add_action("widgets_init", "gaming_dice_roller::load_widget");
add_action("wp_print_styles", "gaming_dice_roller::load_styles");
add_action("wp_enqueue_scripts", "gaming_dice_roller::load_scripts");
add_action('wp_ajax_nopriv_general_roll', 'gaming_dice_roller::roll');					// for unauthenticated visitors
add_action('wp_ajax_general_roll', 'gaming_dice_roller::roll');							// for authenticated visitors

class gaming_dice_roller extends WP_Widget {
	const version = 1.1;
	
	public static function load_widget() { register_widget("gaming_dice_roller"); }
	
	public static function load_scripts() {
		if(!defined("GDR_PLUGIN_FOLDER")) {
			$folders = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
			while(($folder = array_pop($folders)) != "plugins") $path[] = $folder;
			define("GDR_PLUGIN_FOLDER", WP_PLUGIN_URL . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, array_reverse($path)) . DIRECTORY_SEPARATOR);		
		}

		wp_enqueue_script("gaming_dice_roller_scripts", GDR_PLUGIN_FOLDER . "gdr_scripts.js", array("prototype"));
		wp_localize_script("gaming_dice_roller_scripts", 'gaming_dice_roller_ajax', array('url' => admin_url('admin-ajax.php')));
		wp_enqueue_script("prototype");
	}
	
	public static function load_styles() {
		if(!defined("GDR_PLUGIN_FOLDER")) {
			$folders = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
			while(($folder = array_pop($folders)) != "plugins") $path[] = $folder;
			define("GDR_PLUGIN_FOLDER", WP_PLUGIN_URL . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, array_reverse($path)) . DIRECTORY_SEPARATOR);		
		}

		wp_enqueue_style("gaming_dice_roller_styles", GDR_PLUGIN_FOLDER . "gdr_styles.css");
	}
		
	public static function roll() {
		extract($_POST);
		
		// the extraction above gives us $pool and $die which determines how many of what type of die we roll.  but, if
		// $die is "Percentile" then we roll 1d100 regardless of what $pool says.  
		
		$roll = array();
		if($die == 100) $pool = 1;
		for($i=0; $i<floor($pool); $i++) $roll[] = mt_rand(1, $die);

		// for display purposes, we'll sort our roll and then display it as a comma separated list. if there's more than 
		// a single roll, then we're giong to also show the sum of the rolls following the list

		sort($roll);		
		$results = join(", ", $roll);
		if($pool > 1) $results .= " (" . array_sum($roll) . ")";
		exit($results);
	}
	

	/* WIDGET FUNCTIONALITY
	 * The following methods are the core of the WordPress widget functionality.  They provide the ability for our
	 * plugin to be hooked into the WordPress Appearance > Widgets list and create the on-screen display for it.
	 */
	
	public function __construct() {
		$widget_ops  = array("classname" => "gaming-dice-roller-widget", "description" => "The Gaming Dice Roller lets users roll any number of the standard ployhedral RPG gaming dice and outputs the numbers.");
		parent::__construct("gaming-dice-roller-widget", "Gaming Dice Roller", $widget_ops);
	}
	
	public function widget($args, $instance) {
		extract($instance);		// creates gaming_dice_roller_title
		extract($args);			// creates before_widget, before_title, after_title, after_widget
		
		$widget = "$before_widget $before_title $gaming_dice_roller_title $after_title";
		ob_start(); ?>
		
		

		
		
				
		<form id="dice_rolling_form">
		<p>
			<label for="gaming_dice_roller_pool"><span>How many dice do you want to</span> Roll:</label>
			<input type="number" id="gaming_dice_roller_pool" name="gaming_dice_roller_pool" value="1">
			<label for="gaming_dice_roller_die"><span>What type of die do you want to roll:</span></label>
			<select id="gaming_dice_roller_die" name="gaming_dice_roller_die">
				<option value="4">d4</option>
				<option value="6">d6</option>
				<option value="8">d8</option>
				<option value="10">d10</option>
				<option value="12">d12</option>
				<option value="20">d20</option>
				<option value="100">d100</option>
			</select>
			<input type="button" id="gaming_dice_roller_go" value="Go">
		</p>
		</form>
		
		<div id="gaming_dice_roller_results">
			<strong>Results:</strong>
			<p></p>
		</div>
		
		<p id="gaming_dice_roller_link"><a href="http://www.awesomedice.com/wordpress-dice-roller">Awesome Dice</a></p>
		
		<?php $widget .= ob_get_clean() . $after_widget;		
		echo $widget;
	}

	public function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance["gaming_dice_roller_title"] = strip_tags($new_instance["gaming_dice_roller_title"]);
		return $instance;
	}

	public function form($instance) {
		$defaults = array(
			"gaming_dice_roller_title" => "Gaming Dice Roller",		// title to show about the widget
		);
		
		$instance = wp_parse_args((array) $instance, $defaults); ?>	
		
		<p>
			<label for="<?php echo $this->get_field_id("gaming_dice_roller_title"); ?>"><strong>Title:</strong></label>
			<input id="<?php echo $this->get_field_id("gaming_dice_roller_title"); ?>" name="<?php echo $this->get_field_name("gaming_dice_roller_title"); ?>" value="<?php echo $instance["gaming_dice_roller_title"]; ?>" size="25">
		</p>
	<?php }


} ?>