<?php
/**
 * YP Banner
 *
 * Example:
 * [yp_banner img_src="14" img_size="1920x1080" banner_size="mid" parallax="true" parallax_speed="0.4" top_position="false" boxed="false"]Content[/yp_banner]
 */
call_user_func('add_' . 'shortcode', 'yp_banner', 'yp_banner');
if ( ! function_exists( 'yp_banner' ) ) :
function yp_banner($atts, $content = null) {
    static $YP_BANNER_ID = 0;
    $YP_BANNER_ID++;

    extract(shortcode_atts(array(
        "img_src"      => "",
        "img_size"     => "1920x1080",
        "banner_size"  => "",
        "parallax"     => true,
        "parallax_speed" => 0.4,
        "top_position" => false,
        "boxed"        => false,
        "class"        => ""
    ), $atts));

    if($parallax) {
        $class .= ' youplay-banner-parallax';
    }

    $class .= ' youplay-banner youplay-banner-id-' . intval($YP_BANNER_ID);

    $class .= ' ' . $banner_size;

    if(yp_check($top_position)) {
      $class .= ' banner-top';
    }

    if(yp_check($boxed)) {
      $class .= ' container';
    }

    // move [yp_banner_content_bottom] shortcode from $content to $bottom_content variable
    $pattern = get_shortcode_regex();
    $bottom_content = '';
    preg_match('/'.$pattern.'/s', $content, $matches);
    if ( isset($matches[2]) && is_array($matches) && $matches[2] == 'yp_banner_content_bottom') {
      // shortcode is being used
      $content = str_replace( $matches['0'], '', $content );
      $bottom_content .= $matches['0'];
    }

    return "<div class='" . yp_sanitize_class($class) . "'>
              <div class='image' data-speed='" . esc_attr($parallax_speed) . "'>
                  " .
                   youplay_get_image( $img_src, '1920x1080', false, array(
                       'class' => 'jarallax-img'
                   ) )
                . "
              </div>
              " . do_shortcode($bottom_content) . "
              <div class='info'>
                <div>
                  <div class='container'>
                    " . do_shortcode(yp_fix_content($content)) . "
                  </div>
                </div>
              </div>
            </div>";
}
endif;

// shortcode to add bottom navigation for banners
call_user_func('add_' . 'shortcode', 'yp_banner_content_bottom', 'yp_banner_content_bottom');
if ( ! function_exists( 'yp_banner_content_bottom' ) ) :
function yp_banner_content_bottom($atts, $content = null) {
  return '
      <div class="youplay-user-navigation">
          <div class="container">
              ' . do_shortcode($content) . '
          </div>
      </div>';
}
endif;


/* Add VC Shortcode */
add_action( "after_setup_theme", "vc_yp_banner" );
if ( ! function_exists( 'vc_yp_banner' ) ) :
function vc_yp_banner() {
    if(function_exists("vc_map")) {
        /* Register shortcode with Visual Composer */
        vc_map( array(
           "deprecated"        => "3.5.0",
           "name"              => esc_html__("nK Banner", 'youplay'),
           "base"              => "yp_banner",
           "controls"          => "full",
           "category"          => "nK",
           "icon"              => "icon-nk icon-nk-banner",
           "is_container"      => true,
           "js_view"           => 'VcColumnView',
           "params"            => array(
              array(
                 "type"       => "attach_image",
                 "heading"    => esc_html__("Image", 'youplay'),
                 "param_name" => "img_src",
                 "value"      => ""
              ),
              array(
                 "type"       => "dropdown",
                 "heading"    => esc_html__("Image Size", 'youplay'),
                 "param_name" => "img_size",
                 "value"      => yp_get_intermediate_image_sizes(),
                 "std"        => "1920x1080"
              ),
              array(
                 "type"       => "dropdown",
                 "heading"    => esc_html__("Banner Size", 'youplay'),
                 "param_name" => "banner_size",
                 "value"      => array(
                    esc_html__("Full", 'youplay')        => "full",
                    esc_html__("Big", 'youplay')         => "big",
                    esc_html__("Mid", 'youplay')         => "mid",
                    esc_html__("Small", 'youplay')       => "small",
                    esc_html__("Extra Small", 'youplay') => "xsmall",
                 ),
                 "std"        => esc_html__("Mid", 'youplay')
              ),
              array(
                  "type"        => "checkbox",
                  "heading"     => esc_html__( "Parallax", 'youplay' ),
                  "param_name"  => "parallax",
                  "value"       => array( "" => true ),
                  "std"         => true
              ),
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Parallax Speed", 'youplay'),
                 "param_name"  => "parallax_speed",
                 "value"       => 0.4,
                 "description" => esc_html__('Parallax speed from -1.0 to 2.0', 'youplay'),
                 "dependency"  => array(
                   "element"     => "parallax",
                   "value"       => "1"
                 ),
              ),
              array(
                  "type"        => "checkbox",
                  "heading"     => esc_html__( "Top Position", 'youplay' ),
                  "param_name"  => "top_position",
                  "value"       => array( "" => true ),
                  "description" => esc_html__( "Check it if banner on the top of page.", 'youplay' )
              ),
              array(
                 "type"        => "checkbox",
                 "heading"     => esc_html__("Boxed", 'youplay'),
                 "param_name"  => "boxed",
                 "value"       => array( "" => true ),
                 "description" => esc_html("Use it when your page content boxed disabled", 'youplay'),
              ),
              array(
                 "type"        => "textfield",
                 "heading"     => esc_html__("Custom Classes", 'youplay'),
                 "param_name"  => "class",
                 "value"       => "",
                 "description" => "",
              ),
           )
        ) );
    }
}
endif;

//Your "container" content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_yp_banner extends WPBakeryShortCodesContainer {
    }
}
