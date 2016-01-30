<?php

/**
 *  Plugin Name:        Slaask
 *  Plugin URI:         https://slaask.com/wordpress
 *  Description:        Slaask enables you to set up a dedicated live chat channel on your Slack. Less tabs. Better reactivity. More conversions.
 *  Version:            1.1
 *  Author:             Rémi Delhaye
 *  Author URI:         https://slaask.com/team
 *  License:            GPL2
 *  License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 *  GitHub Plugin URI:  https://github.com/chattr-app/slaask-for-wordpress
 *  GitHub Branch:      master
 **/

if (!defined('ABSPATH')) {
  die('You can not access this file.');
  exit;
}

if (is_admin()) {
  if (file_exists(plugin_dir_path(__FILE__) . '/github-updater.php')) {
    include_once(plugin_dir_path(__FILE__) . '/github-updater.php');
    if (class_exists('WP_GitHub_Updater')) {
      new WP_GitHub_Updater(array(
        'slug'                => plugin_basename(__FILE__),
        'proper_folder_name'  => 'slaask-for-wordpress',
        'api_url'             => 'https://api.github.com/repos/chattr-app/slaask-for-wordpress',
        'raw_url'             => 'https://raw.github.com/chattr-app/slaask-for-wordpress/master',
        'github_url'          => 'https://github.com/chattr-app/slaask-for-wordpress',
        'zip_url'             => 'https://github.com/chattr-app/slaask-for-wordpress/archive/master.zip',
        'sslverify'           => true,
        'requires'            => '4.2',
        'tested'              => '4.2.2',
        'readme'              => 'version.md',
        'access_token'        => '',
      ));
    } else {
      error_log('SLAASK ERROR: The "WP_GitHub_Updater" class could not be loaded. Auto updates are not working...');
    }
  } else {
    error_log('SLAASK ERROR: The "github-updater.php" file could not be loaded. Auto updates are not working...');
  }
}

class Slaask {
  var $options = array();
  var $db_version = 1;

  function __construct() {
    add_action( 'wp_head', array( $this, 'wp_head' ) );
    add_action( 'admin_init', array( $this, 'admin_init' ) );
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );

    $this->option_defaults = array(
      'api_key' => '',
      'db_version' => $this->db_version,
    );
  }

  function wp_head() {
    $this->options = wp_parse_args(get_option('slaask_options'), $this->option_defaults);

    if (isset($this->options['api_key'])) {
      $this->options['api_key'] = esc_attr($this->options['api_key']);

      echo '<script src="https://cdn.slaask.com/chat.js"></script>
      <script>
        _slaask.init("' . $this->options['api_key'] . '");
      </script>';
    }
  }

  function admin_init() {
    $this->options = wp_parse_args(get_option('slaask_options'), $this->option_defaults );
    $this->register_settings();
  }

  function admin_menu() {
    add_management_page(__('Slaask'), __('Slaask'), 'manage_options', 'slaask-settings', array($this, 'slaask_settings'));
  }

  function register_settings() {
    register_setting('slaask', 'slaask_options', array($this, 'slaask_sanitize'));
    add_settings_section('slaask_settings_section', 'Slaask Settings', array($this, 'slaask_settings_callback'), 'slaask-settings');
    add_settings_field('api_key', 'Widget Key', array($this, 'widget_id_callback'), 'slaask-settings', 'slaask_settings_section');
  }

  function slaask_settings_callback() {
    ?>
    <b>Your Widget ID is available on your widget page on <a target="_blank" href="https://slaask.com">slaask.com</a></b>
    <?php
  }

  function widget_id_callback() {
    ?>
    <input type="input" id="slaask_options[api_key]" name="slaask_options[api_key]" value="<?php echo ($this->options['api_key']); ?>" >
    <label for="slaask_options[api_key]"><?php _e('Paste your Widget Key here', 'slaask'); ?></label>
    <?php
  }

  function slaask_settings() {
    ?>
    <div class="wrap">
      <h2><?php _e('Slaask', 'slaask'); ?></h2>
      <form action="options.php" method="POST" >
        <?php
          settings_fields('slaask');
          do_settings_sections('slaask-settings');
          submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  function slaask_sanitize($input) {
    $options              = $this->options;
    $input['db_version']  = $this->db_version;

    foreach ($options as $key=>$value) {
      $output[$key] = sanitize_text_field($input[$key]);
    }

    return $output;
  }

  function add_settings_link($links, $file) {
    if (plugin_basename( __FILE__ ) == $file) {
      $settings_link = '<a href="' . admin_url('tools.php?page=slaask-settings') .'">' . __('Settings', 'slaask') . '</a>';
      array_unshift($links, $settings_link);
    }
    return $links;
  }
}

new Slaask();
