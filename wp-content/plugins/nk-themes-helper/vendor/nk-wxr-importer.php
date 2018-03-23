<?php

class NK_WXR_Importer extends WXR_Importer {
    /**
     * Constructor method.
     *
     * @param array $options Importer options.
     */
    public function __construct( $options = array() ) {
        parent::__construct( $options );

        // Set current user to $mapping variable.
        // Fixes the [WARNING] Could not find the author for ... log warning messages.
        $current_user_obj = wp_get_current_user();
        $this->mapping['user_slug'][ $current_user_obj->user_login ] = $current_user_obj->ID;

        // WooCommerce product attributes registration.
        if ( class_exists( 'WooCommerce' ) ) {
            add_filter( 'wxr_importer.pre_process.term', array( $this, 'woocommerce_product_attributes_registration' ), 10, 1 );
        }
    }

    /**
     * Hook into the pre-process term filter of the content import and register the
     * custom WooCommerce product attributes, so that the terms can then be imported normally.
     *
     * This should probably be removed once the WP importer 2.0 support is added in WooCommerce.
     *
     * Fixes: [WARNING] Failed to import pa_size L warnings in content import.
     * Code from: woocommerce/includes/admin/class-wc-admin-importers.php (ver 2.6.9).
     *
     * Github issue: https://github.com/proteusthemes/one-click-demo-import/issues/71
     *
     * @param  array $date The term data to import.
     * @return array       The unchanged term data.
     */
    public function woocommerce_product_attributes_registration( $data ) {
        global $wpdb;

        if ( strstr( $data['taxonomy'], 'pa_' ) ) {
            if ( ! taxonomy_exists( $data['taxonomy'] ) ) {
                $attribute_name = wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $data['taxonomy'] ) );

                // Create the taxonomy
                if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies() ) ) {
                    $attribute = array(
                        'attribute_label'   => $attribute_name,
                        'attribute_name'    => $attribute_name,
                        'attribute_type'    => 'select',
                        'attribute_orderby' => 'menu_order',
                        'attribute_public'  => 0
                    );
                    $wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
                    delete_transient( 'wc_attribute_taxonomies' );
                }

                // Register the taxonomy now so that the import works!
                register_taxonomy(
                    $data['taxonomy'],
                    apply_filters( 'woocommerce_taxonomy_objects_' . $data['taxonomy'], array( 'product' ) ),
                    apply_filters( 'woocommerce_taxonomy_args_' . $data['taxonomy'], array(
                        'hierarchical' => true,
                        'show_ui'      => false,
                        'query_var'    => true,
                        'rewrite'      => false,
                    ) )
                );
            }
        }

        return $data;
    }

    /**
     * Parse a meta node into meta data.
     *
     * Fixes: not importing custom fields with 0 value
     * Github issue: https://github.com/humanmade/WordPress-Importer/issues/72
     *
     * @param DOMElement $node Parent node of meta data (typically `wp:postmeta` or `wp:commentmeta`).
     * @return array|null Meta data array on success, or null on error.
     */
    protected function parse_meta_node( $node ) {
        foreach ( $node->childNodes as $child ) {
            // We only care about child elements
            if ( $child->nodeType !== XML_ELEMENT_NODE ) {
                continue;
            }

            switch ( $child->tagName ) {
                case 'wp:meta_key':
                    $key = $child->textContent;
                    break;

                case 'wp:meta_value':
                    $value = $child->textContent;
                    break;
            }
        }

        if ( empty( $key ) ) {
            return null;
        }

        return compact( 'key', 'value' );
    }

    /**
     * Attempt to download a remote file attachment.
     * The problem in default method - on some hosts there is error.
     * For some reason on some hosts filesize check failed. Replaced it with md5 check.
     *
     * @param string $url URL of item to fetch
     * @param array $post Attachment details
     * @return array|WP_Error Local file location details on success, WP_Error otherwise
     */
    protected function fetch_remote_file( $url, $post ) {
        // extract the file name and extension from the url
        $file_name = basename( $url );

        // get placeholder file in the upload dir with a unique, sanitized filename
        $upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
        if ( $upload['error'] ) {
            return new WP_Error( 'upload_dir_error', $upload['error'] );
        }

        // fetch the remote url and write it to the placeholder file
        $response = wp_remote_get( $url, array(
            'stream' => true,
            'filename' => $upload['file'],
        ) );

        // request failed
        if ( is_wp_error( $response ) ) {
            unlink( $upload['file'] );
            return $response;
        }

        $code = (int) wp_remote_retrieve_response_code( $response );

        // make sure the fetch was successful
        if ( $code !== 200 ) {
            unlink( $upload['file'] );
            return new WP_Error(
                'import_file_error',
                sprintf(
                    __( 'Remote server returned %1$d %2$s for %3$s', 'nk-themes-helper' ),
                    $code,
                    get_status_header_desc( $code ),
                    $url
                )
            );
        }

        $content_md5 = wp_remote_retrieve_header( $response, 'content-md5' );
        if ( $content_md5 ) {
            $md5_check = verify_file_md5( $upload['file'], $content_md5 );
            if ( is_wp_error( $md5_check ) ) {
                unlink( $upload['file'] );
                return new WP_Error( 'import_file_error', __( 'Remote file is incorrect md5', 'nk-themes-helper' ) );
            }
        }

        $filesize = filesize( $upload['file'] );

        $max_size = (int) $this->max_attachment_size();
        if ( ! empty( $max_size ) && $filesize > $max_size ) {
            unlink( $upload['file'] );
            $message = sprintf( __( 'Remote file is too large, limit is %s', 'nk-themes-helper' ), size_format( $max_size ) );
            return new WP_Error( 'import_file_error', $message );
        }

        return $upload;
    }

    /**
     * Add actions for skipped data
     *
     * Github issue: https://github.com/humanmade/WordPress-Importer/pull/79/
     */
    protected function process_post( $data, $meta, $comments, $terms ) {
        /**
         * Pre-process post data.
         *
         * @param array $data Post data. (Return empty to skip.)
         * @param array $meta Meta data.
         * @param array $comments Comments on the post.
         * @param array $terms Terms on the post.
         */
        $data = apply_filters( 'wxr_importer.pre_process.post', $data, $meta, $comments, $terms );
        if ( empty( $data ) ) {
            return false;
        }

        $original_id = isset( $data['post_id'] )     ? (int) $data['post_id']     : 0;
        $parent_id   = isset( $data['post_parent'] ) ? (int) $data['post_parent'] : 0;
        $author_id   = isset( $data['post_author'] ) ? (int) $data['post_author'] : 0;

        // Have we already processed this?
        if ( isset( $this->mapping['post'][ $original_id ] ) ) {
            return;
        }

        $post_type_object = get_post_type_object( $data['post_type'] );

        // Is this type even valid?
        if ( ! $post_type_object ) {
            $this->logger->warning( sprintf(
                __( 'Failed to import "%s": Invalid post type %s', 'nk-themes-helper' ),
                $data['post_title'],
                $data['post_type']
            ) );
            return false;
        }

        $post_exists = $this->post_exists( $data );
        if ( $post_exists ) {
            $this->logger->info( sprintf(
                __( '%s "%s" already exists.', 'nk-themes-helper' ),
                $post_type_object->labels->singular_name,
                $data['post_title']
            ) );

            /**
             * Post processing already imported.
             *
             * @param array $data Raw data imported for the post.
             */
            do_action( 'wxr_importer.process_already_imported.post', $post_exists, $data );

            // Even though this post already exists, new comments might need importing
            $this->process_comments( $comments, $original_id, $data, $post_exists );

            return false;
        }

        // Map the parent post, or mark it as one we need to fix
        $requires_remapping = false;
        if ( $parent_id ) {
            if ( isset( $this->mapping['post'][ $parent_id ] ) ) {
                $data['post_parent'] = $this->mapping['post'][ $parent_id ];
            } else {
                $meta[] = array( 'key' => '_wxr_import_parent', 'value' => $parent_id );
                $requires_remapping = true;

                $data['post_parent'] = 0;
            }
        }

        // Map the author, or mark it as one we need to fix
        $author = sanitize_user( $data['post_author'], true );
        if ( empty( $author ) ) {
            // Missing or invalid author, use default if available.
            $data['post_author'] = $this->options['default_author'];
        } elseif ( isset( $this->mapping['user_slug'][ $author ] ) ) {
            $data['post_author'] = $this->mapping['user_slug'][ $author ];
        } else {
            $meta[] = array( 'key' => '_wxr_import_user_slug', 'value' => $author );
            $requires_remapping = true;

            $data['post_author'] = (int) get_current_user_id();
        }

        // Does the post look like it contains attachment images?
        if ( preg_match( self::REGEX_HAS_ATTACHMENT_REFS, $data['post_content'] ) ) {
            $meta[] = array( 'key' => '_wxr_import_has_attachment_refs', 'value' => true );
            $requires_remapping = true;
        }

        // Whitelist to just the keys we allow
        $postdata = array(
            'import_id' => $data['post_id'],
        );
        $allowed = array(
            'post_author'    => true,
            'post_date'      => true,
            'post_date_gmt'  => true,
            'post_content'   => true,
            'post_excerpt'   => true,
            'post_title'     => true,
            'post_status'    => true,
            'post_name'      => true,
            'comment_status' => true,
            'ping_status'    => true,
            'guid'           => true,
            'post_parent'    => true,
            'menu_order'     => true,
            'post_type'      => true,
            'post_password'  => true,
        );
        foreach ( $data as $key => $value ) {
            if ( ! isset( $allowed[ $key ] ) ) {
                continue;
            }

            $postdata[ $key ] = $data[ $key ];
        }

        $postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $data );

        if ( 'attachment' === $postdata['post_type'] ) {
            if ( ! $this->options['fetch_attachments'] ) {
                $this->logger->notice( sprintf(
                    __( 'Skipping attachment "%s", fetching attachments disabled' ),
                    $data['post_title']
                ) );
                /**
                 * Post processing skipped.
                 *
                 * @param array $data Raw data imported for the post.
                 * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
                 */
                do_action( 'wxr_importer.process_skipped.post', $data, $meta );
                return false;
            }
            $remote_url = ! empty( $data['attachment_url'] ) ? $data['attachment_url'] : $data['guid'];
            $post_id = $this->process_attachment( $postdata, $meta, $remote_url );
        } else {
            $post_id = wp_insert_post( $postdata, true );
            do_action( 'wp_import_insert_post', $post_id, $original_id, $postdata, $data );
        }

        if ( is_wp_error( $post_id ) ) {
            $this->logger->error( sprintf(
                __( 'Failed to import "%s" (%s)', 'nk-themes-helper' ),
                $data['post_title'],
                $post_type_object->labels->singular_name
            ) );
            $this->logger->debug( $post_id->get_error_message() );

            /**
             * Post processing failed.
             *
             * @param WP_Error $post_id Error object.
             * @param array $data Raw data imported for the post.
             * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
             * @param array $comments Raw comment data, already processed by {@see process_comments}.
             * @param array $terms Raw term data, already processed.
             */
            do_action( 'wxr_importer.process_failed.post', $post_id, $data, $meta, $comments, $terms );
            return false;
        }

        // Ensure stickiness is handled correctly too
        if ( $data['is_sticky'] === '1' ) {
            stick_post( $post_id );
        }

        // map pre-import ID to local ID
        $this->mapping['post'][ $original_id ] = (int) $post_id;
        if ( $requires_remapping ) {
            $this->requires_remapping['post'][ $post_id ] = true;
        }
        $this->mark_post_exists( $data, $post_id );

        $this->logger->info( sprintf(
            __( 'Imported "%s" (%s)', 'nk-themes-helper' ),
            $data['post_title'],
            $post_type_object->labels->singular_name
        ) );
        $this->logger->debug( sprintf(
            __( 'Post %d remapped to %d', 'nk-themes-helper' ),
            $original_id,
            $post_id
        ) );

        // Handle the terms too
        $terms = apply_filters( 'wp_import_post_terms', $terms, $post_id, $data );

        if ( ! empty( $terms ) ) {
            $term_ids = array();
            foreach ( $terms as $term ) {
                $taxonomy = $term['taxonomy'];
                $key = sha1( $taxonomy . ':' . $term['slug'] );

                if ( isset( $this->mapping['term'][ $key ] ) ) {
                    $term_ids[ $taxonomy ][] = (int) $this->mapping['term'][ $key ];
                } else {
                    $meta[] = array( 'key' => '_wxr_import_term', 'value' => $term );
                    $requires_remapping = true;
                }
            }

            foreach ( $term_ids as $tax => $ids ) {
                $tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
                do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $data );
            }
        }

        $this->process_comments( $comments, $post_id, $data );
        $this->process_post_meta( $meta, $post_id, $data );

        if ( 'nav_menu_item' === $data['post_type'] ) {
            $this->process_menu_item_meta( $post_id, $data, $meta );
        }

        /**
         * Post processing completed.
         *
         * @param int $post_id New post ID.
         * @param array $data Raw data imported for the post.
         * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
         * @param array $comments Raw comment data, already processed by {@see process_comments}.
         * @param array $terms Raw term data, already processed.
         */
        do_action( 'wxr_importer.processed.post', $post_id, $data, $meta, $comments, $terms );
    }
    protected function process_comments( $comments, $post_id, $post, $post_exists = false ) {

        $comments = apply_filters( 'wp_import_post_comments', $comments, $post_id, $post );
        if ( empty( $comments ) ) {
            return 0;
        }

        $num_comments = 0;

        // Sort by ID to avoid excessive remapping later
        usort( $comments, array( $this, 'sort_comments_by_id' ) );

        foreach ( $comments as $key => $comment ) {
            /**
             * Pre-process comment data
             *
             * @param array $comment Comment data. (Return empty to skip.)
             * @param int $post_id Post the comment is attached to.
             */
            $comment = apply_filters( 'wxr_importer.pre_process.comment', $comment, $post_id );
            if ( empty( $comment ) ) {
                return false;
            }

            $original_id = isset( $comment['comment_id'] )      ? (int) $comment['comment_id']      : 0;
            $parent_id   = isset( $comment['comment_parent'] )  ? (int) $comment['comment_parent']  : 0;
            $author_id   = isset( $comment['comment_user_id'] ) ? (int) $comment['comment_user_id'] : 0;

            // if this is a new post we can skip the comment_exists() check
            // TODO: Check comment_exists for performance
            if ( $post_exists ) {
                $existing = $this->comment_exists( $comment );
                if ( $existing ) {
                    /**
                     * Comment processing already imported.
                     *
                     * @param array $comment Raw data imported for the comment.
                     */
                    do_action( 'wxr_importer.process_already_imported.comment', $comment );
                    $this->mapping['comment'][ $original_id ] = $exists;
                    continue;
                }
            }

            // Remove meta from the main array
            $meta = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
            unset( $comment['commentmeta'] );

            // Map the parent comment, or mark it as one we need to fix
            $requires_remapping = false;
            if ( $parent_id ) {
                if ( isset( $this->mapping['comment'][ $parent_id ] ) ) {
                    $comment['comment_parent'] = $this->mapping['comment'][ $parent_id ];
                } else {
                    // Prepare for remapping later
                    $meta[] = array( 'key' => '_wxr_import_parent', 'value' => $parent_id );
                    $requires_remapping = true;

                    // Wipe the parent for now
                    $comment['comment_parent'] = 0;
                }
            }

            // Map the author, or mark it as one we need to fix
            if ( $author_id ) {
                if ( isset( $this->mapping['user'][ $author_id ] ) ) {
                    $comment['user_id'] = $this->mapping['user'][ $author_id ];
                } else {
                    // Prepare for remapping later
                    $meta[] = array( 'key' => '_wxr_import_user', 'value' => $author_id );
                    $requires_remapping = true;

                    // Wipe the user for now
                    $comment['user_id'] = 0;
                }
            }

            // Run standard core filters
            $comment['comment_post_ID'] = $post_id;
            $comment = wp_filter_comment( $comment );

            // wp_insert_comment expects slashed data
            $comment_id = wp_insert_comment( wp_slash( $comment ) );
            $this->mapping['comment'][ $original_id ] = $comment_id;
            if ( $requires_remapping ) {
                $this->requires_remapping['comment'][ $comment_id ] = true;
            }
            $this->mark_comment_exists( $comment, $comment_id );

            /**
             * Comment has been imported.
             *
             * @param int $comment_id New comment ID
             * @param array $comment Comment inserted (`comment_id` item refers to the original ID)
             * @param int $post_id Post parent of the comment
             * @param array $post Post data
             */
            do_action( 'wp_import_insert_comment', $comment_id, $comment, $post_id, $post );

            // Process the meta items
            foreach ( $meta as $meta_item ) {
                $value = maybe_unserialize( $meta_item['value'] );
                add_comment_meta( $comment_id, wp_slash( $meta_item['key'] ), wp_slash( $value ) );
            }

            /**
             * Post processing completed.
             *
             * @param int $post_id New post ID.
             * @param array $comment Raw data imported for the comment.
             * @param array $meta Raw meta data, already processed by {@see process_post_meta}.
             * @param array $post_id Parent post ID.
             */
            do_action( 'wxr_importer.processed.comment', $comment_id, $comment, $meta, $post_id );

            $num_comments++;
        }

        return $num_comments;
    }
    protected function process_term( $data, $meta ) {
        /**
         * Pre-process term data.
         *
         * @param array $data Term data. (Return empty to skip.)
         * @param array $meta Meta data.
         */
        $data = apply_filters( 'wxr_importer.pre_process.term', $data, $meta );
        if ( empty( $data ) ) {
            return false;
        }

        $original_id = isset( $data['id'] )      ? (int) $data['id']      : 0;
        $parent_id   = isset( $data['parent'] )  ? (int) $data['parent']  : 0;

        $mapping_key = sha1( $data['taxonomy'] . ':' . $data['slug'] );
        $existing = $this->term_exists( $data );
        if ( $existing ) {
            /**
             * Term processing already imported.
             *
             * @param array $data Raw data imported for the term.
             */
            do_action( 'wxr_importer.process_already_imported.term', $data );

            $this->mapping['term'][ $mapping_key ] = $existing;
            $this->mapping['term_id'][ $original_id ] = $existing;
            return false;
        }

        // WP really likes to repeat itself in export files
        if ( isset( $this->mapping['term'][ $mapping_key ] ) ) {
            return false;
        }

        $termdata = array();
        $allowed = array(
            'slug' => true,
            'description' => true,
        );

        // Map the parent comment, or mark it as one we need to fix
        // TODO: add parent mapping and remapping
        /*$requires_remapping = false;
        if ( $parent_id ) {
            if ( isset( $this->mapping['term'][ $parent_id ] ) ) {
                $data['parent'] = $this->mapping['term'][ $parent_id ];
            } else {
                // Prepare for remapping later
                $meta[] = array( 'key' => '_wxr_import_parent', 'value' => $parent_id );
                $requires_remapping = true;

                // Wipe the parent for now
                $data['parent'] = 0;
            }
        }*/

        foreach ( $data as $key => $value ) {
            if ( ! isset( $allowed[ $key ] ) ) {
                continue;
            }

            $termdata[ $key ] = $data[ $key ];
        }

        $result = wp_insert_term( $data['name'], $data['taxonomy'], $termdata );
        if ( is_wp_error( $result ) ) {
            $this->logger->warning( sprintf(
                __( 'Failed to import %s %s', 'nk-themes-helper' ),
                $data['taxonomy'],
                $data['name']
            ) );
            $this->logger->debug( $result->get_error_message() );
            do_action( 'wp_import_insert_term_failed', $result, $data );

            /**
             * Term processing failed.
             *
             * @param WP_Error $result Error object.
             * @param array $data Raw data imported for the term.
             * @param array $meta Meta data supplied for the term.
             */
            do_action( 'wxr_importer.process_failed.term', $result, $data, $meta );
            return false;
        }

        $term_id = $result['term_id'];

        $this->mapping['term'][ $mapping_key ] = $term_id;
        $this->mapping['term_id'][ $original_id ] = $term_id;

        $this->logger->info( sprintf(
            __( 'Imported "%s" (%s)', 'nk-themes-helper' ),
            $data['name'],
            $data['taxonomy']
        ) );
        $this->logger->debug( sprintf(
            __( 'Term %d remapped to %d', 'nk-themes-helper' ),
            $original_id,
            $term_id
        ) );

        do_action( 'wp_import_insert_term', $term_id, $data );

        /**
         * Term processing completed.
         *
         * @param int $term_id New term ID.
         * @param array $data Raw data imported for the term.
         */
        do_action( 'wxr_importer.processed.term', $term_id, $data );
    }

    /**
     * Added to http_request_timeout filter to force timeout at 60 seconds during import
     *
     * @return int
     */
    function bump_request_timeout ($val) {
        return 300;
    }

    // public function of post_exists
    public function post_exists_pub ($data) {
        return $this->post_exists($data);
    }
}
