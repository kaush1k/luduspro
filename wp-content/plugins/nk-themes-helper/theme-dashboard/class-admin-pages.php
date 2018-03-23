<?php
/**
 * Admin Class for _nK themes
 *
 * @package nk-themes-helper
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class NK_Helper_Theme_Dashboard_Pages
 */
class NK_Helper_Theme_Dashboard_Pages {
    /**
     * The single class instance.
     *
     * @since 1.0.0
     * @access private
     *
     * @var object
     */
    private static $_instance = null;

    /**
     * Main Instance
     * Ensures only one instance of this class exists in memory at any one time.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
            self::$_instance->init_globals();
        }
        return self::$_instance;
    }

    /**
     * NK_Helper_Theme_Dashboard_Pages constructor.
     */
    private function __construct() {
        /* We do nothing here! */
    }

    /**
     * Init Global variables
     */
    private function init_globals() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'admin_body_class', array( $this, 'admin_body_class' ) );
    }

    /**
     * Admin init action
     */
    public function admin_init() {
        if ( isset( $_GET['nk-deactivate'] ) && 'deactivate-plugin' == $_GET['nk-deactivate'] ) {
            check_admin_referer( 'nk-deactivate', 'nk-deactivate-nonce' );

            $plugins = TGM_Plugin_Activation::$instance->plugins;

            foreach ( $plugins as $plugin ) {
                if ( isset( $_GET['plugin'] ) && $plugin['slug'] === $_GET['plugin'] ) {
                    deactivate_plugins( $plugin['file_path'] );
                }
            }
        }
        if ( isset( $_GET['nk-activate'] ) && 'activate-plugin' == $_GET['nk-activate'] ) {
            check_admin_referer( 'nk-activate', 'nk-activate-nonce' );

            $plugins = TGM_Plugin_Activation::$instance->plugins;

            foreach ( $plugins as $plugin ) {
                if ( isset( $_GET['plugin'] ) && $plugin['slug'] == $_GET['plugin'] ) {
                    activate_plugin( $plugin['file_path'] );

                    wp_redirect( admin_url( 'admin.php?page=nk-theme-plugins' ) );
                    exit;
                }
            }
        }
    }

    /**
     * Admin menus
     */
    public function admin_menu() {
        if ( ! is_array( nk_theme()->theme_dashboard()->options['pages'] ) ) {
            return;
        }

        $main_item_title = nk_theme()->theme_dashboard()->theme_name;
        if ( nk_theme()->theme_dashboard()->updater()->is_update_available() ) {
            $main_item_title .= ' <span class="awaiting-mod"><span>New</span></span>';
        }

        $parent_slug = 'nk-theme';

        // add top menu.
        add_menu_page(
            $main_item_title,
            $main_item_title,
            'edit_theme_options',
            $parent_slug,
            array( $this, 'print_pages' ),
            'dashicons-admin-nk',
            '3.22222'
        );

        // add submenus.
        global $submenu;
        foreach ( nk_theme()->theme_dashboard()->options['pages'] as $name => $page ) {
            if ( isset( $page['external_uri'] ) ) {
                // @codingStandardsIgnoreLine
                $submenu[ $parent_slug ][] = array( $page['title'], 'edit_theme_options', $name );
            } else {
                add_submenu_page(
                    $parent_slug,
                    $page['title'],
                    $page['title'],
                    'edit_theme_options',
                    $name,
                    array( $this, 'print_pages' )
                );
            }
        }
    }

    /**
     * Admin body class
     *
     * @param string $classes - classes.
     *
     * @return string
     */
    public function admin_body_class( $classes ) {
        $this_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : false;
        $pages = nk_theme()->theme_dashboard()->options['pages'];
        if ( isset( $pages ) && is_array( $pages ) ) {
            foreach ( $pages as $k => $page ) {
                if ( $k === $this_page ) {
                    $classes .= ' nk-theme-page';
                    return $classes;
                }
            }
        }
        return $classes;
    }

    /**
     * Print pages
     */
    public function print_pages() {
        ?>
        <div class="wrap about-wrap nk-theme-wrap">
            <h1>
                <?php
                // translators: %s - theme name and version.
                printf( esc_html__( 'Welcome to %s', 'nk-themes-helper' ), esc_html( nk_theme()->theme_dashboard()->theme_name ) . ' <span class="nk-theme-version">v ' . esc_html( nk_theme()->theme_dashboard()->theme_version ) . '</span>' );
                ?>
            </h1>

            <div class="about-text">
                <p class="about-text">
                    <?php printf( esc_html( nk_theme()->theme_dashboard()->options['top_message'] ), esc_html( nk_theme()->theme_dashboard()->theme_name ) ); ?>
                </p>

                <a href="<?php echo esc_url( nk_theme()->theme_dashboard()->options['top_button_url'] ); ?>" class="button button-primary" target="_blank">
                    <?php printf( esc_html( nk_theme()->theme_dashboard()->options['top_button_text'] ), esc_html( nk_theme()->theme_dashboard()->theme_name ) ); ?>
                </a>
            </div>

            <?php
            $tab = 'nk-theme';
            if ( isset( $_GET['page'] ) ) {
                $tab = sanitize_text_field( wp_unslash( $_GET['page'] ) );
            }
            echo '<div id="icon-themes" class="icon32"><br></div>';
            echo '<h2 class="nav-tab-wrapper">';

            foreach ( nk_theme()->theme_dashboard()->options['pages'] as $name => $page ) {
                $class = ( $name == $tab ) ? ' nav-tab-active' : '';
                $uri = isset( $page['external_uri'] ) ? $name : menu_page_url( $name, 0 );
                echo "<a class='nav-tab" . esc_attr( $class ) . "' href='" . esc_url( $uri ) . "'>" . esc_html( $page['title'] ) . '</a>';
            }
            echo '</h2>';
            ?>

            <div id="poststuff">
                <?php
                if ( isset( nk_theme()->theme_dashboard()->options['pages'][ $tab ] ) && isset( nk_theme()->theme_dashboard()->options['pages'][ $tab ]['template'] ) ) {
                    require nk_theme()->plugin_path . '/theme-dashboard/admin_pages/' . nk_theme()->theme_dashboard()->options['pages'][ $tab ]['template'];
                }
                ?>
                <p class="description">
                    <?php echo sprintf( esc_html( nk_theme()->theme_dashboard()->options['foot_message'] ), esc_html( nk_theme()->theme_dashboard()->theme_name ) ); ?>
                </p>
            </div>

        </div>
        <?php
    }
}

