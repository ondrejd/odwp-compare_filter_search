<?php
/**
 * Controller file for the fourth metabox "Odkaz na registraci".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


if ( ! function_exists( 'odwpcfs_show_metabox_4' ) ) :
    /**
     * Render metabox "odkaz na registraci".
     * @global WP_Post $post
     */
    function odwpcfs_show_metabox_4() {
        global $post;
        $val = get_post_meta( $post->ID, 'odwpcfs-metabox-4', true );
        ob_start( function() {} );
        include_once ODWPCFS_PATH . '/templates/metabox-4.phtml';
        $html = ob_get_flush();
        echo apply_filters( 'odwpcfs-metabox-4', $html );
    }
endif;


if ( ! function_exists( 'odwpcfs_save_metabox_4' ) ) :
    /**
     * Save metabox "odkaz na registraci".
     * @global WP_Post $post
     * @param integer $post_id
     * @todo Use `filter_input` instead of `$_POST`!
     */
    function odwpcfs_save_metabox_4( $post_id ) {
        global $post;

        if ( 
            ! isset( $_POST['odwpcfs-metabox-4-nonce'] ) || 
            ! wp_verify_nonce( $_POST['odwpcfs-metabox-4-nonce'], 'odwpcfs-mb4-nonce' ) 
        ) {
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

        $value = '';

        if( isset( $_POST['odwpcfs-metabox-4-input'] ) ) {
            $value = $_POST['odwpcfs-metabox-4-input'];
        }   
        update_post_meta( $post_id, 'odwpcfs-metabox-4', $value );
    }
endif;

