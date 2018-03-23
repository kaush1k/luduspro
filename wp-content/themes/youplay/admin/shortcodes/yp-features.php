<?php
/**
 * YP Features
 *
 * Example:
 * [yp_features title="Youplay" description="Description" description_size="small" icon="fa fa-css3" href="" target="_self" boxed="false"]
 */
call_user_func('add_' . 'shortcode', 'yp_features', 'yp_features');
if ( ! function_exists( 'yp_features' ) ) :
function yp_features($atts, $content = null) {
    extract(shortcode_atts(array(
        "icon"        => "fa fa-css3",
        "title"       => "Youplay",
        "description" => "Description",
        "description_size" => "small",
        "boxed"       => false,
        "href"        => "",
        "target"      => "_self",
        "class"       => ""
    ), $atts));

    if(yp_check($boxed)) {
        $class .= " container";
    }

    $start_tag = 'div';
    $end_tag = 'div';

    if($href) {
      $start_tag = 'a href="' . esc_attr($href) . '" target="' . esc_attr($target) . '"';
      $end_tag = 'a';
    }

    $descr_tag = 'div';
    if($description_size == 'small') {
        $descr_tag = 'small';
    }

    // additional classname for custom styles VC
    $class .= yp_get_css_tab_class($atts);

    return '<div class="youplay-features ' . yp_sanitize_class($class) . '"><' . $start_tag . ' class="feature angled-bg">
              <i class="' . yp_sanitize_class($icon) . '"></i>
              <h3>' . esc_html($title) . '</h3>
              <' . $descr_tag . '>' . esc_html($description) . '</' . $descr_tag . '>
            </' . $end_tag . '></div>';
}
endif;



/* Add VC Shortcode */
add_action( "after_setup_theme", "vc_youplay_features" );
if ( ! function_exists( 'vc_youplay_features' ) ) :
function vc_youplay_features() {
    if(function_exists("vc_map")) {
        /* Register shortcode with Visual Composer */
        vc_map( array(
           "name"     => esc_html__("nK Features", 'youplay'),
           "base"     => "yp_features",
           "controls" => "full",
           "category" => "nK",
           "icon"     => "icon-nk icon-nk-features",
           "params"   => array_merge( array(
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Title", 'youplay'),
                 "param_name"  => "title",
                 "value"       => esc_html__("Youplay", 'youplay'),
                 "description" => "",
                 "admin_label" => true,
              ),
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Description", 'youplay'),
                 "param_name"  => "description",
                 "value"       => esc_html__("Description", 'youplay'),
                 "description" => '',
                 "admin_label" => true,
              ),
              array(
                 "type"       => "dropdown",
                 "heading"    => esc_html__("Description Size", 'youplay'),
                 "param_name" => "description_size",
                 "std"        => "small",
                 "value"      => array(
                    esc_html__("Small", 'youplay') => "small",
                    esc_html__("Default", 'youplay') => "default"
                 ),
                 "description" => ""
              ),
              array(
                 "type"        => "iconpicker",
                 "heading"     => esc_html__("Icon", 'youplay'),
                 "param_name"  => "icon",
                 "value"       => esc_html__("fa fa-css3", 'youplay'),
                 "admin_label" => true,
              ),
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Link", 'youplay'),
                 "param_name"  => "href",
                 "value"       => "",
                 "description" => '',
              ),
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Target", 'youplay'),
                 "param_name"  => "target",
                 "value"       => "",
                 "description" => '',
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
