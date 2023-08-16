<?php

class DS8LastestPostsAdmin {
  
	private static $initiated = false;
	private static $notices   = array();

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}

	public static function init_hooks() {
		// The standalone stats page was removed in 3.0 for an all-in-one config and stats page.
		// Redirect any links that might have been bookmarked or in browser history.
		if ( isset( $_GET['page'] ) && 'ds8lastestposts-stats-display' == $_GET['page'] ) {
			wp_safe_redirect( esc_url_raw( self::get_page_url( 'stats' ) ), 301 );
			die;
		}

		self::$initiated = true;

		add_action( 'admin_init', array( 'DS8LastestPostsAdmin', 'admin_init' ) );
		add_action( 'admin_menu', array( 'DS8LastestPostsAdmin', 'admin_menu' ), 5 );
		//add_action( 'admin_notices', array( 'DS8LastestPostsAdmin', 'display_notice' ) );
		add_filter( 'plugin_action_links', array( 'DS8LastestPostsAdmin', 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_action_links_'.plugin_basename( plugin_dir_path( __FILE__ ) . 'ds8lastestposts.php'), array( 'DS8LastestPostsAdmin', 'admin_plugin_settings_link' ) );
		//add_filter( 'all_plugins', array( 'DS8LastestPostsAdmin', 'modify_plugin_description' ) );
	}

	public static function admin_init() {
		if ( get_option( 'Activated_DS8LastestPosts' ) ) {
			delete_option( 'Activated_DS8LastestPosts' );
			if ( ! headers_sent() ) {
				wp_redirect( add_query_arg( array( 'page' => 'ds8lastestposts-key-config', 'view' => 'start' ), class_exists( 'Jetpack' ) ? admin_url( 'admin.php' ) : admin_url( 'options-general.php' ) ) );
			}
		}
                
                // JLMA - FEATURE 01-09-2022
                if(isset($_POST) && isset($_POST['option_page']) &&  $_POST['option_page'] === 'ds8-settings-group') {
                    update_option('plugin_permalinks_flushed', 0);
                }
                
                register_setting('ds8-settings-group', 'ds8_tabla_page');
		load_plugin_textdomain( 'ds8lastestposts' );
	}

	public static function admin_menu() {
			self::load_menu();
	}
	
	public static function admin_plugin_settings_link( $links ) { 
  		$settings_link = '<a href="'.esc_url( self::get_page_url() ).'">'.__('Settings', 'ds8lastestposts').'</a>';
  		array_unshift( $links, $settings_link ); 
  		return $links; 
	}

	public static function load_menu() {
		
                $hook = add_options_page( __('FD API', 'ds8lastestposts'), __('FD API', 'ds8lastestposts'), 'manage_options', 'ds8lastestposts-key-config', array( 'DS8LastestPostsAdmin', 'display_page' ) );

	}
        
        public static function display_page() {
		if ( ( isset( $_GET['view'] ) && $_GET['view'] == 'start'  ) || $_GET['page'] == 'ds8lastestposts-key-config' ){
			//self::display_start_page();
                        //DS8_Latest_Posts::view( 'start' );
                        // FEATURE JLMA 29-08-2022
                        $options = array(
                            array("name" => "Dominio a consultar API",
                                "desc" => "Para la consulta / creación y validación de las URL's del shortcode",
                                "id" => "ds8_tabla_page",
                                "type" => "text",
                                "std" => ""
                            )
                        );
                        DS8_Latest_Posts::view( 'start', array(
                                'front_page_elements' => null,
                                'options' => $options
                        ) );
                }
	}

	public static function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( plugin_dir_url( __FILE__ ) . '/ds8lastestposts.php' ) ) {
			$links[] = '<a href="' . esc_url( self::get_page_url() ) . '">'.esc_html__( 'Settings' , 'ds8lastestposts').'</a>';
		}

		return $links;
	}

	public static function display_alert() {
		DS8_Latest_Posts::view( 'notice', array(
			'type' => 'alert',
			'code' => (int) get_option( 'ds8lastestposts_alert_code' ),
			'msg'  => get_option( 'ds8lastestposts_alert_msg' )
		) );
	}
        
        public static function get_page_url( $page = 'config' ) {

		$args = array( 'page' => 'ds8lastestposts-key-config' );
		$url = add_query_arg( $args,  admin_url( 'options-general.php' ) );

		return $url;
	}
        
        public static function plugin_deactivation( ) {
          
        }
        
        // FEATURE JLMA 29-08-2022
        public static function create_form($options) {
            foreach ($options as $value) {
                switch ($value['type']) {
                    case "textarea";
                        self::create_section_for_textarea($value);
                        break;
                    case "text";
                        self::create_section_for_text($value);
                        break;
                    case "select":
                        self::create_section_for_taxonomy_select($value);
                        break;
                    case "select-page":
                        self::combo_select_page_callback($value);
                        break;
                }
            }
        }
        
        public static function ds8_get_formatted_page_array() {

            $ret = array();
            $pages = get_pages();
            if ($pages != null) {
                foreach ($pages as $page) {
                    $ret[$page->ID] = array("name" => $page->post_title, "id" => $page->ID);
                }
            }

            return $ret;
        }

        public static function combo_select_page_callback($value) {
            echo '<tr valign="top">';
            echo '<th scope="row">' . $value['name'] . '</th>';
            echo '<td>';

            echo "<select id='" . $value['id'] . "' class='post_form' name='" . $value['id'] . "'>\n";
            echo "<option value='0'>-- Select page --</option>";

            $pages = get_pages();

            foreach ($pages as $page) {
                $checked = ' ';

                if (get_option($value['id']) == $page->ID) {
                    $checked = ' selected="selected" ';
                } else if (get_option($value['id']) === FALSE && $value['std'] == $page->ID) {
                    $checked = ' selected="selected" ';
                } else {
                    $checked = '';
                }

                echo '<option value="' . $page->ID . '" ' . $checked . '/>' . $page->post_title . "</option>\n";
            }
            echo "</select>";
            echo "</td>";
            echo '</tr>';
        }

        public static function create_section_for_taxonomy_select($value) {
            echo '<tr valign="top">';
            echo '<th scope="row">' . $value['name'] . '</th>';
            echo '<td>';

            echo "<select id='" . $value['id'] . "' class='post_form' name='" . $value['id'] . "'>\n";
            echo "<option value='0'>-- Seleccione --</option>";

            foreach ($value['options'] as $option_value => $option_list) {
                $checked = ' ';

                if (get_option($value['id']) == $option_value) {
                    $checked = ' selected="selected" ';
                } else if (get_option($value['id']) === FALSE && $value['std'] == $option_list) {
                    $checked = ' selected="selected" ';
                } else {
                    $checked = '';
                }

                echo '<option value="' . $option_value . '" ' . $checked . '/>' . $option_list . "</option>\n";
            }
            echo "</select>";
            echo "</td>";
            echo '</tr>';
        }

        public static function create_section_for_textarea($value) {
            echo '<tr valign="top">';
            echo '<th scope="row">' . $value['name'] . '</th>';

            $text = "";
            if (get_option($value['id']) === FALSE) {
                $text = $value['std'];
            } else {
                $text = get_option($value['id']);
            }

            echo '<td><textarea rows="6" cols="80" id="' . $value['id'] . '" name="' . $value['id'] . '">'.strip_tags($text).'</textarea></td>';
            echo '</tr>';
        }

        public static function create_section_for_text($value) {
            echo '<tr valign="top">';
            echo '<th scope="row">' . $value['name'] . '</th>';

            $text = "";
            if (get_option($value['id']) === FALSE) {
                $text = $value['std'];
            } else {
                $text = get_option($value['id']);
            }

            echo '<td><input type="text" id="' . $value['id'] . '" name="' . $value['id'] . '" value="' . $text . '" /></td>';
            echo '</tr>';
        }
	
}
