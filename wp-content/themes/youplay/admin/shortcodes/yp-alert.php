<?php
/**
 * YP Alert
 *
 * Example:
 * [yp_alert color="primary" dismissible="false" boxed="false"]<strong>Well done!</strong> You successfully read this important alert message.[/yp_alert]
 */
call_user_func('add_' . 'shortcode', 'yp_alert', 'yp_alert');
if ( ! function_exists( 'yp_alert' ) ) :
function yp_alert($atts, $content = "<strong>Well done!</strong> You successfully read this important alert message.") {
    extract(shortcode_atts(array(
        "color"       => "primary",
        "dismissible" => false,
        "boxed"       => false,
        "class"       => ""
    ), $atts));

    if(yp_check($boxed)) {
        $class .= " container";
    }

    $dismissible_btn = yp_check($dismissible)?'<button type="button" class="close" data-dismiss="alert" aria-label="' . esc_html__("Close", 'youplay') . '"><span aria-hidden="true">&times;</span></button>':'';

    $class .= ' alert-' . $color;

    if(yp_check($dismissible)) {
      $class .= ' alert-dismissible';
    }

    // additional classname for custom styles VC
    $class .= yp_get_css_tab_class($atts);

    return '<div class="alert ' . yp_sanitize_class($class) . '" role="alert">' . $dismissible_btn . do_shortcode(yp_fix_content($content)) . '</div>';
}
endif;



/* Add VC Shortcode */
add_action( "after_setup_theme", "vc_youplay_alert" );
if ( ! function_exists( 'vc_youplay_alert' ) ) :
function vc_youplay_alert() {
    if(function_exists("vc_map")) {
        /* Register shortcode with Visual Composer */
        vc_map( array(
           "name"     => esc_html__("nK Alert", 'youplay'),
           "base"     => "yp_alert",
           "controls" => "full",
           "category" => "nK",
           "icon"     => "icon-nk icon-nk-alert",
           "params"   => array_merge( array(
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
                 "description" => "",
                 "admin_label" => true,
              ),
              array(
                  "type"       => "checkbox",
                  "heading"    => esc_html__( "Dismissible", 'youplay' ),
                  "param_name" => "dismissible",
                  "value"      => array( "" => true )
              ),
              array(
                 "type"        => "textarea_html",
                 "heading"     => esc_html__("Inner Text", 'youplay'),
                 "param_name"  => "content",
                 "value"       => esc_html__("Well done! You successfully read this important alert message.", 'youplay'),
                 "description" => "",
              ),
              array(
                 "type"        => "checkbox",
                 "heading"     => esc_html__("Boxed", 'youplay'),
                 "param_name"  => "boxed",
                 "value"       => array( "" => true ),
                 "description" => "Use it when your page content boxed disabled",
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
