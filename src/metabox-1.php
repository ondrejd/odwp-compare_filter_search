<?php
/**
 * Controller file for the first metabox "Hodnocení".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


if ( ! function_exists( 'odwpcfs_show_metabox_1' ) ) :
    /**
     * Render metabox "hodnoceni".
     * @global WP_Post $post
     */
    function odwpcfs_show_metabox_1() {
        global $post;
        $val = get_post_meta( $post->ID, 'odwpcfs-metabox-1', true );
        ob_start( function() {} );
        include_once ODWPCFS_PATH . '/templates/metabox-1.phtml';
        $html = ob_get_flush();
        echo apply_filters( 'odwpcfs-metabox-1', $html );
    }
endif;


if ( ! function_exists( 'odwpcfs_save_metabox_1' ) ) :
    /**
     * Save metabox "hodnoceni".
     * @global WP_Post $post
     * @param integer $post_id
     * @return integer
     */
    function odwpcfs_save_metabox_1( $post_id ) {
        global $post;

        $nonce = filter_input( INPUT_POST, 'odwpcfs-metabox-1-nonce' );
        if ( ! wp_verify_nonce( $nonce, 'odwpcfs-mb1-nonce' ) ) {
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

        $value = filter_input( INPUT_POST, 'odwpcfs-metabox-1-input' );
        if ( is_null( $value ) ) {
            $value = 0;
        }

        update_post_meta( $post_id, 'odwpcfs-metabox-1', $value );

        return $post_id;
    }
endif;

