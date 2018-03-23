<?php
/**
 * YP Progress Bar
 *
 * Example:
 * [yp_progress stripped="true" percent="40" color="success" boxed="false"]40% Complete (success)[/yp_progress]
 */
call_user_func('add_' . 'shortcode', 'yp_progress', 'yp_progress');
if ( ! function_exists( 'yp_progress' ) ) :
function yp_progress($atts, $content = null) {
    extract(shortcode_atts(array(
        "striped" => true,
        "percent" => 40,
        "color"   => "",
        "boxed"   => false,
        "class"   => ""
    ), $atts));

    if(yp_check($boxed)) {
        $class .= " container";
    }

    $striped = yp_check($striped) ? "progress-bar-striped" : "";
    $color = yp_check($color) ? "progress-bar-" . $color : "";

    // additional classname for custom styles VC
    $class .= yp_get_css_tab_class($atts);

    return "<div class='progress youplay-progress " . yp_sanitize_class($class) . "'>
                <div class='progress-bar " . yp_sanitize_class($color . ' ' . $striped) . "' role='progressbar' aria-valuenow='" . esc_attr($percent) . "' aria-valuemin='0' aria-valuemax='100' style='width: " . esc_attr($percent) . "%'>
                    <span class='sr-only'>" . yp_fix_content($content) . "</span>
                </div>
            </div>";
}
endif;



/* Add VC Shortcode */
add_action( "after_setup_theme", "vc_youplay_progress" );
if ( ! function_exists( 'vc_youplay_progress' ) ) :
function vc_youplay_progress() {
    if(function_exists("vc_map")) {
        /* Register shortcode with Visual Composer */
        vc_map( array(
           "name"     => esc_html__("nK Progress Bar", 'youplay'),
           "base"     => "yp_progress",
           "controls" => "full",
           "category" => "nK",
           "icon"     => "icon-nk icon-nk-progress-bar",
           "params"   => array_merge( array(
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Screen Reader Text", 'youplay'),
                 "param_name"  => "content",
                 "value"       => esc_html__("40% Complete (success)", 'youplay'),
                 "description" => "",
              ),
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Percent", 'youplay'),
                 "param_name"  => "percent",
                 "value"       => esc_html__("40", 'youplay'),
                 "description" => '',
                 "admin_label" => true,
              ),
              array(
                 "type"       => "dropdown",
                 "heading"    => esc_html__("Color", 'youplay'),
                 "param_name" => "color",
                 "value"      => array(
                    esc_html__("Default", 'youplay') => "",
                    esc_html__("Primary", 'youplay') => "primary",
                    esc_html__("Success", 'youplay') => "success",
                    esc_html__("Info", 'youplay') => "info",
                    esc_html__("Warning", 'youplay') => "warning",
                    esc_html__("Danger", 'youplay') => "danger",
                 ),
                 "description" => "",
                 "admin_label" => true,
              ),
              array(
                  "type"       => "checkbox",
                  "heading"    => esc_html__( "Striped", 'youplay' ),
                  "param_name" => "striped",
                  "value"      => array( "" => true )
              ),
              array(
                 "type"        => "checkbox",
                 "heading"     => esc_html__("Boxed", 'youplay'),
                 "param_name"  => "boxed",
                 "value"       => array( "" => true ),
                 "description" => esc_html__("Use it when your page content boxed disabled", 'youplay'),
              ),
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Custom Classes", 'youplay'),
                 "param_name"  => "class",
                 "value"       => "",
                 "description" => "",
              ),
           ), yp_get_css_tab() )
        ) );
    }
}
endif;
