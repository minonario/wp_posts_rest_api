<?php

if (!defined('ABSPATH')) exit;

class DS8_Latest_Posts {
  
        private static $initiated = false;
        private static $instance = null;
        private static $url_api = null;
        public $version;
        public $themes;
        
        /**
         * Function constructor
         */
        function __construct() {
            $this->load_dependencies();
            $this->define_admin_hooks();
            
            self::$url_api = get_option('ds8_tabla_page');
            
            //add_action( 'init', array($this, 'init' ) );
            
            add_action('wp_enqueue_scripts', array($this, 'ds8_latest_posts_javascript'), 10);
            add_shortcode( 'ds8_latest_posts', array($this, 'shortcode') );
            
            add_action('wp_ajax_lasts_action', array($this, 'ajax_render_init_lasts'));
            add_action('wp_ajax_nopriv_lasts_action', array($this, 'ajax_render_init_lasts'));
            add_filter('home_template', array($this,'load_cpt_template'), 10, 1);
            
        }
        
        public static function load_cpt_template($template) {
            global $post;
            $dictamen = get_query_var( 'dictamen' );
            
            if (!empty($dictamen) && $GLOBALS['bodyd'] !== null && $GLOBALS['bodyd'] !== false) {
                $plugin_path = plugin_dir_path( __FILE__ );
                $template_name = 'template-parts/singular.php';
                // A specific single template for my custom post type exists in theme folder? Or it also doesn't exist in my plugin?
                if($template === get_stylesheet_directory() . '/' . $template_name
                    || !file_exists($plugin_path . $template_name)) {
                    //Then return "single.php" or "single-my-custom-post-type.php" from theme directory.
                    return $template;
                }
                // If not, return my plugin custom post type template.
                return $plugin_path . $template_name;
            }
            //This is not my custom post type, do nothing with $template
            return $template;
        }
        
        public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
	}
        
        /**
	 * Initializes WordPress hooks
	 */
	private static function init_hooks() {
		self::$initiated = true;
                
                //add_rewrite_tag('%dictamen%', '([^&]+)');
                add_filter('query_vars', array('DS8_Latest_Posts', 'ds8_register_query_var') );
                
                $front_page = get_option('page_on_front');
                
                add_rewrite_rule( "dictamen/([a-z0-9-]+)[/]?$", 'index.php?dictamen=$matches[1]', 'top' );
                add_action('template_redirect', array('DS8_Latest_Posts', 'ds8_redirect') ); 
                add_filter('redirect_canonical', array('DS8_Latest_Posts', 'canonical'), 10, 2);
        }
        public static function ds8_register_query_var($query_vars){
            $query_vars[] = 'dictamen';
            return $query_vars;
        }

        public static function canonical($redirect_url, $requested_url) {
          return $requested_url;
        }
        
        public static function ds8_redirect(){
            $dictamen = get_query_var( 'dictamen' );
            
            if (!empty($dictamen)) {
                $response = wp_remote_get( self::$url_api.'/wp-json/wp/v2/posts?slug='.$dictamen);
                $body_ds8     = json_decode(wp_remote_retrieve_body( $response ),true);
                if (empty($body_ds8)){
                  $GLOBALS['bodyd'] = false;
                  global $wp_query;
                  $wp_query->set_404();
                  status_header( 404 );
                  get_template_part( 404 );
                  exit();
                  //return;
                }else{
                  $bodyd = $body_ds8[0];
                  $GLOBALS['bodyd'] = $bodyd;
                }
            }
            //status_header(200);
        }
        
        public static function ajax_render_init_lasts() {
            if (!check_ajax_referer('fd_security_nonce', 'security')) {
              wp_send_json_error('Invalid security token sent.');
              wp_die();
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              extract($_POST);
            }else{
              extract($_GET);
            }

            $response = wp_remote_get( self::$url_api.'/wp-json/wp/v2/posts/'.$idnoticia);
            $body     = json_decode(wp_remote_retrieve_body( $response ),true);


            $data = array('data' => array('content' => $body['content']['rendered'],
                          'title' => $body['title']['rendered'],
                          'date' => $body['date'],
                          'guid' => $body['guid']['rendered']));
            wp_send_json($data);
        }
        
        /**
        * Singleton pattern
        *
        * @return void
        */
        public static function get_instance() {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }

            return self::$instance;
        }
        
        private function load_dependencies() {
            // Widdget - helpres
            if (is_admin()) {
                //require_once DS8LATESTPOSTS_PLUGIN_DIR . 'includes/class-ds8-profile-user-image.php';
            }
        }
        
        /**
          * Admin hooks
          *
          * @return void
          */
        private function define_admin_hooks() {
            //add_filter('get_avatar', array($this, 'replace_gravatar_image'), 10, 6);
        }
        
        public function shortcode($atts) {
            $defaults = array(
                'per_page' => 4,
                'categories' => '',
                'view' => 'news'
            );

            $atts = wp_parse_args($atts, $defaults);
            $view = $atts['view'];
            unset($atts['view']);
            $atts = array_filter($atts);
            $params = http_build_query($atts) . "\n";
            
            $response = wp_remote_get( self::$url_api.'/wp-json/wp/v2/posts?'.$params); //offset=2&per_page=2' );
            $total = wp_remote_retrieve_header( $response, 'X-WP-Total' );
            $total_pages = wp_remote_retrieve_header( $response, 'X-WP-TotalPages' );
            
            $body = json_decode(wp_remote_retrieve_body( $response ),true);

            ob_start();
            if ($view === 'news') {
              include('template-parts/latest-posts.php');
            }else{
              include('template-parts/latest-posts-cat.php');
            }
            return ob_get_clean();
        }
        
        /**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @since    1.0
	 */
	private static function set_locale() {
		load_plugin_textdomain( 'ds8_latest_posts', false, plugin_dir_path( dirname( __FILE__ ) ) . '/languages/' );

	}
        
        public static function ds8_latest_posts_textdomain( $mofile, $domain ) {
                if ( 'ds8_latest_posts' === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
                        $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
                        $mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
                }
                return $mofile;
        }
        
        
        /**
	 * Check if plugin is active
	 *
	 * @since    1.0
	 */
	private static function is_plugin_active( $plugin_file ) {
		return in_array( $plugin_file, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
	}

        public function ds8_latest_posts_javascript(){
          
            wp_enqueue_style('cssapi-css', plugin_dir_url( __FILE__ ) . 'assets/css/cssapi.css', array(), DS8LATESTPOSTS_VERSION);
            //wp_enqueue_style('bootstrap-css', plugin_dir_url( __FILE__ ) . 'assets/css/bootstrap-grid.min.css', array(), DS8LATESTPOSTS_VERSION);
            //wp_enqueue_style('bootstrap-theme-css', plugin_dir_url( __FILE__ ) . 'assets/css/bootstrap-theme.css', array(), DS8LATESTPOSTS_VERSION);
            
            //wp_register_script( 'bootstrap.js', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap.js', array('jquery'), DS8LATESTPOSTS_VERSION, true );
            //wp_enqueue_script( 'bootstrap.js' );
            
            wp_register_script('lasts.js', plugin_dir_url( __FILE__ ) . 'assets/js/lasts.js', array('jquery'), 3); //DS8LATESTPOSTS_VERSION);
                $localize_script_args = array(
                    'ajaxurl'         => admin_url('admin-ajax.php'),
                    'security'        => wp_create_nonce( 'fd_security_nonce' )
                );
                wp_localize_script('lasts.js', 'lasts', $localize_script_args );
                wp_enqueue_script('lasts.js' );

        }

        public static function view( $name, array $args = array() ) {
                $args = apply_filters( 'ds8_latest_posts_view_arguments', $args, $name );

                foreach ( $args AS $key => $val ) {
                        $$key = $val;
                }

                load_plugin_textdomain( 'ds8_latest_posts' );

                $file = DS8LATESTPOSTS_PLUGIN_DIR . 'views/'. $name . '.php';

                include( $file );
	}
        
        public static function plugin_deactivation( ) {
            unregister_post_type( 'calendar' );
            flush_rewrite_rules();
        }

        /**
	 * Attached to activate_{ plugin_basename( __FILES__ ) } by register_activation_hook()
	 * @static
	 */
	public static function plugin_activation() {
		if ( version_compare( $GLOBALS['wp_version'], DS8LATESTPOSTS_MINIMUM_WP_VERSION, '<' ) ) {
			load_plugin_textdomain( 'ds8_latest_posts' );
                        
			$message = '<strong>'.sprintf(esc_html__( 'FD Estadisticas %s requires WordPress %s or higher.' , 'ds8_latest_posts'), DS8LATESTPOSTS_VERSION, DS8LATESTPOSTS_MINIMUM_WP_VERSION ).'</strong> '.sprintf(__('Please <a href="%1$s">upgrade WordPress</a> to a current version, or <a href="%2$s">downgrade to version 2.4 of the Akismet plugin</a>.', 'ds8_latest_posts'), 'https://codex.wordpress.org/Upgrading_WordPress', 'https://wordpress.org/extend/plugins/ds8_latest_posts/download/');

			DS8_Latest_Posts::bail_on_activation( $message );
		} elseif ( ! empty( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], '/wp-admin/plugins.php' ) ) {
                        flush_rewrite_rules();
			add_option( 'Activated_DS8_Latest_Posts', true );
		}
	}

        private static function bail_on_activation( $message, $deactivate = true ) {
?>
<!doctype html>
<html>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<style>
* {
	text-align: center;
	margin: 0;
	padding: 0;
	font-family: "Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif;
}
p {
	margin-top: 1em;
	font-size: 18px;
}
</style>
</head>
<body>
<p><?php echo esc_html( $message ); ?></p>
</body>
</html>
<?php
		if ( $deactivate ) {
			$plugins = get_option( 'active_plugins' );
			$ds8_latest_posts = plugin_basename( DS8CALENDAR__PLUGIN_DIR . 'ds8_latest_posts.php' );
			$update  = false;
			foreach ( $plugins as $i => $plugin ) {
				if ( $plugin === $ds8_latest_posts ) {
					$plugins[$i] = false;
					$update = true;
				}
			}

			if ( $update ) {
				update_option( 'active_plugins', array_filter( $plugins ) );
			}
		}
		exit;
	}

}