<?php
/**
 * Theme Dashboard template
 *
 * @package nk-themes-helper
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// minimum requirements.
$min_requirements = nk_theme()->theme_dashboard()->options['min_requirements'];
?>

<div class="nk-dashboard-widgets">
    <?php if ( nk_theme()->theme_dashboard()->theme_id ) : ?>
    <div class="nk-dashboard-widget">
        <div class="nk-dashboard-widget-title">
            <?php
            if ( nk_theme()->theme_dashboard()->activation()->active ) {
                ?>
                <span class="nk-dashboard-widget-title-badge yes"><i class="fa fa-thumbs-up"></i> <?php esc_html_e( 'Activated', 'nk-themes-helper' ); ?></span>
                <?php
            } else {
                ?>
                <span class="nk-dashboard-widget-title-badge error"><i class="fa fa-exclamation-triangle"></i> <?php esc_html_e( 'Not activated', 'nk-themes-helper' ); ?></span>
                <?php
            }
            ?>
            <mark><?php esc_html_e( 'Activation', 'nk-themes-helper' ); ?></mark>
        </div>
        <div class="nk-dashboard-widget-content">
            <p>
                <?php
                echo wp_kses(
                    // translators: %s - theme name.
                    sprintf( __( 'By activating %s you will unlock premium options - <strong>direct theme updates</strong> and <strong>demo import</strong>.', 'nk-themes-helper' ), nk_theme()->theme_dashboard()->theme_name ), array(
                        'strong' => array(),
                    )
                );
                ?>
            </p>

            <?php
            if ( nk_theme()->theme_dashboard()->activation()->active ) {
                // EDD Theme.
                if ( nk_theme()->theme_dashboard()->activation()->edd_license ) {
                    ?>
                    <input id="nk-theme-deactivate-reload" type="hidden" value="<?php echo esc_attr( admin_url( 'admin.php?page=nk-theme' ) ); ?>">
                        <p class="clear"></p>
                        <span id="nk-theme-deactivate-license" class="button button-secondary pull-left">
                        <?php
                        // translators: %s - theme name.
                        echo sprintf( esc_html__( 'Deactivate %s', 'nk-themes-helper' ), esc_html( nk_theme()->theme_dashboard()->theme_name ) );
                        ?>
                        </span>
                        <span class="spinner pull-left"></span>
                        <div class="clear"></div>
                        <?php

                        // Envato Theme.
                } else {
                    ?>
                    <a id="nk-theme-deactivate-license" class="button button-secondary pull-left" href="<?php echo esc_attr( 'https://nkdev.info/licenses/?vatomi_item_id=' . nk_theme()->theme_dashboard()->theme_id . '&vatomi_action=deactivate&vatomi_license=' . esc_attr( nk_theme()->theme_dashboard()->activation()->purchase_code ) . '&vatomi_redirect=' . urlencode( admin_url( 'admin.php?page=nk-theme' ) ) ); ?>">
                        <?php
                        // translators: %s - theme name.
                        echo sprintf( esc_html__( 'Deactivate %s', 'nk-themes-helper' ), esc_html( nk_theme()->theme_dashboard()->theme_name ) );
                        ?>
                        </a>
                        <div class="clear"></div>
                        <?php
                }
                ?>
                <?php
            } else {
                ?>
                <p>
                    <a href="<?php echo esc_attr( 'https://nkdev.info/licenses/?vatomi_item_id=' . nk_theme()->theme_dashboard()->theme_id . '&vatomi_action=activate&vatomi_site=' . urlencode( home_url( '/' ) ) . '&vatomi_redirect=' . urlencode( admin_url( 'admin.php?page=nk-theme' ) ) ); ?>" class="button button-primary">
                        <?php
                        // translators: %s - theme name.
                        echo sprintf( esc_html__( 'Activate %s with Envato', 'nk-themes-helper' ), esc_html( nk_theme()->theme_dashboard()->theme_name ) );
                        ?>
                    </a>

                    <?php if ( nk_theme()->theme_dashboard()->options['edd_name'] ) : ?>
                            <a href="#" id="nk-themefromsite-activation-toggle">
                                <?php
                                // translators: %s - theme name.
                                echo sprintf( esc_html__( 'or activate %s purchased on https://nkdev.info/', 'nk-themes-helper' ), esc_html( nk_theme()->theme_dashboard()->theme_name ) );
                                ?>
                            </a>
                            <input id="nk-themefromsite-activate-license" type="text" value="" placeholder="Enter License Key">

                            <span id="nk-themefromsite-activate" class="button button-primary pull-left"><?php esc_html_e( 'Activate', 'nk-themes-helper' ); ?></span>
                            <span class="spinner pull-left"></span>
                            <p class="clear"></p>
                            <input id="nk-themefromsite-activate-reload" type="hidden" value="<?php echo esc_attr( admin_url( 'admin.php?page=nk-theme' ) ); ?>">
                        <?php endif; ?>
                    </p>
                    <p>
                    <em>
                        <?php esc_html_e( 'Don\'t have valid license yet?', 'nk-themes-helper' ); ?>
                        <a href="<?php echo esc_url( nk_theme()->theme_dashboard()->theme_uri ); ?>" target="_blank">
                            <?php
                            // translators: %s - theme name.
                            echo sprintf( esc_html__( 'Purchase %s License', 'nk-themes-helper' ), esc_html( nk_theme()->theme_dashboard()->theme_name ) );
                            ?>
                        </a>
                    </em>
                    </p>
                    <?php
            }
            ?>
        </div>
    </div>
    <div class="nk-dashboard-widget">
        <div class="nk-dashboard-widget-title">
            <?php
            if ( nk_theme()->theme_dashboard()->updater()->is_update_available() ) {
                ?>
                <span class="nk-dashboard-widget-title-badge error"><i class="fa fa-exclamation-triangle"></i> <?php esc_html_e( 'Update Available', 'nk-themes-helper' ); ?></span>
                <?php
            } else {
                ?>
                <span class="nk-dashboard-widget-title-badge yes"><i class="fa fa-thumbs-up"></i> <?php esc_html_e( 'Theme is up to date', 'nk-themes-helper' ); ?></span>
                <?php
            }
            ?>
            <mark><?php esc_html_e( 'Update', 'nk-themes-helper' ); ?></mark>
        </div>
        <div class="nk-dashboard-widget-content">
            <p>
                <strong><?php esc_html_e( 'Installed Version:', 'nk-themes-helper' ); ?></strong>
                <br>
                <?php echo esc_html( nk_theme()->theme_dashboard()->theme_version ); ?>
            </p>
            <p>
                <strong><?php esc_html_e( 'Latest Version:', 'nk-themes-helper' ); ?></strong>
                <br>
                <?php echo esc_html( nk_theme()->theme_dashboard()->updater()->get_latest_theme_version() ); ?>
            </p>
            <?php

            // major update notice.
            $is_major_update = false;
            $version_arr = explode( '.', nk_theme()->theme_dashboard()->theme_version );
            $new_version_arr = explode( '.', nk_theme()->theme_dashboard()->updater()->get_latest_theme_version() );

            if ( is_array( $version_arr ) && count( $version_arr ) > 1 && is_array( $new_version_arr ) && count( $new_version_arr ) > 1 ) {
                $is_major_update = version_compare( $new_version_arr[0] . '.' . $new_version_arr[1], $version_arr[0] . '.' . $version_arr[1], '>' );
            }

            if ( $is_major_update ) {
                ?>
                <p class="nk-dashboard-alert"><?php echo esc_html__( 'This is a major theme update, please check theme changelog before updating!', 'nk-themes-helper' ); ?></p>
                <?php
            }

            if ( nk_theme()->theme_dashboard()->updater()->is_update_available() ) {
                if ( nk_theme()->theme_dashboard()->activation()->active ) {
                    $update_url = wp_nonce_url( admin_url( 'update.php?action=upgrade-theme&amp;theme=' . urlencode( nk_theme()->theme_dashboard()->theme_slug ) ), 'upgrade-theme_' . nk_theme()->theme_dashboard()->theme_slug );
                    ?>
                    <a href="<?php echo esc_attr( $update_url ); ?>" class="button button-primary">
                        <?php esc_html_e( 'Update Now', 'nk-themes-helper' ); ?>
                    </a>
                    <?php
                } else {
                    ?>
                    <span class="button button-primary disabled">
                        <?php esc_html_e( 'Update Now', 'nk-themes-helper' ); ?>
                    </span>
                    <?php
                }
            } else {
                ?>
                <span class="button disabled">
                    <?php esc_html_e( 'Update Now', 'nk-themes-helper' ); ?>
                </span>
                <?php
            }
            ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="nk-dashboard-widget">
        <?php

        // requirements check.
        $memory = nk_theme()->let_to_num( WP_MEMORY_LIMIT );
        $min_memory = nk_theme()->let_to_num( $min_requirements['memory_limit'] );
        $req_memory_limit = $memory >= $min_memory;

        $req_php_ver = true;
        if ( function_exists( 'phpversion' ) ) {
            $php_ver = phpversion();
            $req_php_ver = version_compare( $php_ver, $min_requirements['php_version'], '>=' );
        }

        $req_max_exec_time = true;
        if ( function_exists( 'ini_get' ) ) {
            $time_limit = ini_get( 'max_execution_time' );
            $req_max_exec_time = $time_limit >= $min_requirements['max_execution_time'];
        }

        $req_wp_remote_get = true;
        $wp_remote_get_response = wp_remote_get( 'https://nkdev.info/' );
        if ( is_wp_error( $wp_remote_get_response ) ) {
            $req_wp_remote_get = false;
        }

        $req_all_ok = $req_memory_limit && $req_php_ver && $req_max_exec_time && $req_wp_remote_get;

        ?>

        <div class="nk-dashboard-widget-title">
            <?php
            if ( $req_all_ok ) {
                ?>
                <span class="nk-dashboard-widget-title-badge yes"><i class="fa fa-thumbs-up"></i> <?php esc_html_e( 'No Problems', 'nk-themes-helper' ); ?></span>
                <?php
            } else {
                ?>
                <span class="nk-dashboard-widget-title-badge warning"><i class="fa fa-exclamation-triangle"></i> <?php esc_html_e( 'Can be improved', 'nk-themes-helper' ); ?></span>
                <?php
            }
            ?>
            <mark><?php esc_html_e( 'Recommendations', 'nk-themes-helper' ); ?></mark>
        </div>
        <div class="nk-dashboard-widget-content">
            <div class="nk-theme-requirements">
                <table class="widefat" cellspacing="0">
                    <tbody>
                        <tr>
                            <td><?php esc_html_e( 'WP Memory Limit:', 'nk-themes-helper' ); ?></td>
                            <td>
                            <?php
                            if ( $req_memory_limit ) {
                                echo '<mark class="yes"><i class="fa fa-check-circle"></i> ' . esc_html( size_format( $memory ) ) . '</mark>';
                            } else {
                                echo '<mark class="nk-drop"><i class="fa fa-times-circle"></i> ' . esc_html( size_format( $memory ) ) . ' ';
                                echo '<small>' . esc_html__( '[more info]', 'nk-themes-helper' ) . '</small>';
                                echo '<span class="nk-drop-cont" style="display: none;">';
                                echo sprintf(
                                    // translators: %s - memory.
                                    esc_html__( 'We recommend setting memory to at least %s.', 'nk-themes-helper' ),
                                    '<strong>' . esc_html( size_format( $min_memory ) ) . '</strong>'
                                );
                                echo ' <br> ';
                                echo sprintf(
                                    // translators: %s - url to help article.
                                    esc_html__( 'See more: %s', 'nk-themes-helper' ),
                                    sprintf( '<a href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">%s</a>', esc_html__( 'Increasing memory allocated to PHP.', 'nk-themes-helper' ) )
                                );
                                echo '</span>';
                                echo '</mark>';
                            }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'PHP Version:', 'nk-themes-helper' ); ?></td>
                            <td>
                            <?php
                            if ( function_exists( 'phpversion' ) ) {
                                if ( $req_php_ver ) {
                                    echo '<mark class="yes"><i class="fa fa-check-circle"></i> ' . esc_html( $php_ver ) . '</mark>';
                                } else {
                                    echo '<mark class="nk-drop">';
                                    echo '<i class="fa fa-times-circle"></i> ' . esc_html( $php_ver );
                                    echo ' <small>' . esc_html__( '[more info]', 'nk-themes-helper' ) . '</small>';
                                    echo '<span class="nk-drop-cont" style="display: none;">';
                                    // translators: %s - php version.
                                    echo sprintf( esc_html__( 'We recommend upgrade php version to at least %s.', 'nk-themes-helper' ), esc_html( $min_requirements['php_version'] ) );
                                    echo '</span>';
                                    echo '</mark>';
                                }
                            }
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'PHP Time Limit:', 'nk-themes-helper' ); ?></td>
                            <td>
                            <?php
                            if ( function_exists( 'ini_get' ) ) :
                                if ( $req_max_exec_time ) {
                                    echo '<mark class="yes"><i class="fa fa-check-circle"></i> ' . esc_html( $time_limit ) . '</mark>';
                                } else {
                                    echo '<mark class="nk-drop">';
                                    echo '<i class="fa fa-times-circle"></i> ' . esc_html( $time_limit );
                                    echo ' <small>' . esc_html__( '[more info]', 'nk-themes-helper' ) . '</small>';
                                    echo '<span class="nk-drop-cont" style="display: none;">';
                                    // translators: %s - execution time.
                                    echo sprintf( esc_html__( 'We recommend setting max execution time to at least %s.', 'nk-themes-helper' ), esc_html( $min_requirements['max_execution_time'] ) );
                                    echo ' <br> ';
                                    echo sprintf(
                                        // translators: %s - url to help article.
                                        esc_html__( 'See more: %s', 'nk-themes-helper' ),
                                        sprintf( '<a href="http://codex.wordpress.org/Common_WordPress_Errors#Maximum_execution_time_exceeded" target="_blank">%s</a>', esc_html__( 'Increasing max execution to PHP', 'nk-themes-helper' ) )
                                    );
                                    echo '</span>';
                                    echo '</mark>';
                                }
                            endif;
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'WP Remote Get:', 'nk-themes-helper' ); ?></td>
                            <td>
                            <?php
                            if ( $req_wp_remote_get ) {
                                echo '<mark class="yes"><i class="fa fa-check-circle"></i> </mark>';
                            } else {
                                echo '<mark class="nk-drop">';
                                echo '<i class="fa fa-times-circle"></i> ' . esc_html__( 'Failed', 'nk-themes-helper' );
                                echo ' <small>' . esc_html__( '[more info]', 'nk-themes-helper' ) . '</small>';
                                echo '<span class="nk-drop-cont" style="display: none;">';
                                echo esc_html__( 'wp_remote_get() failed. Some theme features may not work. Please contact your hosting provider and make sure that https://nkdev.info/ is not blocked.', 'nk-themes-helper' );
                                echo '</span>';
                                echo '</mark>';
                            }
                                ?>
                                </td>
                        </tr>
                        <tr>
                            <td><?php esc_html_e( 'Child Theme:', 'nk-themes-helper' ); ?></td>
                            <td>
                            <?php
                            if ( nk_theme()->theme_dashboard()->theme_is_child ) {
                                echo '<mark class="yes"><i class="fa fa-check-circle"></i></mark>';
                            } else {
                                ?>
                                <mark class="nk-drop">
                                    <i class="fa fa-times-circle"></i>
                                    <small><?php esc_html_e( '[more info]', 'nk-themes-helper' ); ?></small>
                                    <span class="nk-drop-cont" style="display: none;">
                                        <?php esc_html_e( 'We recommend use child theme to prevent loosing your customizations after theme update.', 'nk-themes-helper' ); ?>
                                    </span>
                                    </mark>
                                    <?php
                            }
                            ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
