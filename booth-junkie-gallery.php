<?php
/*
 * Plugin Name:       Booth Junkie Gallery
 * Description:       Showcase your Booth Junkie Social galleries on your Wordpress enabled website.
 * Version:           1.1.5
 * Author:            Booth Junkie Ltd
 * Author URI:        https://www.boothjunkie.co.uk
 */


function getBoothJunkieCSS(){
		return "		
		fieldset {
			border: none;
		}
		
		h1 {
			text-align: center;
			font-family: Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif;
			font-size: 32px;
			font-style: normal;
			font-variant: normal;
			font-weight: 500;
			line-height: 26.4px;
		}
		
		aimg
		{
		 width:auto;
		 box-shadow:0px 0px 20px #cecece;
		 -moz-transform: scale(0.7);
		 -moz-transition-duration: 0.6s; 
		 -webkit-transition-duration: 0.6s;
		 -webkit-transform: scale(0.7);
		 
		 -ms-transform: scale(0.7);
		 -ms-transition-duration: 0.6s; 
		}
		aimg:hover
		{
		  box-shadow: 20px 20px 20px #dcdcdc;
		 -moz-transform: scale(0.8);
		 -moz-transition-duration: 0.6s;
		 -webkit-transition-duration: 0.6s;
		 -webkit-transform: scale(0.8);
		 
		 -ms-transform: scale(0.8);
		 -ms-transition-duration: 0.6s;
		 
		}
		
		.shareBox{
			float: right;
			position: relative;
			height: 100%;
			width: 40px;
		}
		
		.fullContainer {
		  padding: 0;
		  margin: 0;
		  list-style: none;
		  
		  display: -webkit-box;
		  display: -moz-box;
		  display: -ms-flexbox;
		  display: -webkit-flex;
		  display: flex;
		  
		  -webkit-flex-flow: row wrap;
		  justify-content: space-around;
		}
		
		.imageBox{
			qwidth:250px;
			qheight:200px;
		  box-shadow: 0 2px 4px 0 rgba(0,0,0,0.16),0 2px 10px 0 rgba(0,0,0,0.12) !important;
		
		  background: #C0C0C0;
		  padding: 5px;
		  width: 250px;
		  height: 200px;
		  margin-top: 10px;
		  
		  line-height: 200px;
		  color: white;
		  font-weight: bold;
		  font-size: 3em;
		  text-align: center;
		}
		
		.imageContainer{
			width:200px;
			height:200px;
			position: relative;
			top: 0px;
			left: 0px;
			float: left;
			text-align: center;
		}
		
		.facebookShare, .emailShare, .deleteButton {
			cursor: pointer;
			width: 45px;
			height: 45px;
			margin: 2px;
			float: right;
		}
		
		#emailForm, #deleteForm {
			display: none;
		}";
}

function displayBoothJunkieGallery(){
	$ret = "";
	$options = get_option( 'boothjunkie_settings' );
	$licencekeys = $options['boothjunkie_licencekeys'];
	$licencekeys = explode("\n", $licencekeys);
	array_walk($licencekeys, create_function('&$val', '$val = trim(str_replace("-","",$val));')); 
	foreach($licencekeys as $key){
		$response = wp_remote_get( 'https://social.boothjunkie.co.uk/getGalleries.php?' . $key );
		$ret .= wp_remote_retrieve_body( $response );
	}
	return $ret;
}
add_shortcode('BoothJunkieGallery', 'displayBoothJunkieGallery');

// Settings
?><?php
add_action( 'admin_menu', 'boothjunkie_add_admin_menu' );
add_action( 'admin_init', 'boothjunkie_settings_init' );
add_action( 'wp_enqueue_scripts','BoothJunkieEnqueue');
function BoothJunkieEnqueue(){
	wp_enqueue_style( 'BoothJunkieGalleryCSS', plugins_url( '/style.css', __FILE__ ) );
}

function boothjunkie_add_admin_menu(  ) { 

	add_menu_page( 'Booth Junkie Gallery', 'Booth Junkie Gallery', 'manage_options', 'booth_junkie_gallery', 'boothjunkie_options_page' );

}


function boothjunkie_settings_init(  ) { 

	register_setting( 'pluginPage', 'boothjunkie_settings' );

	add_settings_section(
		'boothjunkie_pluginPage_section', 
		__( 'Settings', 'Booth Junkie Gallery' ), 
		'boothjunkie_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'boothjunkie_licencekeys', 
		__( 'Licence Key(s)', 'Booth Junkie Gallery' ), 
		'boothjunkie_textarea_licencekeys_render', 
		'pluginPage', 
		'boothjunkie_pluginPage_section' 
	);


}


function boothjunkie_textarea_licencekeys_render(  ) { 

	$options = get_option( 'boothjunkie_settings' );
	?>
	<textarea cols='40' rows='5' name='boothjunkie_settings[boothjunkie_licencekeys]'><?php echo $options['boothjunkie_licencekeys']; ?></textarea>
	<?php

}


function boothjunkie_settings_section_callback(  ) { 

	echo __( 'Use the field below to input your Licence Key(s) for Booth Junkie.</br>These can be found in the Options tab of the Admin Panel of Booth Junkie, by clicking or tapping the top right corner of your screen.<br/>Please insert each Licence Key on a new line', 'Booth Junkie Gallery' );

}


function boothjunkie_options_page(  ) { 

	?>
	<form action='options.php' method='post'>

		<h2>Booth Junkie Gallery</h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}

?>