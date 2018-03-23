<?php
/**
 * Check for premium plugins updates
 *
 * @package nk-themes-helper
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class NK_Helper_Plugins_Updater
 */
class NK_Helper_Plugins_Updater {
    /**
     * NK_Helper_Plugins_Updater constructor.
     */
    public function __construct() {
        $this->init_actions();
    }

    /**
     * Premium plugins array
     *
     * @var array
     */
    private $premium_plugins = array(
        'advanced-custom-fields-pro',
        'js_composer',
    );

    /**
     * Init actions
     */
    private function init_actions() {
        // For active themes only.
        if ( ! nk_theme()->theme_dashboard()->is_envato_hosted && ! nk_theme()->theme_dashboard()->activation()->active ) {
            return;
        }

        // Return ACF pro fake license to prevent notices.
        add_filter( 'option_acf_pro_license', array( $this, 'acf_pro_license' ), 20, 1 );
        add_filter( 'pre_option_acf_pro_license', array( $this, 'acf_pro_license' ), 20, 1 );

        // Return VC fake license to prevent notices.
        add_filter( 'option_wpb_js_js_composer_purchase_code', array( $this, 'js_composer_license' ), 20, 1 );
        add_filter( 'pre_option_wpb_js_js_composer_purchase_code', array( $this, 'js_composer_license' ), 20, 1 );
        add_filter( 'site_option_wpb_js_js_composer_purchase_code', array( $this, 'js_composer_license' ), 20, 1 );
        add_filter( 'pre_site_option_wpb_js_js_composer_purchase_code', array( $this, 'js_composer_license' ), 20, 1 );

        // Prevent VC upgrader work.
        add_filter( 'upgrader_pre_download', array( $this, 'js_composer_pre_download_1' ), 1, 4 );
        add_filter( 'upgrader_pre_download', array( $this, 'js_composer_pre_download_2' ), 20, 4 );

        // Modify update information for premium plugins.
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'modify_plugins_transient' ), 20, 1 );
    }

    /**
     * Get TGM premium plugins
     *
     * @return array
     */
    public function get_tgm_premium_plugins() {
        if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
            return false;
        }
        $plugins = TGM_Plugin_Activation::$instance->plugins;
        $premium_plugins = array();

        foreach ( $this->premium_plugins as $slug ) {
            if ( isset( $plugins[ $slug ] ) ) {
                $premium_plugins[ $slug ] = $plugins[ $slug ];
            }
        }

        return $premium_plugins;
    }

    /**
     *  Return ACF pro fake license to prevent notices
     *
     *  @param mixed $license - license.
     *  @return mixed
     */
    public function acf_pro_license( $license ) {
        if ( ! $license ) {
            return base64_encode( maybe_serialize( array(
                'key' => 'fake',
                'url' => home_url(),
            ) ) );
        }

        return $license;
    }

    /**
     * Return VC fake license to prevent notices
     *
     * @param mixed $license - license.
     * @return mixed
     */
    public function js_composer_license( $license ) {
        return $license ? : 'fake';
    }

    /**
     * Change VC data to fake to prevent VC upgrader start
     *
     * @param mixed       $return - return data.
     * @param object      $package - package data.
     * @param WP_Upgrader $updater - upgrader data.
     * @return mixed
     */
    public function js_composer_pre_download_1( $return, $package, $updater ) {
        if ( isset( $updater->skin->plugin ) && 'js_composer/js_composer.php' === $updater->skin->plugin ) {
            $updater->skin->plugin .= ' fake';
        }
        if ( isset( $updater->skin->plugin_info ) && 'WPBakery Page Builder' === $updater->skin->plugin_info['Name'] ) {
            $updater->skin->plugin_info['Name'] .= ' fake';
        }
        return $return;
    }

    /**
     * Change VC data back to normal after VC upgrader code end
     *
     * @param mixed       $return - return data.
     * @param object      $package - package data.
     * @param WP_Upgrader $updater - upgrader data.
     * @return mixed
     */
    public function js_composer_pre_download_2( $return, $package, $updater ) {
        if ( isset( $updater->skin->plugin ) && 'js_composer/js_composer.php fake' === $updater->skin->plugin ) {
            $updater->skin->plugin = 'js_composer/js_composer.php';
        }
        if ( isset( $updater->skin->plugin_info ) && 'WPBakery Page Builder fake' === $updater->skin->plugin_info['Name'] ) {
            $updater->skin->plugin_info['Name'] = 'WPBakery Page Builder';
        }
        return $return;
    }

    /**
     * Modify premium plugins update information
     *
     * @param object $transient - plugin data.
     * @return object
     */
    public function modify_plugins_transient( $transient ) {
        // bail early if no response (error).
        if ( ! isset( $transient->response ) ) {
            return $transient;
        }

        // get all premium plugins.
        $plugins = $this->get_tgm_premium_plugins();

        if ( ! $plugins || empty( $plugins ) ) {
            return $transient;
        }

        foreach ( $plugins as $plugin ) {
            // only for external source type.
            if ( 'external' !== $plugin['source_type'] ) {
                continue;
            }

            // check if available transient for this plugin.
            if ( ! isset( $transient->response[ $plugin['file_path'] ] ) ) {
                continue;
            }

            $transient->response[ $plugin['file_path'] ]->package = $plugin['source'];
        }

        return $transient;
    }
}
