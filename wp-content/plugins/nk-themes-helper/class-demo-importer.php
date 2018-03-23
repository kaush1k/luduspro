<?php
/**
 * Demo Import Helper
 *
 * Example:
 *   // import all demo data
 *   echo '<br><h4>Demo Data:</h4>';
 *   nk_theme()->demo_importer()->import_demo_data($import_data_file);
 *
 *   // setup widgets
 *   echo '<br><h4>Widgets:</h4>';
 *   nk_theme()->demo_importer()->import_demo_widgets($import_widgets_file);
 *
 *   // options tree importer
 *   echo '<br><h4>Theme Options:</h4>';
 *   nk_theme()->demo_importer()->import_demo_options_tree($import_options_file);
 *
 * @package nk-themes-helper
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class NK_Helper_Demo_Importer
 */
class NK_Helper_Demo_Importer {
    /**
     * The single class instance.
     *
     * @var object
     */
    private static $_instance = null;

    /**
     * Time in milliseconds, marking the beginning of the import.
     *
     * @var float
     */
    private $microtime;

    /**
     * Main Instance
     * Ensures only one instance of this class exists in memory at any one time.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * NK_Helper_Demo_Importer constructor.
     */
    private function __construct() {
        /* We do nothing here! */
    }

    /**
     * Prepare demo importer
     */
    private function prepare_demo_importer() {
        // set time limit to prevent demo import failings.
        set_time_limit( 300 );

        if ( ! class_exists( '\WP_Importer' ) ) {
            defined( 'WP_LOAD_IMPORTERS' ) || define( 'WP_LOAD_IMPORTERS', true );
            require ABSPATH . '/wp-admin/includes/class-wp-importer.php';
        }

        if ( ! class_exists( 'WXR_Importer' ) ) {
            $wxr_importer_path = nk_theme()->plugin_path . 'vendor/wxr-importer/class-wxr-importer.php';
            $wxr_importer_info = nk_theme()->plugin_path . 'vendor/wxr-importer/class-wxr-import-info.php';
            $wxr_logger_path = nk_theme()->plugin_path . 'vendor/wxr-importer/class-logger.php';
            $wxr_logger_html_path = nk_theme()->plugin_path . 'vendor/wxr-importer/class-logger-html.php';
            $wxr_logger_serversentevents = nk_theme()->plugin_path . 'vendor/wxr-importer/class-logger-serversentevents.php';

            if ( file_exists( $wxr_importer_path ) ) {
                require_once( $wxr_importer_path );
            }
            if ( file_exists( $wxr_importer_info ) ) {
                require_once( $wxr_importer_info );
            }
            if ( file_exists( $wxr_logger_path ) ) {
                require_once( $wxr_logger_path );
            }
            if ( file_exists( $wxr_logger_html_path ) ) {
                require_once( $wxr_logger_html_path );
            }
            if ( file_exists( $wxr_logger_serversentevents ) ) {
                require_once( $wxr_logger_serversentevents );
            }

            $nk_wxr_importer_path = nk_theme()->plugin_path . 'vendor/nk-wxr-importer.php';
            if ( file_exists( $nk_wxr_importer_path ) ) {
                require_once( $nk_wxr_importer_path );
            }
        }

        if ( ! class_exists( 'Customizer_Import' ) ) {
            $customizer_importer_path = nk_theme()->plugin_path . 'vendor/customizer-importer/customizer-importer.php';
            $nk_customizer_importer_path = nk_theme()->plugin_path . 'vendor/nk-customizer-importer.php';
            if ( file_exists( $customizer_importer_path ) ) {
                require_once( $customizer_importer_path );
            }
            if ( file_exists( $nk_customizer_importer_path ) ) {
                require_once( $nk_customizer_importer_path );
            }
        }

        if ( ! function_exists( 'wie_import_data' ) ) {
            $widgets_importer_path = nk_theme()->plugin_path . 'vendor/widgets-importer/widgets_import.php';
            if ( file_exists( $widgets_importer_path ) ) {
                require_once( $widgets_importer_path );
            }
        }
    }


    /***
     * NEW IMPORTER WITH STREAMING PROCESS
     * idea from https://github.com/humanmade/WordPress-Importer/blob/master/class-wxr-import-ui.php
     */

    /**
     * Max delta
     *
     * @var int
     */
    public $max_delta = 0;

    /**
     * Current delta
     *
     * @var int
     */
    public $delta = 0;

    /**
     * Resume delta
     *
     * @var int
     */
    public $resume_delta = 0;

    /**
     * Logger
     *
     * @var object
     */
    public $logger;

    /**
     * WP Import
     *
     * @var object
     */
    public $wp_import;

    /**
     * Imported images
     *
     * @var array
     */
    public $imported_images = array();

    /**
     * Stream Import
     *
     * @param array $settings settings.
     */
    public function stream_import( $settings = array() ) {
        // get settings.
        $settings = array_merge(
            array(
                // array with blog options
                // Example:
                // array(
                // 'permalink' => '/%postname%/',
                // 'page_on_front_title' => 'GodLike',
                // 'page_for_posts_title' => 'News',
                // 'posts_per_page' => 6
                // )
                // .
                'blog_options' => false,

                // set woocommerce pages
                // Example:
                // array(
                // 'shop_page_title' => 'Shop',
                // 'cart_page_title' => 'Cart',
                // 'checkout_page_title' => 'Checkout',
                // 'myaccount_page_title' => 'My Account',
                // )
                // .
                'woocommerce_options' => false,

                // set navigations
                // Example:
                // array(
                // 'Top Menu' => 'top_menu',
                // 'Main Menu' => 'primary',
                // 'Some Menu Locations' => array('primary', 'top_menu'),
                // )
                // .
                'navigations' => false,

                // exported files to import.
                'demo_data_file' => false,
                'widgets_file' => false,
                'customizer_file' => false,
                'rev_slider_file' => false,
            ), $settings
        );

        // Turn off PHP output compression.
        $previous = error_reporting( error_reporting() ^ E_WARNING );
        ini_set( 'output_buffering', 'off' );
        ini_set( 'zlib.output_compression', false );
        error_reporting( $previous );

        if ( $GLOBALS['is_nginx'] ) {
            // Setting this header instructs Nginx to disable fastcgi_buffering
            // and disable gzip for this request.
            header( 'X-Accel-Buffering: no' );
            header( 'Content-Encoding: none' );
        }

        // Start the event stream.
        header( 'Content-Type: text/event-stream' );
        header( 'Cache-Control: no-cache' );

        // 2KB padding for IE
        echo ':' . esc_html( str_repeat( ' ', 2048 ) ) . "\n\n";

        // Time to run the import!
        set_time_limit( 300 );

        // Ensure we're not buffered.
        if ( ob_get_length() ) {
            ob_end_flush();
        }
        flush();

        // prepare all importer libraries.
        $this->prepare_demo_importer();

        // main importer.
        $this->wp_import = new NK_WXR_Importer(
            array(
                'fetch_attachments' => true,
            )
        );

        // init logger.
        $this->logger = new WP_Importer_Logger_ServerSentEvents();

        // get max_delta information.
        if ( $settings['demo_data_file'] ) {
            $info = $this->wp_import->get_preliminary_information( $settings['demo_data_file'] );
            $this->max_delta += isset( $info->comment_count ) ? $info->comment_count : 0;
            $this->max_delta += isset( $info->media_count ) ? $info->media_count : 0;
            $this->max_delta += isset( $info->post_count ) ? $info->post_count : 0;
            $this->max_delta += isset( $info->term_count ) ? $info->term_count : 0;
        }
        foreach ( $settings as $k => $setting ) {
            if ( is_array( $setting ) ) {
                $this->max_delta += count( $setting );
            } else if ( 'demo_data_file' !== $k ) {
                $this->max_delta += 1;
            }
        }

        // Import XML file.
        if ( $settings['demo_data_file'] ) {
            $this->stream_import_demo_data( $settings['demo_data_file'] );
        }

        // import widgets.
        if ( $settings['widgets_file'] && $this->resume_delta < $this->delta ) {
            $this->stream_import_widgets( $settings['widgets_file'] );
        }
        $this->update_delta( 'import_widgets' );
        $this->new_request_maybe( array() );

        // import customizer.
        if ( $settings['customizer_file'] && $this->resume_delta < $this->delta ) {
            $this->stream_import_customizer( $settings['customizer_file'] );
        }
        $this->update_delta( 'import_customizer' );
        $this->new_request_maybe( array() );

        // setup navigations.
        if ( $settings['navigations'] && is_array( $settings['navigations'] ) && $this->resume_delta < $this->delta ) {
            $this->stream_setup_navigations( $settings['navigations'] );
        }
        $this->update_delta( 'setup_navigations' );
        $this->new_request_maybe( array() );

        // update blog options.
        if ( $settings['blog_options'] && is_array( $settings['blog_options'] ) && $this->resume_delta < $this->delta ) {
            $this->stream_blog_options( $settings['blog_options'] );
        }
        $this->update_delta( 'blog_options' );
        $this->new_request_maybe( array() );

        // update WooCommerce options.
        if ( $settings['woocommerce_options'] && is_array( $settings['woocommerce_options'] ) && class_exists( 'WooCommerce' ) && $this->resume_delta < $this->delta ) {
            $this->stream_woocommerce_options( $settings['woocommerce_options'] );
        }
        $this->update_delta( 'woocommerce_options' );
        $this->new_request_maybe( array() );

        // import RevSlider.
        if ( $settings['rev_slider_file'] && class_exists( 'RevSlider' ) && $this->resume_delta < $this->delta ) {
            $this->stream_import_rev_slider( $settings['rev_slider_file'] );
        }
        $this->update_delta( 'import_rev_slider' );
        $this->new_request_maybe( array() );

        // Done.
        $this->logger->info( __( 'Demo data successfully imported!', 'nk-themes-helper' ) );
        delete_transient( 'nk_theme_importer_delta' );
        $this->emit_sse_message(
            array(
                'action' => 'complete',
                'error' => false,
            )
        );
    }

    /**
     * Import demo data using stream
     *
     * @param string $file - file name.
     */
    public function stream_import_demo_data( $file ) {
        $this->microtime = microtime( true );

        add_action( 'wxr_importer.processed.post', array( $this, 'imported_post' ), 10, 2 );
        add_action( 'wxr_importer.process_failed.post', array( $this, 'imported_post' ), 10, 2 );
        add_action( 'wxr_importer.process_already_imported.post', array( $this, 'imported_post' ), 10, 2 );
        add_action( 'wxr_importer.process_skipped.post', array( $this, 'imported_post' ), 10, 2 );
        add_action( 'wxr_importer.processed.comment', array( $this, 'imported_comment' ) );
        add_action( 'wxr_importer.process_already_imported.comment', array( $this, 'imported_comment' ) );
        add_action( 'wxr_importer.processed.term', array( $this, 'imported_term' ) );
        add_action( 'wxr_importer.process_failed.term', array( $this, 'imported_term' ) );
        add_action( 'wxr_importer.process_already_imported.term', array( $this, 'imported_term' ) );

        // Disable users import.
        add_filter( 'wxr_importer.pre_process.user', '__return_false' );

        // Check, if we need to send another AJAX request.
        add_filter( 'wxr_importer.pre_process.post', array( $this, 'new_request_maybe' ) );
        add_filter( 'wxr_importer.pre_process.comment', array( $this, 'new_request_maybe' ) );
        add_filter( 'wxr_importer.pre_process.term', array( $this, 'new_request_maybe' ) );

        // Set the importing author to the current user.
        add_filter( 'wxr_importer.pre_process.post', array( $this, 'set_post_author' ) );

        if ( ! $this->wp_import ) {
            $this->wp_import = new NK_WXR_Importer(
                array(
                    'fetch_attachments' => true,
                )
            );
        }

        // get delta from the previous demo run.
        $this->resume_delta = get_transient( 'nk_theme_importer_delta' ) ? : 0;

        $this->wp_import->set_logger( $this->logger );

        $err = $this->wp_import->import( $file );

        if ( is_wp_error( $err ) ) {
            $this->emit_sse_message(
                array(
                    'action' => 'error',
                    'error' => $err->get_error_message(),
                )
            );
            exit;
        }
    }

    /**
     * Check if we need to create a new AJAX request, so that server does not timeout.
     *
     * @param array $data current post data.
     * @return array
     */
    public function new_request_maybe( $data ) {
        $time = microtime( true ) - $this->microtime;

        // We should make a new ajax call, if the time is right.
        // 25 seconds per request.
        if ( $time > 25 ) {
            // Set the current importer stat, so it will be used on the next ajax call.
            if ( $this->wp_import ) {
                set_transient( 'nk_theme_importer_delta', $this->delta, 0.1 * HOUR_IN_SECONDS );
            }

            $this->logger->info( __( 'New Request...', 'nk-themes-helper' ) );
            $this->emit_sse_message(
                array(
                    'action' => 'new_ajax',
                    'type'   => 'new_ajax',
                )
            );

            wp_die(
                '', '', array(
                    'response' => null,
                )
            );
        }

        // update delta.
        $this->delta += 1;

        if ( $this->resume_delta >= $this->delta ) {
            return array();
        }

        return $data;
    }

    /**
    // Set importing author to the current user.
    // Fixes the [WARNING] Could not find the author for ... log warning messages.
     *
     * @param array $data current post data.
     * @return array
     */
    public function set_post_author( $data ) {
        if ( ! empty( $data ) ) {
            $current_user_obj    = wp_get_current_user();
            $data['post_author'] = $current_user_obj->user_login;
        }
        return $data;
    }

    /**
     * Import widgets
     *
     * @param string $file - file name.
     */
    public function stream_import_widgets( $file ) {
        $this->prepare_demo_importer();
        if ( ! file_exists( $file ) ) {
            $this->logger->info( __( 'Widgets import file could not be found.', 'nk-themes-helper' ) );
            return;
        }
        $data = file_get_contents( $file );
        $data = json_decode( $data );
        wie_import_data( $data );
        $this->logger->info( __( 'Widgets imported', 'nk-themes-helper' ) );
    }

    /**
     * Import customizer
     *
     * @param string $file - file name.
     */
    public function stream_import_customizer( $file ) {
        $this->prepare_demo_importer();
        $importer = new NK_Customizer_Import();
        $importer->import_images = true;
        $importer->_import( $file );
        $this->logger->info( __( 'Customizer options imported', 'nk-themes-helper' ) );
    }

    /**
     * Import rev slider
     *
     * @param string $file - file name.
     * @param object $slider - rev slider object.
     */
    public function stream_import_rev_slider( $file, $slider = false ) {
        if ( ! class_exists( 'RevSlider' ) ) {
            return;
        }
        if ( ! $slider ) {
            $slider = new RevSlider();
        }
        if ( is_array( $file ) ) {
            foreach ( $file as $a ) {
                $this->stream_import_rev_slider( $a, $slider );
            }
            return;
        }
        $this->prepare_demo_importer();
        if ( file_exists( $file ) ) {
            $file_hash = md5_file( $file );

            // check if slider already exists.
            $imported = nk_theme()->get_option( 'revslider_' . $file_hash, false );
            if ( $imported ) {
                $all_sliders = $slider->getArrSlidersShort();
                if ( isset( $all_sliders[ $imported ] ) ) {
                    return;
                }
            }

            // import new slider.
            $response = $slider->importSliderFromPost( true, true, $file );
            if ( $response && isset( $response['sliderID'] ) ) {
                nk_theme()->update_option( 'revslider_' . $file_hash, $response['sliderID'] );
            }
            // translators: %s.
            $this->logger->info( sprintf( __( 'RevSlider %s imported', 'nk-themes-helper' ), basename( $file ) ) );
        }
    }

    /**
     * Setup navigations
     *
     * @param array $navigations - navigations.
     */
    public function stream_setup_navigations( $navigations = array() ) {
        $locations = get_theme_mod( 'nav_menu_locations', array() );
        $menus = wp_get_nav_menus();
        if ( $menus ) {
            foreach ( $menus as $menu ) {
                if ( isset( $navigations[ $menu->name ] ) ) {
                    if ( is_array( $navigations[ $menu->name ] ) ) {
                        foreach ( $navigations[ $menu->name ] as $menu_name ) {
                            $locations[ $menu_name ] = $menu->term_id;
                        }
                    } else {
                        $locations[ $navigations[ $menu->name ] ] = $menu->term_id;
                    }
                }
            }
        }
        set_theme_mod( 'nav_menu_locations', $locations );
        $this->logger->info( __( 'Navigations added to their locations', 'nk-themes-helper' ) );
    }

    /**
     * Setup blog options
     *
     * @param array $options - options.
     */
    public function stream_blog_options( $options = array() ) {
        foreach ( $options as $name => $value ) {
            switch ( $name ) {
                case 'permalink':
                    global $wp_rewrite;
                    $wp_rewrite->set_permalink_structure( $value );
                    break;

                // home page.
                case 'page_on_front_title':
                    // @codingStandardsIgnoreLine
                    $homepage = get_page_by_title( $value );
                    if ( isset( $homepage ) && $homepage->ID ) {
                        update_option( 'show_on_front', 'page' );
                        update_option( 'page_on_front', $homepage->ID );
                    }
                    break;

                // blog page.
                case 'page_for_posts_title':
                    // @codingStandardsIgnoreLine
                    $blog = get_page_by_title( $value );
                    if ( isset( $blog ) && $blog->ID ) {
                        update_option( 'page_for_posts', $blog->ID );
                    }
                    break;

                // default options.
                default:
                    update_option( $name, $value );
                    break;
            }
        }
        $this->logger->info( __( 'Blog settings imported', 'nk-themes-helper' ) );
    }

    /**
     * Setup WooCommerce options
     *
     * @param array $options - options.
     */
    public function stream_woocommerce_options( $options = array() ) {
        foreach ( $options as $name => $value ) {
            switch ( $name ) {
                // default pages by title.
                case 'shop_page_title':
                case 'cart_page_title':
                case 'checkout_page_title':
                case 'myaccount_page_title':
                // @codingStandardsIgnoreLine
                    $page = get_page_by_title( $value );
                    if ( isset( $page ) && $page->ID ) {
                        update_option( 'woocommerce_' . str_replace( '_title', '', $name ) . '_id', $page->ID );
                    }
                    break;

                // default options.
                default:
                    update_option( $name, $value );
                    break;
            }
        }
        $this->logger->info( __( 'WooCommerce settings imported', 'nk-themes-helper' ) );
    }

    /**
     * Emit a Server-Sent Events message.
     *
     * @param mixed $data Data to be JSON-encoded and sent in the message.
     */
    protected function emit_sse_message( $data ) {
        $data['max_delta'] = isset( $this->max_delta ) ? $this->max_delta : 0;

        echo "event: message\n";
        echo 'data: ' . wp_json_encode( $data ) . "\n\n";
        // Extra padding.
        echo ':' . esc_html( str_repeat( ' ', 2048 ) ) . "\n\n";

        if ( ob_get_length() ) {
            ob_end_flush();
        }
        flush();
    }

    /**
     * Send message when a post has been imported.
     *
     * @param string $type type.
     */
    public function update_delta( $type ) {
        $this->emit_sse_message(
            array(
                'action' => 'updateDelta',
                'type'   => $type,
                'delta'  => $this->delta,
            )
        );
    }

    /**
     * Send message when a post has been imported.
     *
     * @param int|WP_Error $id Post ID.
     * @param array        $data Post data saved to the DB.
     */
    public function imported_post( $id, $data ) {
        if ( ! is_wp_error( $id ) ) {
            /**
             * Skip error message
                $this->emit_sse_message(
                    array(
                        'action' => 'error',
                        'error' => $id->get_error_message() . ' [' . $id->get_error_code() . ']',
                    )
                );
                } else {
             */
            if ( 'attachment' === $data['post_type'] ) {
                $this->imported_images[] = 'ID: ' . $id . 'URL: ' . ( ! empty( $data['attachment_url'] ) ? $data['attachment_url'] : $data['guid'] );
            }
        }
        $this->update_delta( 'attachment' === $data['post_type'] ? 'media' : 'posts' );
    }

    /**
     * Send message when a comment has been imported.
     */
    public function imported_comment() {
        $this->update_delta( 'comments' );
    }

    /**
     * Send message when a term has been imported.
     */
    public function imported_term() {
        $this->update_delta( 'terms' );
    }



    /**
     * DEPRECATED old importer methods
     */

    // @codingStandardsIgnoreStart
    public function import_demo_data( $file ) {
        $this->prepare_demo_importer();
        $this->logger = new WP_Importer_Logger_HTML();
        $this->wp_import = new NK_WXR_Importer(
            array(
                'fetch_attachments' => true,
            )
        );
        $this->wp_import->set_logger( $this->logger );

        $result = $this->wp_import->import( $file );

        if ( is_wp_error( $result ) ) {
            echo $result->get_error_message();
            return $result;
        }
    }
    private function nk_wie_import_data( $file ) {
        if ( ! file_exists( $file ) ) {
            return new WP_Error( 'widget-import-error', esc_html__( 'Widgets import file could not be found.', 'nk-themes-helper' ) );
        }
        $data = file_get_contents( $file );
        $data = json_decode( $data );
        return wie_import_data( $data );
    }
    public function import_demo_widgets( $file ) {
        $this->prepare_demo_importer();
        $import_widgets_result = $this->nk_wie_import_data( $file );
        if ( is_wp_error( $import_widgets_result ) ) {
            echo '<p>' . $import_widgets_result->get_error_message() . '</p>';
        } else {
            echo '<p>Widgets imported.</p>';
        }
    }
    public function import_rev_slider( $file, $slider = false ) {
        if ( ! class_exists( 'RevSlider' ) ) {
            echo '<p>Revolution Slider plugin is not installed.</p>';
            return;
        }
        if ( ! $slider ) {
            $slider = new RevSlider();
        }
        if ( is_array( $file ) ) {
            foreach ( $file as $a ) {
                $this->import_rev_slider( $a, $slider );
            }
            return;
        }
        $this->prepare_demo_importer();
        if ( file_exists( $file ) ) {
            $file_hash = md5_file( $file );

            // check if slider already exists
            $imported = nk_theme()->get_option( 'revslider_' . $file_hash, false );
            if ( $imported ) {
                $all_sliders = $slider->getArrSlidersShort();
                if ( isset( $all_sliders[ $imported ] ) ) {
                    return;
                }
            }

            // import new slider
            $response = $slider->importSliderFromPost( true, true, $file );
            if ( $response && isset( $response['sliderID'] ) ) {
                nk_theme()->update_option( 'revslider_' . $file_hash, $response['sliderID'] );
            }
            echo '<p>' . basename( $file ) . ' imported.</p>';
        }
    }
    public function import_demo_options_tree( $file ) {
        $this->prepare_demo_importer();
        if ( function_exists( 'ot_options_id' ) && file_exists( $file ) ) {
            $import_options_data = file_get_contents( $file );
            $import_options_data = maybe_unserialize( base64_decode( $import_options_data ) );

            if ( ! empty( $import_options_data ) || is_array( $import_options_data ) ) {
                update_option( ot_options_id(), $import_options_data );
                echo '<p>Options imported.</p>';
            } else {
                echo '<p>Options import error.</p>';
            }
        }
    }
    public function import_demo_customizer( $file ) {
        $this->prepare_demo_importer();
        $importer = new NK_Customizer_Import();
        $importer->import_images = true;
        $importer->_import( $file );
    }
    // @codingStandardsIgnoreEnd
}
