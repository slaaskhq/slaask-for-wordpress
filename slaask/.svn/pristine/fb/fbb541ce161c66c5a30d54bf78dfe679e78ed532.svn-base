<?php
/**
 *  Plugin Name:        Slaask
 *  Plugin URI:         https://slaask.com/wordpress
 *  Description:        Your customer service app for Slack. Bring all your team -and client!- communication together in one place.
 *  Version:            1.5
 *  Author:             Slaask Team
 *  Author URI:         https://slaask.com/team
 *  License:            GPL2
 *  License URI:        https://www.gnu.org/licenses/gpl-2.0.html
 *  GitHub Plugin URI:  https://github.com/slaaskhq/slaask-for-wordpress
 *  GitHub Branch:      master
 **/

if (!defined('ABSPATH')) {
    die('You can not access this file.');
    exit;
}

class Slaask {
    const  plugin_folder_name = 'slaask';

    var $options = array();
    var $db_version = 1;
    function __construct() {
        add_action( 'wp_head', array( $this, 'wp_head' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'load_wp_scripts' ) );

        $this->option_defaults = array(
            'api_key' => '',
            'db_version' => $this->db_version,
        );
    }

    function load_wp_scripts(){
        $this->options = wp_parse_args(get_option('slaask_options'), $this->option_defaults);

        if (isset($this->options['api_key'])) {
            $this->options['api_key'] = esc_attr($this->options['api_key']);
            wp_enqueue_script('chat_script', 'https://cdn.slaask.com/chat.js');
            wp_enqueue_script('chat_script_init', plugins_url('/' . self::plugin_folder_name . '/slaask_init_script.php') . '?api_key=' . $this->options['api_key']);
        }
    }

    function wp_head() {
        //
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
