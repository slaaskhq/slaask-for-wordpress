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
	    'enable_identification' => 0,
	    'identification_fields' => array()
        );
    }

    function load_wp_scripts(){
        $this->options = wp_parse_args(get_option('slaask_options'), $this->option_defaults);

        if (isset($this->options['api_key'])) {
            wp_enqueue_script('chat_script', 'https://cdn.slaask.com/chat.js');
            wp_enqueue_script('chat_script_init', plugins_url('/' . self::plugin_folder_name . '/slaask_init_script.php'));
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
	// Slaask general settings
        add_settings_section('slaask_settings_section', 'Slaask General Settings', array($this, 'slaask_settings_callback'), 'slaask-settings');
        add_settings_field('api_key', 'Widget Key', array($this, 'widget_id_callback'), 'slaask-settings', 'slaask_settings_section');
	// Slaask identification settings
        add_settings_section('slaask_identification_settings_section', 'Slaask Identification Settings', array($this, 'slaask_settings_identify_callback'), 'slaask-settings');
        add_settings_field('slaask_enable_identification', 'Enable users identification', array($this, 'widget_enable_identification_callback'), 'slaask-settings', 'slaask_identification_settings_section');
        add_settings_field('slaask_idenficication_fields', 'Select Fields', array($this, 'widget_identification_fields_callback'), 'slaask-settings', 'slaask_identification_settings_section');
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
    
    /**
     * Add identification settings section 
     */
    function slaask_settings_identify_callback() {
        ?>
        <b>Identify your Users </b>
        <?php
    }
    
    /**
     * Add checkbox input to enable identification
     */
    function widget_enable_identification_callback() {
        ?>
        <input type="checkbox" id="slaask_options[enable_identification]" value="1" name="slaask_options[enable_identification]" <?php 
	echo $this->options['enable_identification']?"checked":""; 
	?> >
        <label for="slaask_options[enable_identification]"><?php _e('On', 'slaask'); ?></label>
        <?php
    }
    
    
    function widget_identification_fields_callback() {
	// get current user to access available data
	$user = wp_get_current_user();

	// do not add anything if the user is not loaded
	if (!$user) {
	    return;
	}

	// convert the objet in array to count the number of properties
	$arrayObj = new ArrayObject($user);
	
	// create the multiple select 
	?>
		<select name="slaask_options[identification_fields][]" multiple="true" size="<?php echo ( $arrayObj->count() + 1); ?>">
	    <?php
	    foreach ($user->data as $fieldname => $value) {
		// don't show the pass or the activation key
		if (in_array($fieldname, array('user_pass', 'user_activation_key'))) {
		    continue;
		}
		printf("<option value='%s' %s> %s ( ex: %s )</option>", $fieldname, in_array($fieldname, $this->options['identification_fields']) ? 'selected' : '', $fieldname, $value);
	    }
	    ?>
		</select>
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
	$output = array();
	$options = $this->options;
        $input['db_version']  = $this->db_version;
        foreach ($options as $key=>$value) {
	    if(!is_array($input[$key])){
		$output[$key] = sanitize_text_field($input[$key]);
	    }else {
		$output[$key] = $input[$key];
	    }
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
