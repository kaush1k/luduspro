<?php
/**
 * YP Label
 *
 * Example:
 * [yp_label color="default" text="Label"]
 */
call_user_func('add_' . 'shortcode', 'yp_label', 'yp_label');
if ( ! function_exists( 'yp_label' ) ) :
function yp_label($atts, $content = null) {
    extract(shortcode_atts(array(
        "color"    => "default",
        "text"     => "Label",
        "class"    => ""
    ), $atts));

    // additional classname for custom styles VC
    $class .= yp_get_css_tab_class($atts);

    return '<span class="label ' . yp_sanitize_class($class . ' label-' . $color) . '">' . esc_html($text) . '</span>';
}
endif;



/* Add VC Shortcode */
add_action( "after_setup_theme", "vc_youplay_label" );
if ( ! function_exists( 'vc_youplay_label' ) ) :
function vc_youplay_label() {
    if(function_exists("vc_map")) {
        /* Register shortcode with Visual Composer */
        vc_map( array(
           "name"     => esc_html__("nK Label", 'youplay'),
           "base"     => "yp_label",
           "controls" => "full",
           "category" => "nK",
           "icon"     => "icon-nk icon-nk-label",
           "params"   => array_merge( array(
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Inner Text", 'youplay'),
                 "param_name"  => "text",
                 "value"       => esc_html__("Label", 'youplay'),
                 "admin_label" => true,
                 "description" => "",
              ),
              array(
                 "type"       => "dropdown",
                 "heading"    => esc_html__("Color", 'youplay'),
                 "param_name" => "color",
                 "value"      => array(
                    esc_html__("Default", 'youplay') => "",
                    esc_html__("Primary", 'youplay') => "primary",
                    esc_html__("Success", 'youplay') => "success",
                    esc_html__("Info", 'youplay')    => "info",
                    esc_html__("Warning", 'youplay') => "warning",
                    esc_html__("Danger", 'youplay')  => "danger",
                 ),
                 "description" => ""
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
