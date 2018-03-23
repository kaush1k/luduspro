<?php
/**
 * Theme Dashboard plugins template
 *
 * @package nk-themes-helper
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
    ?>
    <p class="about-description"><?php esc_html__( 'TGMPA library is required to show plugins', 'nk-themes-helper' ); ?></p>
    <?php
    return;
}

$plugins           = TGM_Plugin_Activation::$instance->plugins;
$installed_plugins = get_plugins();

/**
 * Print plugin actions
 *
 * @param array $item - plugin data.
 * @param array $installed_plugins - installed plugins data.
 */
function nk_helper_print_plugin_actions( $item, $installed_plugins ) {
    $item['sanitized_plugin'] = $item['name'];

    // We have a repo plugin.
    $update_version = TGM_Plugin_Activation::$instance->does_plugin_have_update( $item['slug'] );
    if ( ! $item['version'] ) {
        $item['version'] = $update_version;
    }

    /** We need to display the 'Install' hover link */
    if ( ! isset( $installed_plugins[ $item['file_path'] ] ) ) {
        printf(
            // translators: %1$s - url to plugin install.
            // translators: %2$s - plugin name.
            '<a href="%1$s" class="button button-primary" title="' . esc_attr__( 'Install %2$s', 'nk-themes-helper' ) . '">' . esc_html__( 'Install', 'nk-themes-helper' ) . '</a>',
            esc_url(
                wp_nonce_url(
                    add_query_arg(
                        array(
                            'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
                            'plugin'        => urlencode( $item['slug'] ),
                            'plugin_name'   => urlencode( $item['sanitized_plugin'] ),
                            'plugin_source' => urlencode( $item['source'] ),
                            'tgmpa-install' => 'install-plugin',
                        ),
                        TGM_Plugin_Activation::$instance->get_tgmpa_url()
                    ),
                    'tgmpa-install',
                    'tgmpa-nonce'
                )
            ),
            esc_attr( $item['sanitized_plugin'] )
        );
    }

    /** We need to display the 'Activate' hover link */
    if ( isset( $installed_plugins[ $item['file_path'] ] ) && is_plugin_inactive( $item['file_path'] ) ) {
        printf(
            // translators: %1$s - url to plugin install.
            // translators: %2$s - plugin name.
            '<a href="%1$s" class="button button-primary" title="' . esc_attr__( 'Activate %2$s', 'nk-themes-helper' ) . '">' . esc_html__( 'Activate', 'nk-themes-helper' ) . '</a>',
            esc_url(
                add_query_arg(
                    array(
                        'plugin'               => urlencode( $item['slug'] ),
                        'plugin_name'          => urlencode( $item['sanitized_plugin'] ),
                        'plugin_source'        => urlencode( $item['source'] ),
                        'nk-activate'          => 'activate-plugin',
                        'nk-activate-nonce'    => wp_create_nonce( 'nk-activate' ),
                    ),
                    admin_url( 'admin.php?page=nk-theme-plugins' )
                )
            ),
            esc_attr( $item['sanitized_plugin'] )
        );
    }

    /** We need to display the 'Update' hover link */
    if ( isset( $installed_plugins[ $item['file_path'] ] ) && version_compare( $installed_plugins[ $item['file_path'] ]['Version'], $update_version, '<' ) ) {
        printf(
            // translators: %1$s - url to plugin install.
            // translators: %2$s - plugin name.
            '<a href="%1$s" class="button button-primary" title="' . esc_attr__( 'Update %2$s', 'nk-themes-helper' ) . '">' . esc_html__( 'Update', 'nk-themes-helper' ) . '</a>',
            esc_url(
                wp_nonce_url(
                    add_query_arg(
                        array(
                            'page'          => urlencode( TGM_Plugin_Activation::$instance->menu ),
                            'plugin'        => urlencode( $item['slug'] ),
                            'tgmpa-update'  => 'update-plugin',
                            'plugin_source' => urlencode( $item['source'] ),
                            'version'       => urlencode( $item['version'] ),
                        ),
                        TGM_Plugin_Activation::$instance->get_tgmpa_url()
                    ),
                    'tgmpa-update',
                    'tgmpa-nonce'
                )
            ),
            esc_attr( $item['sanitized_plugin'] )
        );
    }

    if ( is_plugin_active( $item['file_path'] ) ) {
        printf(
            // translators: %1$s - url to plugin install.
            // translators: %2$s - plugin name.
            '<a href="%1$s" class="button button-secondary" title="' . esc_attr__( 'Deactivate %2$s', 'nk-themes-helper' ) . '">' . esc_html__( 'Deactivate', 'nk-themes-helper' ) . '</a>',
            esc_url(
                add_query_arg(
                    array(
                        'plugin'                 => urlencode( $item['slug'] ),
                        'plugin_name'            => urlencode( $item['sanitized_plugin'] ),
                        'plugin_source'          => urlencode( $item['source'] ),
                        'nk-deactivate'       => 'deactivate-plugin',
                        'nk-deactivate-nonce' => wp_create_nonce( 'nk-deactivate' ),
                    ),
                    admin_url( 'admin.php?page=nk-theme-plugins' )
                )
            ),
            esc_attr( $item['sanitized_plugin'] )
        );
    }
}

?>
<p class="about-description">
    <?php
    // translators: %s - theme name.
    printf( esc_html__( 'These plugins comes with %s theme. If you want full functionality from demo page, you should activate all of these plugins.', 'nk-themes-helper' ), esc_html( nk_theme()->theme_dashboard()->theme_name ) );
    ?>
</p>
<div class="feature-section theme-browser nk-plugins-list">
    <?php foreach ( $plugins as $plugin ) : ?>
        <div class="theme">
            <div class="theme-wrapper">
                <div class="theme-screenshot">
                    <?php
                    $thumbnail = 'assets/images/plugins/' . $plugin['slug'] . '.png';
                    if ( file_exists( nk_theme()->plugin_path . $thumbnail ) ) {
                        ?>
                         <img src="<?php echo esc_url( nk_theme()->plugin_url . $thumbnail ); ?>" alt=""/>
                        <?php
                    }
                    ?>

                    <div class="plugin-info">
                        <?php
                        if ( isset( $installed_plugins[ $plugin['file_path'] ] ) ) {
                            printf(
                                // translators: %1s - plugin version.
                                // translators: %2s - plugin author url.
                                // translators: %3s - plugin author name.
                                esc_html__( 'Version: %1s | ', 'nk-themes-helper' ) . '<a href="%2s" target="_blank">%3s</a>',
                                esc_html( $installed_plugins[ $plugin['file_path'] ]['Version'] ),
                                esc_url( $installed_plugins[ $plugin['file_path'] ]['AuthorURI'] ),
                                esc_html( $installed_plugins[ $plugin['file_path'] ]['Author'] )
                            );
                        } elseif ( 'bundled' == $plugin['source_type'] ) {
                            // translators: %s - plugin version.
                            printf( esc_attr__( 'Available Version: %s', 'nk-themes-helper' ), esc_html( $plugin['version'] ) );
                        }
                        ?>
                    </div>
                </div>
                <div class="theme-id-container">
                    <h3 class="theme-name">
                        <?php echo esc_html( $plugin['name'] ); ?>
                    </h3>
                    <div class="theme-actions">
                        <?php nk_helper_print_plugin_actions( $plugin, $installed_plugins ); ?>
                    </div>
                </div>
                <?php if ( isset( $plugin['required'] ) && $plugin['required'] ) : ?>
                    <div class="plugin-required">
                        <?php esc_html_e( 'Required', 'nk-themes-helper' ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
