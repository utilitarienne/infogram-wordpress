<?php
/*
  Plugin Name: Infogr.am FORK
  Plugin URI: https://github.com/utilitarienne/infogram-wordpress
  Description: It allows you to insert graphics from the site infogram.com
  Version: 1.6.2
  Text Domain: infogram
  Tags: infogram, shortcode, iframe, insert, rest api, json
*/

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


// check if class already exists
if( !class_exists('cl_plugin_infogram') ) :

class cl_plugin_infogram {


  /*
  *  __construct
  *
  *  This function will setup the class functionality
  *
  *  @type  function
  *  @date  17/02/2016
  *  @since 1.0.0
  *
  *  @param void
  *  @return  void
  */

  function __construct() {

    // settings
    // - these will be passed into the field class.
    $this->settings = array(
      'version' => '1.0.0',
      'url'   => plugin_dir_url( __FILE__ ),
      'path'    => plugin_dir_path( __FILE__ )
    );

    // Add setings page and register settings
    add_action('admin_menu', array($this, 'infogr_add_pages'));
    add_action('wp_ajax_infogram_dialog', array($this, 'infogr_ajax_dialog'));

    add_shortcode('infogram', array($this, 'infogr_add_infographics'));


    // include field
    add_action('acf/include_field_types',   array($this, 'include_field')); // v5
    add_action('acf/register_fields',     array($this, 'include_field')); // v4

    // Main Infogram activation hook
    if ( is_admin() ) {
      add_action('plugins_loaded', array($this, 'infogr_create_object'));
    };
  }


  function infogr_add_pages() {
    //create new top-level menu
    add_options_page('Infogram.com v1.6.2', 'Infogram settings', 'level_0', 'infogram', 'infogr_page');

    //call register settings function
    add_action('admin_init', array($this, 'register_infogr_settings'));
  }

  function infogr_ajax_dialog() {
    global $infogram;
    ($infogram->check_is_valid()) ? infogr_add_media_popup() : infogr_message_popup();

    wp_die();
  }

  function register_infogr_settings() {
    //register our settings
    register_setting('my-infogr-settings', 'infogr_api_key');
    register_setting('my-infogr-settings', 'infogr_api_secret');
  }


  function infogr_create_object() {
    // Load Api config file
    require_once('core/autoload.php');
    // Load main Infogram class
    require_once('class/class-infogram.php');
    // Load media button function
    require_once('button/add_button.php');

    global $infogram;

    $options = array(
      'api_key' => get_option('infogr_api_key'),
      'api_secret' => get_option('infogr_api_secret')
    );

    if ( !$infogram ) {
      $infogram = new Infogram($options);
    }
  }


  // out infographic
  function infogr_add_infographics($atts) {
    $atts = shortcode_atts(array(
      'id' => '',
      'prefix' => '',
      'format' => ''
    ), $atts, 'id');

    $format = 'interactive';

    if($atts['id']) {
      if($atts['format'] && $atts['format'] == 'image') {
        $format = 'image';
      }

      return '<div class="infogram-embed" data-id="'.$atts['id'].'" data-type="'.$format.'"></div><script>!function(e,t,s,i){var n="InfogramEmbeds",o=e.getElementsByTagName("script"),d=o[0],r=/^http:/.test(e.location)?"http:":"https:";if(/^\/{2}/.test(i)&&(i=r+i),window[n]&&window[n].initialized)window[n].process&&window[n].process();else if(!e.getElementById(s)){var a=e.createElement("script");a.async=1,a.id=s,a.src=i,d.parentNode.insertBefore(a,d)}}(document,0,"infogram-async","//e.infogram.com/js/dist/embed-loader-min.js");</script>';
    } else {
      return 'This code is broken or not exists!';
    }
  }


    /*
  *  include_field
  *
  *  This function will include the field type class
  *
  *  @type  function
  *  @date  17/02/2016
  *  @since 1.0.0
  *
  *  @param $version (int) major ACF version. Defaults to false
  *  @return  void
  */

  function include_field( $version = false ) {

    // support empty $version
    if( !$version ) $version = 4;


    // load acf-infogram
    load_plugin_textdomain( 'infogram', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );


    // include
    include_once('custom_field/fields/class-cl-acf-field-infogram-v' . $version . '.php');
  }

}

function infogr_page() {
  global $infogram;
?>
  <div class="wrap">
    <h2>Infogram</h2>
    <?php $infogram->plugin_status(); ?>
    <form method="post" action="options.php">
      <?php settings_fields('my-infogr-settings'); ?>
      <?php do_settings_sections('my-infogr-settings'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row"><?php _e('Your Api key:', 'infogram'); ?></th>
          <td><input type="text" name="infogr_api_key" size="40" value="<?php echo esc_attr( get_option('infogr_api_key') ); ?>" /></td>
        </tr>
        <tr valign="top">
          <th scope="row"><?php _e('Your Api secret:', 'infogram'); ?></th>
          <td><input type="text" name="infogr_api_secret" size="40" value="<?php echo esc_attr( get_option('infogr_api_secret') ); ?>" /></td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
<?php
}

// initialize
new cl_plugin_infogram();

// class_exists check
endif;
