<?php
// WooCommerce is active
if ( !class_exists( 'WooCommerce' ) ) {
	return;
}


/* Custom Breadcrumbs */
add_filter( 'woocommerce_breadcrumb_defaults', 'yp_woocommerce_breadcrumbs' );
if ( ! function_exists( 'yp_woocommerce_breadcrumbs' ) ) :
function yp_woocommerce_breadcrumbs($defaults) {
	$defaults['delimiter'] = ' <span class="fa fa-angle-right"></span> ';
	$defaults['wrap_before'] = '<nav class="mb-20">';
	$defaults['wrap_after'] = '</nav>';
	return $defaults;
}
endif;


/* Related Products Count */
add_filter( 'woocommerce_output_related_products_args', 'yp_related_products_args' );
if ( ! function_exists( 'yp_related_products_args' ) ) :
function yp_related_products_args( $args ) {
	$args['posts_per_page'] = 5;
	return $args;
}
endif;


// share action
add_action( 'woocommerce_share', 'youplay_product_sharing', 10, 0 );

// add share buttons tab
// add custom youplay tab
add_filter( 'woocommerce_product_tabs', 'yp_add_woo_tabs', 98 );
if ( ! function_exists( 'yp_add_woo_tabs' ) ) :
function yp_add_woo_tabs( $tabs ) {
	$tabs['sharing'] = array(
		'priority' => 25,
		'callback' => 'woocommerce_template_single_sharing'
	);

	$tabs['additional_params'] = array(
		'priority' => 26,
		'callback' => 'youplay_woo_additional_tab'
	);
	return $tabs;
}
endif;

if ( ! function_exists( 'youplay_woo_additional_tab' ) ) :
function youplay_woo_additional_tab() {
	$use = yp_opts('single_product_additional_params', true);
	$title = yp_opts('single_product_additional_params_title', true);
	$cont = yp_opts('single_product_additional_params_cont', true);

	if($use) {
		if($title) {
			echo '<h2>' . $title . '</h2>';
		}
		if($cont) {
			echo do_shortcode($cont);
		}
	}
}
endif;



// proceed to checkout button
if ( ! function_exists( 'woocommerce_button_proceed_to_checkout' ) ) :
function woocommerce_button_proceed_to_checkout() {
	$checkout_url = wc_get_checkout_url();
	?>
	<a href="<?php echo esc_url($checkout_url); ?>" class="btn btn-default btn-lg"><?php _e( 'Proceed to Checkout', 'youplay' ); ?></a>
	<?php
}
endif;

if ( ! function_exists( 'yp_get_text_between_tags' ) ) :
function yp_get_text_between_tags($string, $tagname) {
	$pattern = "/<$tagname>(.*)<\/$tagname>/";
	preg_match($pattern, $string, $matches);
	return $matches[1];
}
endif;

// Product Price fix discount
add_filter( 'woocommerce_get_price_html', 'yp_woo_price_html', 100, 2 );
if ( ! function_exists( 'yp_woo_price_html' ) ) :
function yp_woo_price_html( $price ) {
	// check if no <ins> tag and return default value
	if (strpos($price, '<ins>') == false) {
		return $price;
	}

	$old = yp_get_text_between_tags($price, "del");
	$new = yp_get_text_between_tags($price, "ins");
	if($new) {
    	return $new . ($old ? (' <sup><del>' . $old . '</del></sup>') : '');
	} else {
		return $price;
	}
}
endif;

// product discount badge
if ( ! function_exists( 'yp_woo_discount_badge' ) ) :
function yp_woo_discount_badge( $product, $show = true ) {
	$regular = $product->get_regular_price();
	$current = $product->get_sale_price();

	if(is_numeric($regular) && is_numeric($current)) {
		$discount = ceil(100 - 100 * $current / $regular);

		if($discount == 0) {
			return '';
		}

		$bg = ' bg-default';

		if($discount >= 80) {
			$bg = ' bg-success';
		}

		return '<div class="' . yp_sanitize_class('badge' . ($show?' show':'') . $bg) . '">-' . $discount . '%</div>';
	} else {
		return '';
	}
}
endif;



// form fields
if ( ! function_exists( 'youplay_form_field' ) ) {

	/**
	 * Outputs a checkout/address form field.
	 * based on woocommerce_form_field
	 *
	 * @subpackage	Forms
	 * @param string $key
	 * @param mixed $args
	 * @param string $value (default: null)
	 * @todo This function needs to be broken up in smaller pieces
	 */
	function youplay_form_field( $key, $args, $value = null ) {
        $defaults = array(
            'type'              => 'text',
            'label'             => '',
            'description'       => '',
            'placeholder'       => '',
            'maxlength'         => false,
            'required'          => false,
            'id'                => $key,
            'class'             => array(),
            'label_class'       => array(),
            'input_class'       => array(),
            'return'            => false,
            'options'           => array(),
            'custom_attributes' => array(),
            'validate'          => array(),
            'default'           => '',
        );

        $args = wp_parse_args( $args, $defaults );
        $args = apply_filters( 'woocommerce_form_field_args', $args, $key, $value );

        if ( $args['required'] ) {
            $args['class'][] = 'validate-required';
            $required = ' <abbr class="required" title="' . esc_attr__( 'required', 'youplay'  ) . '">*</abbr>';
        } else {
            $required = '';
        }

        $args['maxlength'] = ( $args['maxlength'] ) ? 'maxlength="' . absint( $args['maxlength'] ) . '"' : '';

        if ( is_string( $args['label_class'] ) ) {
            $args['label_class'] = array( $args['label_class'] );
        }

        if ( is_null( $value ) ) {
            $value = $args['default'];
        }

        // Custom attribute handling
        $custom_attributes = array();

        if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
            foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
                $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
            }
        }

        if ( ! empty( $args['validate'] ) ) {
            foreach( $args['validate'] as $validate ) {
                $args['class'][] = 'validate-' . $validate;
            }
        }

        $field = '';
        $label_id = $args['id'];
        $field_container = '<div class="%1$s" id="%2$s">%3$s</div>';

        switch ( $args['type'] ) {
            case 'country' :

                $countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

                if ( 1 === sizeof( $countries ) ) {

                    $field .= '<strong>' . current( array_values( $countries ) ) . '</strong>';

                    $field .= '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="' . current( array_keys($countries ) ) . '" ' . implode( ' ', $custom_attributes ) . ' class="country_to_state" />';

                } else {

                    $field = '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="country_to_state country_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" ' . implode( ' ', $custom_attributes ) . '>'
                            . '<option value="">'.__( 'Select a country&hellip;', 'youplay' ) .'</option>';

                    foreach ( $countries as $ckey => $cvalue ) {
                        $field .= '<option value="' . esc_attr( $ckey ) . '" '. selected( $value, $ckey, false ) . '>' . $cvalue . '</option>';
                    }

                    $field .= '</select>';

                    $field .= '<noscript><input type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__( 'Update country', 'youplay' ) . '" /></noscript>';

                }

                break;
            case 'state' :

                /* Get Country */
                $country_key = 'billing_state' === $key ? 'billing_country' : 'shipping_country';
                $current_cc  = WC()->checkout->get_value( $country_key );
                $states      = WC()->countries->get_states( $current_cc );

                if ( is_array( $states ) && empty( $states ) ) {

                    $field_container = '<p class="form-row %1$s" id="%2$s" style="display: none">%3$s</p>';

                    $field .= '<input type="hidden" class="hidden" name="' . esc_attr( $key )  . '" id="' . esc_attr( $args['id'] ) . '" value="" ' . implode( ' ', $custom_attributes ) . ' placeholder="' . esc_attr( $args['placeholder'] ) . '" />';

                } elseif ( is_array( $states ) ) {

                    $field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="state_select ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
                        <option value="">'.__( 'Select a state&hellip;', 'youplay' ) .'</option>';

                    foreach ( $states as $ckey => $cvalue ) {
                        $field .= '<option value="' . esc_attr( $ckey ) . '" '.selected( $value, $ckey, false ) .'>' . $cvalue . '</option>';
                    }

                    $field .= '</select>';

                } else {

                    $field .= '<div class="youplay-input"><input type="text" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" value="' . esc_attr( $value ) . '"  placeholder="' . esc_attr( $args['placeholder'] ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" ' . implode( ' ', $custom_attributes ) . ' /></div>';

                }

                break;
            case 'textarea' :

                $field .= '<div class="youplay-input"><textarea name="' . esc_attr( $key ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . $args['maxlength'] . ' ' . ( empty( $args['custom_attributes']['rows'] ) ? ' rows="2"' : '' ) . ( empty( $args['custom_attributes']['cols'] ) ? ' cols="5"' : '' ) . implode( ' ', $custom_attributes ) . '>'. esc_textarea( $value  ) .'</textarea></div>';

                break;
            case 'checkbox' :

                $field = '<label class="checkbox ' . implode( ' ', $args['label_class'] ) .'" ' . implode( ' ', $custom_attributes ) . '>
                        <input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="1" '.checked( $value, 1, false ) .' /> '
                         . $args['label'] . $required . '</label>';

                break;
            case 'password' :
            case 'text' :
            case 'email' :
            case 'tel' :
            case 'number' :

                $field .= '<div class="youplay-input"><input type="' . esc_attr( $args['type'] ) . '" class="input-text ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" ' . $args['maxlength'] . ' value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' /></div>';

                break;
            case 'select' :

                $options = $field = '';

                if ( ! empty( $args['options'] ) ) {
                    foreach ( $args['options'] as $option_key => $option_text ) {
                        if ( '' === $option_key ) {
                            // If we have a blank option, select2 needs a placeholder
                            if ( empty( $args['placeholder'] ) ) {
                                $args['placeholder'] = $option_text ? $option_text : __( 'Choose an option', 'youplay' );
                            }
                            $custom_attributes[] = 'data-allow_clear="true"';
                        }
                        $options .= '<option value="' . esc_attr( $option_key ) . '" '. selected( $value, $option_key, false ) . '>' . esc_attr( $option_text ) .'</option>';
                    }

                    $field .= '<select name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" class="select '. esc_attr( implode( ' ', $args['input_class'] ) ) . '" ' . implode( ' ', $custom_attributes ) . ' data-placeholder="' . esc_attr( $args['placeholder'] ) . '">
                            ' . $options . '
                        </select>';
                }

                break;
            case 'radio' :

                $label_id = current( array_keys( $args['options'] ) );

                if ( ! empty( $args['options'] ) ) {
                    foreach ( $args['options'] as $option_key => $option_text ) {
                        $field .= '<input type="radio" class="input-radio ' . esc_attr( implode( ' ', $args['input_class'] ) ) .'" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';
                        $field .= '<label for="' . esc_attr( $args['id'] ) . '_' . esc_attr( $option_key ) . '" class="radio ' . implode( ' ', $args['label_class'] ) .'">' . $option_text . '</label>';
                    }
                }

                break;
        }

        if ( ! empty( $field ) ) {
            $field_html = '';

            if ( $args['label'] && 'checkbox' != $args['type'] ) {
                $field_html .= '<label for="' . esc_attr( $label_id ) . '" class="' . esc_attr( implode( ' ', $args['label_class'] ) ) .'">' . $args['label'] . $required . '</label>';
            }

            $field_html .= $field;

            if ( $args['description'] ) {
                $field_html .= '<span class="description">' . esc_html( $args['description'] ) . '</span>';
            }

            $container_class = 'form-row ' . esc_attr( implode( ' ', $args['class'] ) );
            $container_id = esc_attr( $args['id'] ) . '_field';

            $after = ! empty( $args['clear'] ) ? '<div class="clear"></div>' : '';

            $field = sprintf( $field_container, $container_class, $container_id, $field_html ) . $after;
        }

        $field = apply_filters( 'woocommerce_form_field_' . $args['type'], $field, $key, $args, $value );

        if ( $args['return'] ) {
            return $field;
        } else {
            echo $field;
        }
	}
}





/* Override Widgets */
add_action( 'widgets_init', 'yp_override_woocommerce_widgets', 15 );
if ( ! function_exists( 'yp_override_woocommerce_widgets' ) ) :
function yp_override_woocommerce_widgets() {
	$override_list = array(
		'WC_Widget_Recently_Viewed'     => 'class-wc-widget-recently-viewed.php',
		'WC_Widget_Top_Rated_Products'  => 'class-wc-widget-top-rated-products.php',
		'WC_Widget_Products'            => 'class-wc-widget-products.php',
		'WC_Widget_Recent_Reviews'      => 'class-wc-widget-recent-reviews.php',
		'WC_Widget_Product_Categories'  => 'class-wc-widget-product-categories.php',
		'WC_Widget_Price_Filter'        => 'class-wc-widget-price-filter.php',
		'WC_Widget_Product_Tag_Cloud'   => 'class-wc-widget-product-tag-cloud.php'
	);

	foreach($override_list as $key => $val) {
		if ( class_exists( $key ) ) {
			unregister_widget( $key );
			include_once( get_template_directory() . '/woocommerce/widgets/' . $val );
			register_widget( 'yp_' . $key );
		}
	}
}
endif;
