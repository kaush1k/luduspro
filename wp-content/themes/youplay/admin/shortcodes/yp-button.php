<?php
/**
 * YP Buttons
 *
 * Example:
 * [yp_button href="https://nkdev.info" size="lg" full_width="false" active="false" color="success" align="auto" icon_before="fa fa-html5" icon_after=""]Youplay[/yp_button]
 *
 * Group Example:
 * [yp_button_group]
 *   [yp_button href="https://nkdev.info" target="_self" size="lg" full_width="false" active="false" color="success" icon_before="fa fa-html5" icon_after=""]Youplay 1[/yp_button]
 *   [yp_button href="https://nkdev.info" target="_self" size="lg" full_width="false" active="false" color="success" icon_before="fa fa-css3" icon_after=""]Youplay 2[/yp_button]
 * [/yp_button_group]
 */
call_user_func('add_' . 'shortcode', 'yp_button', 'yp_button');
if ( ! function_exists( 'yp_button' ) ) :
function yp_button($atts, $content = null) {
    extract(shortcode_atts(array(
        "href"        => "",
        "target"      => "_self",
        "size"        => "",
        "full_width"  => false,
        "active"      => false,
        "color"       => "",
        "align"       => "",
        "icon_before" => "",
        "icon_after"  => "",
        "class"       => ""
    ), $atts));

    if(yp_check($size)) {
      $class .= ' btn-' . $size;
    }

    if(yp_check($full_width)) {
      $class .= ' btn-full';
    }

    if(yp_check($active)) {
      $class .= ' active';
    }

    if(yp_check($color)) {
      $class .= ' btn-' . $color;
    }

    $icon_before = yp_check($icon_before) ? "<span class='" . yp_sanitize_class($icon_before) . "'></span>" : "";
    $icon_after = yp_check($icon_after) ? "<span class='" . yp_sanitize_class($icon_after) . "'></span>" : "";

    // set align
    if($align === 'left' || $align === 'right') {
        $class .= ' pull-' . $align;
    }
    $before = '';
    $after = '';
    if($align === 'center') {
        $before = '<div class="text-' . $align . '">';
        $after = '</div>';
    }

    // additional classname for custom styles VC
    $class .= yp_get_css_tab_class($atts);

    return $before . "<a class='btn " . yp_sanitize_class($class) . "' href='" . esc_url($href) . "' target='" . esc_attr($target) . "'>" . $icon_before . " " . esc_html($content) . " " . $icon_after . "</a>" . $after;
}
endif;

// buttons group
call_user_func('add_' . 'shortcode', 'yp_button_group', 'yp_button_group');
if ( ! function_exists( 'yp_button_group' ) ) :
function yp_button_group($atts, $content = null) {
    return "<div class='btn-group'>
              " . do_shortcode(yp_fix_content($content)) . "
            </div>";
}
endif;



/* Add VC Shortcode */
add_action( "after_setup_theme", "vc_youplay_button" );
if ( ! function_exists( 'vc_youplay_button' ) ) :
function vc_youplay_button() {
    if(function_exists("vc_map")) {
        /* Register shortcode with Visual Composer */
        vc_map( array(
           "name"     => esc_html__("nK Button", 'youplay'),
           "base"     => "yp_button",
           "controls" => "full",
           "category" => "nK",
           "icon"     => "icon-nk icon-nk-button",
           "params"   => array_merge( array(
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Inner Text", 'youplay'),
                 "param_name"  => "content",
                 "value"       => esc_html__("Youplay", 'youplay'),
                 "description" => "",
              ),
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Link", 'youplay'),
                 "param_name"  => "href",
                 "value"       => "",
                 "description" => '',
                 "admin_label" => true,
              ),
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Target", 'youplay'),
                 "param_name"  => "target",
                 "value"       => "",
                 "description" => '',
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
                    esc_html__("White", 'youplay')   => "white",
                    esc_html__("Black", 'youplay')   => "black",
                 ),
                 "description" => ""
              ),
              array(
                 "type"       => "dropdown",
                 "heading"    => esc_html__("Size", 'youplay'),
                 "param_name" => "size",
                 "value"      => array(
                    esc_html__("Default", 'youplay')     => "",
                    esc_html__("Large", 'youplay')       => "lg",
                    esc_html__("Middle", 'youplay')      => "md",
                    esc_html__("Small", 'youplay')       => "sm",
                    esc_html__("Extra Small", 'youplay') => "xs",
                 ),
                 "description" => ""
              ),
              array(
                  "type"       => "checkbox",
                  "heading"    => esc_html__( "Full Width", 'youplay' ),
                  "param_name" => "full_width",
                  "value"      => array( "" => true )
              ),
              array(
                  "type"       => "checkbox",
                  "heading"    => esc_html__( "Active", 'youplay' ),
                  "param_name" => "active",
                  "value"      => array( "" => true )
              ),
              array(
                 "type"       => "dropdown",
                 "heading"    => esc_html__("Align", 'youplay'),
                 "param_name" => "align",
                 "value"      => array(
                    esc_html__("Auto", 'youplay')   => "auto",
                    esc_html__("Left", 'youplay')   => "left",
                    esc_html__("Center", 'youplay') => "center",
                    esc_html__("Right", 'youplay')  => "right"
                 ),
                 "description" => ""
              ),
              array(
                 "type"        => "iconpicker",
                 "heading"     => esc_html__("Icon Before", 'youplay'),
                 "param_name"  => "icon_before",
                 "value"       => esc_html__("fa fa-html5", 'youplay'),
                 "description" => "Insert icon before inner text.",
              ),
              array(
                 "type"        => "iconpicker",
                 "heading"     => esc_html__("Icon After", 'youplay'),
                 "param_name"  => "icon_after",
                 "value"       => "",
                 "description" => "Insert icon after inner text.",
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
