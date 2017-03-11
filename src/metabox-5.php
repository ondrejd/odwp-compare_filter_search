<?php
/**
 * Controller file for the fifth metabox "Vlastnosti".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


if ( ! function_exists( 'odwpcfs_show_metabox_5' ) ) :
    /**
     * Render metabox "hodnoceni".
     * @global WP_Post $post
     * @global wpdb $wpdb
     * @link https://codex.wordpress.org/Class_Reference/wpdb#SELECT_Generic_Results
     */
    function odwpcfs_show_metabox_5() {
        global $post;
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;

        // Get all available filters
        $filters = $wpdb->get_results( "SELECT * FROM $table_name WHERE 1 ORDER BY `name`, `type` " );
        
        // Get all values for them
        $filter_vals = array();
        foreach ( $filters as $filter ) {
            $meta_name = sprintf( 'odwpcfs-metabox-5-%d', $filter->id );
            $filter_vals[$filter->id] = get_post_meta( $post->ID, $meta_name, true );
        }
        
        // Render template
        ob_start( function() {} );
        include_once ODWPCFS_PATH . '/templates/metabox-5.phtml';
        $html = ob_get_flush();
        echo apply_filters( 'odwpcfs-metabox-5', $html );
    }
endif;


if ( ! function_exists( 'odwpcfs_save_metabox_5' ) ) :
    /**
     * Save metabox "hodnoceni".
     * @global WP_Post $post
     * @global wpdb $wpdb
     * @param integer $post_id
     * @return $post_id
     * @link https://codex.wordpress.org/Class_Reference/wpdb#SELECT_Generic_Results
     */
    function odwpcfs_save_metabox_5( $post_id ) {
        global $post;
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;

        $nonce = filter_input( INPUT_POST, 'odwpcfs-metabox-5-nonce' );
        if ( ! wp_verify_nonce( $nonce, 'odwpcfs-mb5-nonce' ) ) {
            return $post_id;
        }

        if( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        if( ODWPCFS_CPT != $post->post_type) {
            return $post_id;
        }

        $filters = $wpdb->get_results( "SELECT * FROM $table_name WHERE 1 ORDER BY `name`, `type` ", OBJECT_K );
        $values  = filter_input( INPUT_POST, 'odwpcfs-metabox-5-input', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

        foreach ( $filters as $filter_id => $filter ) {
            $meta_name = sprintf( 'odwpcfs-metabox-5-%d', $filter_id );
            $meta_val  = '';

            if ( array_key_exists( $filter_id, $values ) ) {
                $meta_val = (int) $filter->type == 0 ? true : $values[$filter_id];
            } else {
                $meta_val = (int) $filter->type == 0 ? false : '';
            }

            update_post_meta( $post_id, $meta_name, $meta_val );
        }

        return $post_id;
    }
endif;

