<?php
// bbPress is active
if ( !class_exists( 'buddypress' ) ) {
    return;
}

if(!defined('BP_AVATAR_THUMB_WIDTH')) {
    define( 'BP_AVATAR_THUMB_WIDTH', 80 );
}
if(!defined('BP_AVATAR_THUMB_HEIGHT')) {
    define( 'BP_AVATAR_THUMB_HEIGHT', 80 );
}
if(!defined('BP_AVATAR_FULL_WIDTH')) {
    define( 'BP_AVATAR_FULL_WIDTH', 200 );
}
if(!defined('BP_AVATAR_FULL_HEIGHT')) {
    define( 'BP_AVATAR_FULL_HEIGHT', 200 );
}

/* Classes for timeline item */
if ( ! function_exists( 'youplay_get_activity_css_class' ) ) :
function youplay_get_activity_css_class($classes) {
    return str_replace('activity-item', 'activity-item youplay-timeline-block', $classes);
}
endif;
add_filter( 'bp_get_activity_css_class', 'youplay_get_activity_css_class' );


/* Classes for delete button */
if ( ! function_exists( 'youplay_get_activity_delete_link' ) ) :
function youplay_get_activity_delete_link($classes) {
    return str_replace('button item-button bp-secondary-action delete-activity confirm', '', $classes);
}
endif;
add_filter( 'bp_get_activity_delete_link', 'youplay_get_activity_delete_link' );


/* Avatar Sizes */
if ( ! function_exists( 'youplay_core_avatar_thumb_sizes' ) ) :
function youplay_core_avatar_thumb_sizes() {
    return 100;
}
endif;
add_filter( 'bp_core_avatar_thumb_width', 'youplay_core_avatar_thumb_sizes' );
add_filter( 'bp_core_avatar_thumb_height', 'youplay_core_avatar_thumb_sizes' );


/* Responsive oEmbeds */
if ( ! function_exists( 'youplay_bp_embed_oembed_html' ) ) :
function youplay_bp_embed_oembed_html($html) {
    return '<div class="responsive-embed responsive-embed-16x9">' . $html . '</div>';
}
endif;
add_filter( 'bp_embed_oembed_html', 'youplay_bp_embed_oembed_html' );


/**
 * Cover image callback
 *
 * @see bp_legacy_theme_cover_image() to discover the one used by BP Legacy
 */
if ( ! function_exists( 'youplay_cover_image_callback' ) ) :
function youplay_cover_image_callback( $params = array() ) {
    if ( empty( $params ) ) {
        return;
    }

    return '';
}
endif;

if ( ! function_exists( 'youplay_cover_image_css' ) ) :
function youplay_cover_image_css( $settings = array() ) {
    $settings['callback'] = 'youplay_cover_image_callback';
    return $settings;
}
endif;
add_filter( 'bp_before_xprofile_cover_image_settings_parse_args', 'youplay_cover_image_css', 10, 1 );
add_filter( 'bp_before_groups_cover_image_settings_parse_args', 'youplay_cover_image_css', 10, 1 );


/**
 * Style default buddypress buttons
 */
if ( ! function_exists( 'youplay_bp_profile_buttons' ) ) :
function youplay_bp_profile_buttons( $button ) {
    $button['link_class'] .= ' btn btn-sm btn-default';
    $button['wrapper'] = false;
    return $button;
}
endif;
add_filter( 'bp_get_add_friend_button', 'youplay_bp_profile_buttons' );
add_filter( 'bp_get_send_public_message_button', 'youplay_bp_profile_buttons' );
add_filter( 'bp_get_send_message_button_args', 'youplay_bp_profile_buttons' );
add_filter( 'bp_get_group_join_button', 'youplay_bp_profile_buttons' );
add_filter( 'bp_get_group_new_topic_button', 'youplay_bp_profile_buttons' );



/* Changed BuddyPress tabs */
if ( ! function_exists( 'youplay_bp_get_options_nav' ) ) :
function youplay_bp_get_options_nav() {
    ob_start();
    bp_get_options_nav();
    $result = ob_get_contents();
    ob_end_clean();

    $result = str_replace('current selected', 'current active', $result);
    $result = str_replace('<span class="count">', '<span class="badge mnb-1">', $result);
    $result = str_replace('<span class="no-count">', '<span class="badge mnb-1 sr-only">', $result);
    $result = str_replace('<span>', '<span class="badge mnb-1">', $result);

    echo $result;
}
endif;
if ( ! function_exists( 'youplay_bp_get_displayed_user_nav' ) ) :
function youplay_bp_get_displayed_user_nav() {
    ob_start();
    bp_get_displayed_user_nav();
    $result = ob_get_contents();
    ob_end_clean();

    $result = str_replace('current selected', 'current active', $result);
    $result = str_replace('<span class="count">', '<span class="badge mnb-1">', $result);
    $result = str_replace('<span class="no-count">', '<span class="badge mnb-1 sr-only">', $result);
    $result = str_replace('<span>', '<span class="badge mnb-1">', $result);

    echo $result;
}
endif;
if ( ! function_exists( 'youplay_bp_group_admin_tabs' ) ) :
function youplay_bp_group_admin_tabs() {
    ob_start();
    bp_group_admin_tabs();
    $result = ob_get_contents();
    ob_end_clean();

    $result = str_replace('current selected', 'current active', $result);
    $result = str_replace('<span class="count">', '<span class="badge mnb-1">', $result);
    $result = str_replace('<span class="no-count">', '<span class="badge mnb-1 sr-only">', $result);
    $result = str_replace('<span>', '<span class="badge mnb-1">', $result);

    echo $result;
}
endif;


/* Changed get template to return value, not print */
if ( ! function_exists( 'youplay_bp_get_template_part' ) ) :
function youplay_bp_get_template_part($slug, $name = null) {
    ob_start();
    bp_get_template_part($slug, $name);
    $result = ob_get_contents();
    ob_end_clean();

    return $result;
}
endif;



/* Notifications */
if ( ! function_exists( 'youplay_bp_notifications_bulk_management_dropdown' ) ) :
function youplay_bp_notifications_bulk_management_dropdown() {
  ?>
    <label class="bp-screen-reader-text" for="notification-select"><?php _e( 'Select Bulk Action', 'youplay' ); ?></label>
    <div class="youplay-select dib" style="width: auto;">
        <select name="notification_bulk_action" id="notification-select">
            <option value="" selected="selected"><?php _e( 'Bulk Actions', 'youplay' ); ?></option>

            <?php if ( bp_is_current_action( 'unread' ) ) : ?>
                <option value="read"><?php _e( 'Mark read', 'youplay' ); ?></option>
            <?php elseif ( bp_is_current_action( 'read' ) ) : ?>
                <option value="unread"><?php _e( 'Mark unread', 'youplay' ); ?></option>
            <?php endif; ?>
            <option value="delete"><?php _e( 'Delete', 'youplay' ); ?></option>
        </select>
    </div>
    <button type="submit" id="notification-bulk-manage" class="button action btn btn-sm btn-default"><?php esc_html_e( 'Apply', 'youplay' ); ?></button>
    <?php
}
endif;
if ( ! function_exists( 'youplay_bp_notifications_sort_order_form' ) ) :
function youplay_bp_notifications_sort_order_form() {

  // Setup local variables.
  $orders   = array( 'DESC', 'ASC' );
  $selected = 'DESC';

  // Check for a custom sort_order.
  if ( !empty( $_REQUEST['sort_order'] ) ) {
    if ( in_array( $_REQUEST['sort_order'], $orders ) ) {
      $selected = $_REQUEST['sort_order'];
    }
  } ?>

    <form action="" method="get" id="notifications-sort-order">
        <label for="notifications-sort-order-list"><?php esc_html_e( 'Order By:', 'youplay' ); ?></label>

        <div class="youplay-select dib" style="width: auto;">
            <select id="notifications-sort-order-list" name="sort_order" onchange="this.form.submit();">
                <option value="DESC" <?php selected( $selected, 'DESC' ); ?>><?php _e( 'Newest First', 'youplay' ); ?></option>
                <option value="ASC"  <?php selected( $selected, 'ASC'  ); ?>><?php _e( 'Oldest First', 'youplay' ); ?></option>
            </select>
        </div>

        <noscript>
            <button id="submit" type="submit" name="form-submit" class="submit"><?php esc_html_e( 'Go', 'youplay' ); ?></button>
        </noscript>
    </form>

<?php
}
endif;


/* Messages */
if ( ! function_exists( 'youplay_bp_message_search_form' ) ) :
function youplay_bp_message_search_form() {

  // Get the default search text.
  $default_search_value = bp_get_search_default_text( 'messages' );

  // Setup a few values based on what's being searched for.
  $search_submitted     = ! empty( $_REQUEST['s'] ) ? stripslashes( $_REQUEST['s'] ) : $default_search_value;
  $search_placeholder   = ( $search_submitted === $default_search_value ) ? ' placeholder="' .  esc_attr( $search_submitted ) . '"' : '';
  $search_value         = ( $search_submitted !== $default_search_value ) ? ' value="'       .  esc_attr( $search_submitted ) . '"' : '';

  // Start the output buffer, so form can be filtered.
  ob_start(); ?>

    <form action="" method="get" id="search-message-form">
        <label for="messages_search" class="sr-only"><?php esc_html_e( 'Search Messages', 'youplay' ); ?></label>
        <div class="youplay-input">
            <input type="text" name="s" id="messages_search"<?php echo $search_placeholder . $search_value; ?> />
        </div>
    </form>

    <?php

  // Get the search form from the above output buffer.
  $search_form_html = ob_get_clean();


/**
 * Filters the private message component search form.
 *
 * @since 2.2.0
 *
 * @param string $search_form_html HTML markup for the message search form.
 */
  echo apply_filters( 'bp_message_search_form', $search_form_html );
}
endif;

if ( ! function_exists( 'youplay_bp_message_thread_unread_count' ) ) :
function youplay_bp_message_thread_unread_count( $thread_id = false ) {
    if ( false === $thread_id ) {
        $thread_id = bp_get_message_thread_id();
    }

    $unread = bp_get_message_thread_unread_count( $thread_id );

    return $unread;
}
endif;

if ( ! function_exists( 'youplay_bp_messages_bulk_management_dropdown' ) ) :
function youplay_bp_messages_bulk_management_dropdown() {
    ?>
    <label class="bp-screen-reader-text" for="messages-select"><?php _e( 'Select Bulk Action', 'youplay' ); ?></label>
    <div class="youplay-select dib" style="width: auto;">
        <select name="messages_bulk_action" id="messages-select">
            <option value="" selected="selected"><?php _e( 'Bulk Actions', 'youplay' ); ?></option>
            <option value="read"><?php _e( 'Mark read', 'youplay' ); ?></option>
            <option value="unread"><?php _e( 'Mark unread', 'youplay' ); ?></option>
            <option value="delete"><?php _e( 'Delete', 'youplay' ); ?></option>
            <?php
                /**
                 * Action to add additional options to the messages bulk management dropdown.
                 *
                 * @since 2.3.0
                 */
                do_action( 'bp_messages_bulk_management_dropdown' );
            ?>
        </select>
    </div>
    <button type="submit" id="messages-bulk-manage" class="button action btn btn-sm btn-default"><?php esc_html_e( 'Apply', 'youplay' ); ?></button>
    <?php
}
endif;



/* Settings */
if ( ! function_exists( 'youplay_bp_profile_settings_visibility_select' ) ) :
function youplay_bp_profile_settings_visibility_select( $select ) {
    $select = str_replace('<select', '<div class="youplay-select"><select', $select);
    $select = str_replace('</select>', '</select></div>', $select);
    return $select;
}
endif;
add_filter( 'bp_profile_settings_visibility_select', 'youplay_bp_profile_settings_visibility_select' );



/* Groups */
if ( ! function_exists( 'youplay_bp_groups_members_filter' ) ) :
function youplay_bp_groups_members_filter() {
    ?>
    <li id="group_members-order-select" class="last filter">
        <label for="group_members-order-by"><?php _e( 'Order By:', 'youplay' ); ?></label>
        <div class="youplay-select">
            <select id="group_members-order-by">
                <option value="last_joined"><?php _e( 'Newest', 'youplay' ); ?></option>
                <option value="first_joined"><?php _e( 'Oldest', 'youplay' ); ?></option>

                <?php if ( bp_is_active( 'activity' ) ) : ?>
                    <option value="group_activity"><?php _e( 'Group Activity', 'youplay' ); ?></option>
                <?php endif; ?>

                <option value="alphabetical"><?php _e( 'Alphabetical', 'youplay' ); ?></option>

                <?php

                /**
                 * Fires at the end of the Group members filters select input.
                 *
                 * Useful for plugins to add more filter options.
                 *
                 * @since 2.0.0
                 */
                do_action( 'bp_groups_members_order_options' ); ?>

            </select>
        </div>
    </li>
    <?php
}
endif;
if ( ! function_exists( 'youplay_bp_groups_members_template_part' ) ) :
function youplay_bp_groups_members_template_part() {
    ?>
    <div class="item-list-tabs" id="subnav" role="navigation text-mute">
        <ul>
            <li role="search">
                <?php youplay_bp_directory_members_search_form(); ?>
            </li>

            <?php youplay_bp_groups_members_filter(); ?>
            <?php

            /**
             * Fires at the end of the group members search unordered list.
             *
             * Part of bp_groups_members_template_part().
             *
             * @since 1.5.0
             */
            do_action( 'bp_members_directory_member_sub_types' ); ?>

        </ul>
    </div>

    <div id="members-group-list" class="group_members dir-list">

        <?php bp_get_template_part( 'groups/single/members' ); ?>

    </div>
    <?php
}
endif;
if ( ! function_exists( 'youplay_bp_directory_members_search_form' ) ) :
function youplay_bp_directory_members_search_form() {
    $query_arg = bp_core_get_component_search_query_arg( 'members' );

    if ( ! empty( $_REQUEST[ $query_arg ] ) ) {
        $search_value = stripslashes( $_REQUEST[ $query_arg ] );
    } else {
        $search_value = bp_get_search_default_text( 'members' );
    }

    $search_form_html = '<form action="" method="get" id="search-members-form">
        <div class="youplay-input dib">
            <input type="text" name="' . esc_attr( $query_arg ) . '" id="members_search" placeholder="'. esc_attr( $search_value ) .'" />
        </div>
        <button type="submit" id="members_search_submit" name="members_search_submit" class="btn btn-default">' . __( 'Search', 'youplay' ) . '</button>
    </form>';

    echo apply_filters( 'bp_directory_members_search_form', $search_form_html );
}
endif;


if ( ! function_exists( 'youplay_bp_directory_groups_search_form' ) ) :
function youplay_bp_directory_groups_search_form() {

    $query_arg = bp_core_get_component_search_query_arg( 'groups' );

    if ( ! empty( $_REQUEST[ $query_arg ] ) ) {
        $search_value = stripslashes( $_REQUEST[ $query_arg ] );
    } else {
        $search_value = bp_get_search_default_text( 'groups' );
    }

    $search_form_html = '<form action="" method="get" id="search-groups-form">
        <div class="youplay-input dib">
        <input type="text" name="' . esc_attr( $query_arg ) . '" id="groups_search" placeholder="'. esc_attr( $search_value ) .'" />
        </div>
        <button type="submit" id="groups_search_submit" name="groups_search_submit" class="btn btn-default">'. __( 'Search', 'youplay' ) .'</button>
    </form>';

    /**
     * Filters the HTML markup for the groups search form.
     *
     * @since 1.9.0
     *
     * @param string $search_form_html HTML markup for the search form.
     */
    echo apply_filters( 'bp_directory_groups_search_form', $search_form_html );
}
endif;

if ( ! function_exists( 'youplay_bp_group_creation_tabs' ) ) :
function youplay_bp_group_creation_tabs() {
    $bp = buddypress();

    if ( !is_array( $bp->groups->group_creation_steps ) ) {
        return false;
    }

    if ( !bp_get_groups_current_create_step() ) {
        $keys = array_keys( $bp->groups->group_creation_steps );
        $bp->groups->current_create_step = array_shift( $keys );
    }

    $counter = 1;

    foreach ( (array) $bp->groups->group_creation_steps as $slug => $step ) {
        $is_enabled = bp_are_previous_group_creation_steps_complete( $slug ); ?>

        <li<?php if ( bp_get_groups_current_create_step() == $slug ) : ?> class="active"<?php endif; ?>><?php if ( $is_enabled ) : ?><a href="<?php bp_groups_directory_permalink(); ?>create/step/<?php echo $slug ?>/"><?php else: ?><span><?php endif; ?><?php echo $counter ?>. <?php echo $step['name'] ?><?php if ( $is_enabled ) : ?></a><?php else: ?></span><?php endif ?></li><?php
        $counter++;
    }

    unset( $is_enabled );

    /**
     * Fires at the end of the creation of the group tabs.
     *
     * @since 1.0.0
     */
    do_action( 'groups_creation_tabs' );
}
endif;




/**
 * Style default xProfile fields
 */
if(class_exists('BP_XProfile_Field_Type_Number')) :
class youplay_BP_XProfile_Field_Type_Number extends BP_XProfile_Field_Type_Number {
    public function edit_field_html( array $raw_properties = array() ) {

        // User_id is a special optional parameter that certain other fields
        // types pass to {@link bp_the_profile_field_options()}.
        if ( isset( $raw_properties['user_id'] ) ) {
            unset( $raw_properties['user_id'] );
        }

        $r = bp_parse_args( $raw_properties, array(
            'type'  => 'number',
            'value' =>  bp_get_the_profile_field_edit_value()
        ) ); ?>

        <label for="<?php bp_the_profile_field_input_name(); ?>">
            <?php bp_the_profile_field_name(); ?>
            <?php bp_the_profile_field_required_label(); ?>
        </label>

        <?php

        /** This action is documented in bp-xprofile/bp-xprofile-classes */
        do_action( bp_get_the_profile_field_errors_action() ); ?>

        <div class="youplay-input">
            <input <?php echo $this->get_edit_field_html_elements( $r ); ?>>
        </div>

        <?php
    }
}
endif;

if(class_exists('BP_XProfile_Field_Type_Datebox')) :
class youplay_BP_XProfile_Field_Type_Datebox extends BP_XProfile_Field_Type_Datebox {
    public function edit_field_html( array $raw_properties = array() ) {

        // User_id is a special optional parameter that we pass to.
        // {@link bp_the_profile_field_options()}.
        if ( isset( $raw_properties['user_id'] ) ) {
            $user_id = (int) $raw_properties['user_id'];
            unset( $raw_properties['user_id'] );
        } else {
            $user_id = bp_displayed_user_id();
        }

        $day_r = bp_parse_args( $raw_properties, array(
            'id'   => bp_get_the_profile_field_input_name() . '_day',
            'name' => bp_get_the_profile_field_input_name() . '_day'
        ) );

        $month_r = bp_parse_args( $raw_properties, array(
            'id'   => bp_get_the_profile_field_input_name() . '_month',
            'name' => bp_get_the_profile_field_input_name() . '_month'
        ) );

        $year_r = bp_parse_args( $raw_properties, array(
            'id'   => bp_get_the_profile_field_input_name() . '_year',
            'name' => bp_get_the_profile_field_input_name() . '_year'
        ) ); ?>

        <div class="datebox">

            <label for="<?php bp_the_profile_field_input_name(); ?>_day">
                <?php bp_the_profile_field_name(); ?>
                <?php bp_the_profile_field_required_label(); ?>
            </label>

            <br>

            <?php

            /**
             * Fires after field label and displays associated errors for the field.
             *
             * This is a dynamic hook that is dependent on the associated
             * field ID. The hooks will be similar to `bp_field_12_errors`
             * where the 12 is the field ID. Simply replace the 12 with
             * your needed target ID.
             *
             * @since 1.8.0
             */
            do_action( bp_get_the_profile_field_errors_action() ); ?>

            <div class="youplay-select dib">
                <select <?php echo $this->get_edit_field_html_elements( $day_r ); ?>>
                    <?php bp_the_profile_field_options( array(
                        'type'    => 'day',
                        'user_id' => $user_id
                    ) ); ?>
                </select>
            </div>

            <div class="youplay-select dib">
                <select <?php echo $this->get_edit_field_html_elements( $month_r ); ?>>
                    <?php bp_the_profile_field_options( array(
                        'type'    => 'month',
                        'user_id' => $user_id
                    ) ); ?>
                </select>
            </div>

            <div class="youplay-select dib">
                <select <?php echo $this->get_edit_field_html_elements( $year_r ); ?>>
                    <?php bp_the_profile_field_options( array(
                        'type'    => 'year',
                        'user_id' => $user_id
                    ) ); ?>
                </select>
            </div>

        </div>
    <?php
    }
}
endif;

if(class_exists('BP_XProfile_Field_Type_URL')) :
class youplay_BP_XProfile_Field_Type_URL extends BP_XProfile_Field_Type_URL {
    public function edit_field_html( array $raw_properties = array() ) {

        // `user_id` is a special optional parameter that certain other
        // fields types pass to {@link bp_the_profile_field_options()}.
        if ( isset( $raw_properties['user_id'] ) ) {
            unset( $raw_properties['user_id'] );
        }

        $r = bp_parse_args( $raw_properties, array(
            'type'      => 'text',
            'inputmode' => 'url',
            'value'     => esc_url( bp_get_the_profile_field_edit_value() ),
        ) ); ?>

        <label for="<?php bp_the_profile_field_input_name(); ?>">
            <?php bp_the_profile_field_name(); ?>
            <?php bp_the_profile_field_required_label(); ?>
        </label>

        <?php

        /** This action is documented in bp-xprofile/bp-xprofile-classes */
        do_action( bp_get_the_profile_field_errors_action() ); ?>

        <div class="youplay-input">
            <input <?php echo $this->get_edit_field_html_elements( $r ); ?>>
        </div>

        <?php
    }
}
endif;

if(class_exists('BP_XProfile_Field_Type_Textbox')) :
class youplay_BP_XProfile_Field_Type_Textbox extends BP_XProfile_Field_Type_Textbox {
    public function edit_field_html( array $raw_properties = array() ) {

        // User_id is a special optional parameter that certain other fields
        // types pass to {@link bp_the_profile_field_options()}.
        if ( isset( $raw_properties['user_id'] ) ) {
            unset( $raw_properties['user_id'] );
        }

        $r = bp_parse_args( $raw_properties, array(
            'type'  => 'text',
            'value' => bp_get_the_profile_field_edit_value(),
        ) ); ?>

        <label for="<?php bp_the_profile_field_input_name(); ?>">
            <?php bp_the_profile_field_name(); ?>
            <?php bp_the_profile_field_required_label(); ?>
        </label>

        <?php

        /** This action is documented in bp-xprofile/bp-xprofile-classes */
        do_action( bp_get_the_profile_field_errors_action() ); ?>

        <div class="youplay-input">
            <input <?php echo $this->get_edit_field_html_elements( $r ); ?>>
        </div>

        <?php
    }
}
endif;

if(class_exists('BP_XProfile_Field_Type_Checkbox')) :
class youplay_BP_XProfile_Field_Type_Checkbox extends BP_XProfile_Field_Type_Checkbox {
    public function edit_field_html( array $raw_properties = array() ) {

        // User_id is a special optional parameter that we pass to
        // {@link bp_the_profile_field_options()}.
        if ( isset( $raw_properties['user_id'] ) ) {
            $user_id = (int) $raw_properties['user_id'];
            unset( $raw_properties['user_id'] );
        } else {
            $user_id = bp_displayed_user_id();
        } ?>

        <label for="<?php bp_the_profile_field_input_name(); ?>">
            <?php bp_the_profile_field_name(); ?>
            <?php bp_the_profile_field_required_label(); ?>
        </label>

        <?php

        /** This action is documented in bp-xprofile/bp-xprofile-classes */
        do_action( bp_get_the_profile_field_errors_action() ); ?>

        <?php bp_the_profile_field_options( array(
            'user_id' => $user_id
        ) ); ?>

        <?php
    }
}
endif;

if(class_exists('BP_XProfile_Field_Type_Radiobutton')) :
class youplay_BP_XProfile_Field_Type_Radiobutton extends BP_XProfile_Field_Type_Radiobutton {
    public function edit_field_html( array $raw_properties = array() ) {

        // User_id is a special optional parameter that we pass to
        // {@link bp_the_profile_field_options()}.
        if ( isset( $raw_properties['user_id'] ) ) {
            $user_id = (int) $raw_properties['user_id'];
            unset( $raw_properties['user_id'] );
        } else {
            $user_id = bp_displayed_user_id();
        } ?>

        <div>

            <label for="<?php bp_the_profile_field_input_name(); ?>">
                <?php bp_the_profile_field_name(); ?>
                <?php bp_the_profile_field_required_label(); ?>
            </label>

            <?php

            /** This action is documented in bp-xprofile/bp-xprofile-classes */
            do_action( bp_get_the_profile_field_errors_action() ); ?>

            <?php bp_the_profile_field_options( array( 'user_id' => $user_id ) );

            if ( ! bp_get_the_profile_field_is_required() ) : ?>

                <a class="clear-value" href="javascript:clear( '<?php echo esc_js( bp_get_the_profile_field_input_name() ); ?>' );">
                    <?php esc_html_e( 'Clear', 'youplay' ); ?>
                </a>

            <?php endif; ?>

        </div>

        <?php
    }
}
endif;

if(class_exists('BP_XProfile_Field_Type_Selectbox')) :
class youplay_BP_XProfile_Field_Type_Selectbox extends BP_XProfile_Field_Type_Selectbox {
    public function edit_field_html( array $raw_properties = array() ) {

        // User_id is a special optional parameter that we pass to
        // {@link bp_the_profile_field_options()}.
        if ( isset( $raw_properties['user_id'] ) ) {
            $user_id = (int) $raw_properties['user_id'];
            unset( $raw_properties['user_id'] );
        } else {
            $user_id = bp_displayed_user_id();
        } ?>

        <label for="<?php bp_the_profile_field_input_name(); ?>">
            <?php bp_the_profile_field_name(); ?>
            <?php bp_the_profile_field_required_label(); ?>
        </label>

        <br>

        <?php

        /** This action is documented in bp-xprofile/bp-xprofile-classes */
        do_action( bp_get_the_profile_field_errors_action() ); ?>

        <div class="youplay-select dib">
            <select <?php echo $this->get_edit_field_html_elements( $raw_properties ); ?>>
                <?php bp_the_profile_field_options( array( 'user_id' => $user_id ) ); ?>
            </select>
        </div>

        <?php
    }
}
endif;

if(class_exists('BP_XProfile_Field_Type_Multiselectbox')) :
class youplay_BP_XProfile_Field_Type_Multiselectbox extends BP_XProfile_Field_Type_Multiselectbox {
    public function edit_field_html( array $raw_properties = array() ) {

        // User_id is a special optional parameter that we pass to
        // {@link bp_the_profile_field_options()}.
        if ( isset( $raw_properties['user_id'] ) ) {
            $user_id = (int) $raw_properties['user_id'];
            unset( $raw_properties['user_id'] );
        } else {
            $user_id = bp_displayed_user_id();
        }

        $r = bp_parse_args( $raw_properties, array(
            'multiple' => 'multiple',
            'id'       => bp_get_the_profile_field_input_name() . '[]',
            'name'     => bp_get_the_profile_field_input_name() . '[]',
        ) ); ?>

        <label for="<?php bp_the_profile_field_input_name(); ?>[]">
            <?php bp_the_profile_field_name(); ?>
            <?php bp_the_profile_field_required_label(); ?>
        </label>

        <?php

        /** This action is documented in bp-xprofile/bp-xprofile-classes */
        do_action( bp_get_the_profile_field_errors_action() ); ?>

        <div class="youplay-select">
            <select <?php echo $this->get_edit_field_html_elements( $r ); ?>>
                <?php bp_the_profile_field_options( array(
                    'user_id' => $user_id
                ) ); ?>
            </select>
        </div>

        <?php if ( ! bp_get_the_profile_field_is_required() ) : ?>

            <a class="clear-value" href="javascript:clear( '<?php echo esc_js( bp_get_the_profile_field_input_name() ); ?>[]' );">
                <?php esc_html_e( 'Clear', 'youplay' ); ?>
            </a>

        <?php endif; ?>
    <?php
    }
}
endif;

if ( ! function_exists( 'youplay_bp_xprofile_get_field_types' ) ) :
function youplay_bp_xprofile_get_field_types( $fields = array('') ) {
    $new_fields = array();
    foreach($fields as $k => $field) {
        switch($k) {
            case 'number':
                $field = 'youplay_BP_XProfile_Field_Type_Number';
                break;
            case 'datebox':
                $field = 'youplay_BP_XProfile_Field_Type_Datebox';
                break;
            case 'url':
                $field = 'youplay_BP_XProfile_Field_Type_URL';
                break;
            case 'textbox':
                $field = 'youplay_BP_XProfile_Field_Type_Textbox';
                break;
            case 'checkbox':
                $field = 'youplay_BP_XProfile_Field_Type_Checkbox';
                break;
            case 'radio':
                $field = 'youplay_BP_XProfile_Field_Type_Radiobutton';
                break;
            case 'selectbox':
                $field = 'youplay_BP_XProfile_Field_Type_Selectbox';
                break;
            case 'multiselectbox':
                $field = 'youplay_BP_XProfile_Field_Type_Multiselectbox';
                break;
        }
        $new_fields[$k] = $field;
    }
    return $new_fields;
}
endif;
add_filter( 'bp_xprofile_get_field_types', 'youplay_bp_xprofile_get_field_types' );

/* Group Tabs */
if ( ! function_exists( 'youplay_xprofile_filter_profile_group_tabs' ) ) :
function youplay_xprofile_filter_profile_group_tabs( $tabs = array('') ) {
    $new_tabs = array();
    foreach($tabs as $tab) {
        $new_tabs[] = str_replace('class="current"', 'class="current active"', $tab);
    }
    return $new_tabs;
}
endif;
add_filter( 'xprofile_filter_profile_group_tabs', 'youplay_xprofile_filter_profile_group_tabs' );

/* Checkbox */
if ( ! function_exists( 'youplay_bp_get_the_profile_field_options_checkbox' ) ) :
function youplay_bp_get_the_profile_field_options_checkbox( $html, $options = null, $id = null, $selected = null, $k = null ) {
    $new_html = sprintf( '<div class="youplay-checkbox ml-10"><input %1$s type="checkbox" name="%2$s" id="%3$s" value="%4$s"><label for="%3$s">%5$s</label></div>',
        $selected,
        esc_attr( "field_{$id}[]" ),
        esc_attr( "field_{$options->id}_{$k}" ),
        esc_attr( stripslashes( $options->name ) ),
        esc_html( stripslashes( $options->name ) )
    );
    return $new_html;
}
endif;
add_filter( 'bp_get_the_profile_field_options_checkbox', 'youplay_bp_get_the_profile_field_options_checkbox', 10, 5 );

/* Radio */
if ( ! function_exists( 'youplay_bp_get_the_profile_field_options_radio' ) ) :
function youplay_bp_get_the_profile_field_options_radio( $html, $options = null, $id = null, $selected = null, $k = null ) {
    $new_html = sprintf( '<div class="youplay-radio ml-10"><input %1$s type="radio" name="%2$s" id="%3$s" value="%4$s"><label for="%3$s">%5$s</label></div>',
        $selected,
        esc_attr( "field_{$id}[]" ),
        esc_attr( "field_{$options->id}_{$k}" ),
        esc_attr( stripslashes( $options->name ) ),
        esc_html( stripslashes( $options->name ) )
    );
    return $new_html;
}
endif;
add_filter( 'bp_get_the_profile_field_options_radio', 'youplay_bp_get_the_profile_field_options_radio', 10, 5 );
