<?php
/**
 * Controller for shortcode "Tabulka společností".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 * 
 * @link https://www.gavick.com/blog/wordpress-tinymce-custom-buttons
 * @todo Labels in TinyMCE button should be localized properly!
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


if ( ! function_exists( 'odwpcfs_add_shortcode_1' ) ) :
    /**
     * Register shortcode "Tabulka společností".
     * @global wpdb $wpdb
     * @param array $atts
     * @param string $content (Optional.)
     * @return string
     */
    function odwpcfs_add_shortcode_1( $atts, $content = null ) {
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;

        // Collect attributes
        $attrs = shortcode_atts( array(
            'posts_per_page' => 5,
            'title' => __( 'Tabulka společností', ODWPCFS_SLUG ),
            'show_filter' => 1,
            'show_title' => 0,
        ), $atts );

        // ...
        // Firstly we need query all companies than we make another query with pagination
        $query_args = array(
            'offset' => 0,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => ODWPCFS_CPT,
            'post_status' => 'publish',
            'numberposts' => -1,
        );

        // Is filter used?
        if ( isset( $_POST['odwpcfs-filter-submit'] ) ) {
            $sql        = "SELECT * FROM $table_name WHERE 1 ORDER BY `name`, `type` ";
            $filters    = $wpdb->get_results( $sql, OBJECT_K );
            $meta_query = array();

            foreach ( $_POST as $key => $val ) {
                if ( strpos( 'odwpcfs-filter-', $key ) != 0 ) {
                    continue; // It is not a filter value, skip it.
                }

                // Get filter id and the filter
                $filter_id = (int) str_replace( 'odwpcfs-filter-', '', $key );
                $filter    = ( array_key_exists( $filter_id, $filters ) ) ? $filters[$filter_id] : null;
                if ( is_null( $filter ) ) {
                    continue; // Skip filter because it doesn't exist in database
                }

                // Prepare meta name and value
                $meta_key = sprintf( 'odwpcfs-metabox-5-%d', $filter_id );
                
                if ( (int) $filter->type == 0 && $val == 'on' ) {
                    $meta_query[] = array( 'key' => $meta_key, 'value' => '1' );
                }
                elseif ( (int) $filter->type == 1 ) {
                    if ( is_array( $val ) ) {
                        foreach ( $val as $v ) {
                            $meta_query[] = array( 'key' => $meta_key, 'value' => $v );
                        }
                    } else {
                        $meta_query[] = array( 'key' => $meta_key, 'value' => $val );
                    }
                }
                elseif ( (int) $filter->type == 2 ) {
                    continue; // We doesn't use text filters right now
                }
            }

            $query_args['meta_query'] = $meta_query;
        }
        elseif ( isset( $_GET['ocptpf'] ) ) {
            $json = str_replace( "\\", '', $_GET['ocptpf'] );
            $meta_query = json_decode( $json, true );

            if ( is_array( $meta_query ) ) {
                $query_args['meta_query'] = $meta_query;
            }
        }

        // Pagination
        $total_count    = count( get_posts( $query_args ) );
        $posts_per_page = intval( $attrs['posts_per_page'] );
        $total_pages    = (int) ceil( $total_count / $posts_per_page );
        $current_page   = (int) filter_input( INPUT_GET, 'ocptp', FILTER_DEFAULT, FILTER_SANITIZE_NUMBER_INT );
        $current_page   = $current_page == 0 ? 1 : $current_page;
        $current_offset = ( $current_page == 1 ) ? 0 : ( ( $current_page - 1 ) * $posts_per_page );

        // Now query with pagination
        unset( $query_args['numberposts'] );
        $query_args['offset'] = $current_offset;
        $query_args['posts_per_page'] = $posts_per_page;

        // Get all companies
        $companies = get_posts( $query_args );

        // This is filter for URLs of pagination
        $filter_url_part = '';
        if ( array_key_exists( 'meta_query', $query_args ) ) {
            $filter_url_part = urlencode( json_encode( $query_args['meta_query'] ) );
        }

        // Render template
        ob_start( function() {} );
        include_once( ODWPCFS_PATH . '/templates/shortcode-1.phtml' );
        $html = ob_get_flush();
        return apply_filters( 'odwpcfs-shortcode-1', $html );
    }
endif;
add_shortcode( 'tabulka-spolecnosti', 'odwpcfs_add_shortcode_1' );


// Functions below are for TinyMCE button for this shortcode:

if ( ! function_exists( 'odwpcfs_add_tinymce_btn_shortcode_1' ) ) :
/**
 * Enable easy access to our shortcode via TinyMCE button.
 * @global string $typenow
 */
function odwpcfs_add_tinymce_btn_shortcode_1() {
    global $typenow;

    if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
        return;
    }
    // verify the post type
    if ( ! in_array( $typenow, array( 'post', 'page' ) ) ) {
        return;
    }
    // check if WYSIWYG is enabled
    if ( get_user_option( 'rich_editing' ) == 'true' ) {
        add_filter( 'mce_external_plugins', 'odwpcfs_register_tinymce_plugin_shortcode_1' );
        add_filter( 'mce_buttons', 'odwpcfs_register_tinymce_btn_shortcode_1' );
    }
}
endif;
add_action( 'admin_head', 'odwpcfs_add_tinymce_btn_shortcode_1' );


if ( ! function_exists( 'odwpcfs_register_tinymce_btn_shortcode_1' ) ) :
    /**
     * Register new TinyMCE buttons.
     * @param array $buttons
     * @return array
     */
    function odwpcfs_register_tinymce_btn_shortcode_1( $buttons ) {
       array_push( $buttons, 'separator', 'odwpcfs_shortcode_1' );
       return $buttons;
    }
endif;


if ( ! function_exists( 'odwpcfs_register_tinymce_plugin_shortcode_1' ) ) :
    /**
     * Register our TinyMCE plugin.
     * @param array $plugins
     * @return array
     */
    function odwpcfs_register_tinymce_plugin_shortcode_1( $plugins ) {
        $plugins['odwpcfs_shortcode_1'] = plugins_url( '/js/tinymce-plugin-shortcode-1.js', ODWPCFS_FILE );
        return $plugins;
    }
endif;
