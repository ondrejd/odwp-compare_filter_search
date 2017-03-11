<?php
/**
 * Plugin Name: odwp-compare_filter_search
 * Plugin URI: https://bitbucket.org/ondrejd/odwp-compare_filter_search
 * Description: 
 * Version: 1.0.0
 * Author: Ondřej Doněk
 * Author URI: 
 * License: GPLv3
 * Requires at least: 4.6
 * Tested up to: 4.7.3
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


defined( 'ODWPCFS_SLUG' ) || define( 'ODWPCFS_SLUG', 'odwp-compare_filter_search' );
defined( 'ODWPCFS_FILE' ) || define( 'ODWPCFS_FILE', __FILE__ );
defined( 'ODWPCFS_PATH' ) || define( 'ODWPCFS_PATH', dirname( ODWPCFS_FILE ) );
defined( 'ODWPCFS_CPT' ) || define( 'ODWPCFS_CPT', 'odwpcfs_cpt' );
defined( 'ODWPCFS_DBTABLE' ) || define( 'ODWPCFS_DBTABLE', 'odwpcfs_filters' );


if ( ! function_exists( 'odwpcfs_custom_post_type' ) ) :
    /**
     * Register our custom post type.
     */
    function odwpcfs_custom_post_type() {
        $labels = array(
            'name'                  => _x( 'Společnosti', 'Post Type General Name', ODWPCFS_SLUG ),
            'singular_name'         => _x( 'Společnost', 'Post Type Singular Name', ODWPCFS_SLUG ),
            'menu_name'             => __( 'Společnosti', ODWPCFS_SLUG ),
            'name_admin_bar'        => __( 'Společnost', ODWPCFS_SLUG ),
            'archives'              => __( 'Archív společností', ODWPCFS_SLUG ),
            'attributes'            => __( 'Atributy společnosti', ODWPCFS_SLUG ),
            'parent_item_colon'     => __( 'Nadřazená společnost:', ODWPCFS_SLUG ),
            'all_items'             => __( 'Všechny společnosti', ODWPCFS_SLUG ),
            'add_new_item'          => __( 'Přidej novou společnost', ODWPCFS_SLUG ),
            'add_new'               => __( 'Přidej novou', ODWPCFS_SLUG ),
            'new_item'              => __( 'Nová společnost', ODWPCFS_SLUG ),
            'edit_item'             => __( 'Uprav společnost', ODWPCFS_SLUG ),
            'update_item'           => __( 'Aktualizuj společnost', ODWPCFS_SLUG ),
            'view_item'             => __( 'Zobraz společnost', ODWPCFS_SLUG ),
            'view_items'            => __( 'Zobraz společnosti', ODWPCFS_SLUG ),
            /*'search_items'          => __( 'Search Item', ODWPCFS_SLUG ),
            'not_found'             => __( 'Not found', ODWPCFS_SLUG ),
            'not_found_in_trash'    => __( 'Not found in Trash', ODWPCFS_SLUG ),
            'featured_image'        => __( 'Featured Image', ODWPCFS_SLUG ),
            'set_featured_image'    => __( 'Set featured image', ODWPCFS_SLUG ),
            'remove_featured_image' => __( 'Remove featured image', ODWPCFS_SLUG ),
            'use_featured_image'    => __( 'Use as featured image', ODWPCFS_SLUG ),
            'insert_into_item'      => __( 'Insert into item', ODWPCFS_SLUG ),
            'uploaded_to_this_item' => __( 'Uploaded to this item', ODWPCFS_SLUG ),
            'items_list'            => __( 'Items list', ODWPCFS_SLUG ),
            'items_list_navigation' => __( 'Items list navigation', ODWPCFS_SLUG ),
            'filter_items_list'     => __( 'Filter items list', ODWPCFS_SLUG ),*/
        );
        $args = array(
            'label'                 => __( 'Společnost', ODWPCFS_SLUG ),
            'description'           => __( '...', ODWPCFS_SLUG ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'excerpt'/*, 'custom-fields'*/, 'thumbnail', 'post-formats' ),
            'taxonomies'            => array( /*'category', 'post_tag'*/ ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,        
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );
        register_post_type( ODWPCFS_CPT, $args );
    }
endif;
add_action( 'init', 'odwpcfs_custom_post_type', 0 );


/**
 * @var array $odwpcfs_metaboxes
 */
$odwpcfs_metaboxes = array(
    0 => __( 'Hodnocení', ODWPCFS_SLUG ),
    1 => __( 'Vstupní bonus', ODWPCFS_SLUG ),
    2 => __( 'Odkaz na recenzi', ODWPCFS_SLUG ),
    3 => __( 'Odkaz na registraci', ODWPCFS_SLUG ),
    4 => __( 'Vlastnosti', ODWPCFS_SLUG ),
);


if ( !function_exists( 'odwpcfs_metaboxes' ) ) :
    /**
     * Meta boxes for our `odwpcfs_cpt` custom post type.
     * @global array $odwpcfs_metaboxes
     */
    function odwpcfs_metaboxes() {
        global $odwpcfs_metaboxes;
        
        for( $i = 1; $i <= count( $odwpcfs_metaboxes ); $i++ ) {
            add_meta_box(
               sprintf( 'odwpcfs-metabox-%d', $i ),
               $odwpcfs_metaboxes[$i - 1],
               sprintf( 'odwpcfs_show_metabox_%d', $i ),
               ODWPCFS_CPT,
               'normal',
               'high'
            );
        }
    }
endif;


if ( !function_exists( 'odwpcfs_add_metaboxes' ) ) :
    /**
     * Add our meta boxes.
     * @global array $odwpcfs_metaboxes
     */
    function odwpcfs_add_metaboxes() {
        global $odwpcfs_metaboxes;
        
        add_action( 'add_meta_boxes', 'odwpcfs_metaboxes' );
        
        for( $i = 1; $i <= count( $odwpcfs_metaboxes ); $i++ ) {
            add_action( 'save_post', sprintf( 'odwpcfs_save_metabox_%d', $i ), 10, 3 );
        }
    }
endif;

if ( is_admin() === true ) {
    // Include all meta boxes
    include_once( ODWPCFS_PATH . '/src/metabox-1.php' );
    include_once( ODWPCFS_PATH . '/src/metabox-2.php' );
    include_once( ODWPCFS_PATH . '/src/metabox-3.php' );
    include_once( ODWPCFS_PATH . '/src/metabox-4.php' );
    include_once( ODWPCFS_PATH . '/src/metabox-5.php' );
    // Load our meta boxes only on edit post page.
    add_action( 'load-post.php', 'odwpcfs_add_metaboxes' );
    add_action( 'load-post-new.php', 'odwpcfs_add_metaboxes' );
}


if ( !function_exists( 'odwpcfs_get_thumbnail' ) ) :
    /**
     * Add new column in "odwpcfs-cpt" list in WP admin.
     */
    function odwpcfs_get_thumbnail( $post_id ) {
        $post_thumbnail_id = get_post_thumbnail_id( $post_id );
        if ( $post_thumbnail_id ) {
            $post_thumbnail_img = wp_get_attachment_image_src( $post_thumbnail_id/*, 'featured_preview'*/ );
            return $post_thumbnail_img[0];
        }
    }
endif;


if ( !function_exists( 'odwpcfs_cpt_columns' ) ) :
    /**
     * Add new columns in "odwpcfs-cpt" list in WP admin.
     * @global string $post_type
     * @param array $defaults
     * @return array
     */
    function odwpcfs_cpt_columns( $defaults ) {
        global $post_type;

        if ( $post_type != ODWPCFS_CPT ) {
            return $defaults;
        }

        $defaults['odwpcfs_logo_column'] = __( 'Logo', ODWPCFS_SLUG );
        $defaults['odwpcfs_rating_column'] = __( 'Hodnocení', ODWPCFS_SLUG );
        $defaults['odwpcfs_bonus_column'] = __( 'Vstupní bonus', ODWPCFS_SLUG );
        $defaults['odwpcfs_review_column'] = sprintf( '<abbr title="%s">%s</abbr>', __( 'Recenze', ODWPCFS_SLUG ), __( 'R', ODWPCFS_SLUG) );

        return $defaults;
    }
endif;
add_filter( 'manage_posts_columns', 'odwpcfs_cpt_columns' );
 

if ( !function_exists( 'odwpcfs_cpt_columns_content' ) ) :
    /**
     * Render content for logo cell in "odwpcfs-cpt" list in WP admin.
     * @global string $post_type
     */
    function odwpcfs_cpt_columns_content( $column_name, $post_id ) {
        if ( $column_name == 'odwpcfs_logo_column' ) {
            $post_featured_image = odwpcfs_get_thumbnail( $post_id );
            if ( $post_featured_image) {
                printf( '<img src="%s">', $post_featured_image );
            }
        }
        elseif ( $column_name == 'odwpcfs_rating_column' ) {
            $val = get_post_meta( $post_id, 'odwpcfs-metabox-1', true );
            printf( '<div class="star-ratings-sprite">' .
                    '<span style="width:%d%%" class="star-ratings-sprite-rating"></span>' .
                    '</div>', $val );
        }
        elseif ( $column_name == 'odwpcfs_bonus_column' ) {
            $val = get_post_meta( $post_id, 'odwpcfs-metabox-2', true );
            printf( '<span>%s</span>', $val );
        }
        elseif ( $column_name == 'odwpcfs_review_column' ) {
            $post_id = (int) get_post_meta( $post_id, 'odwpcfs-metabox-3', true );

            if ( $post_id == 0 ) {
                echo '<span class="dashicons dashicons-format-aside without_review"></span>';
            } else {
                printf( '<a href="%s"><span class="dashicons dashicons-format-aside"></span></a>', get_page_link( $post_id ) );
            }
        }
    }
endif;
add_action( 'manage_posts_custom_column', 'odwpcfs_cpt_columns_content', 10, 2 );


// ===========================================================================
// ===========================================================================
// ===========================================================================
if ( !function_exists( 'odwpcfs_update_cpt_quicklinks' ) ) :
/**
 * Updates quicklinks in table list page of our custom post type.
 * @global string $post_type
 * @global WP_Query $wp_query
 * @param array $views
 * @return array
 * @todo Calculate correct count of companies without review link.
 */
function odwpcfs_update_cpt_quicklinks( $views ) {
    global $post_type;
    global $wp_query;

    if ( ! is_admin() || $post_type != ODWPCFS_CPT ) {
        return;
    }

    $result = new WP_Query( array(
        'post_type'   => ODWPCFS_CPT,
        'meta_query'  => array(
            array( 'key' => 'odwpcfs-metabox-3', 'value' => '0' ),
        )
    ) );

    $link_url = home_url(
            add_query_arg( array( 'post_type' => ODWPCFS_CPT, 'post_status' => 'without_review' ) ),
            add_query_arg( null, null )
    );

    $post_status = filter_input( INPUT_GET, 'post_status' );
    $current     = '';
    if ( $post_status == 'without_review' ) {
        $current = ' class="current"';
    }

    $views['without_review'] = sprintf(
            '<a href="%s"' . $current . '>%s <span class="count">(%d)</span></a>',
            $link_url,
            __( 'Bez recenze', ODWPCFS_SLUG ),
            $result->found_posts
    );

    return $views;
}
endif;
add_filter( 'views_edit-' . ODWPCFS_CPT, 'odwpcfs_update_cpt_quicklinks' );


if ( ! function_exists( 'odwpcfs_limit_cpt_to_without_review' ) ) :
/**
 * @global string $pagenow
 * @global string $post_type
 * @param WP_Query $query
 * @return WP_Query
 */
function odwpcfs_limit_cpt_to_without_review( $query ) {
    global $pagenow;
    global $post_type;

    if ( 'edit.php' != $pagenow || ! $query->is_admin || $post_type != ODWPCFS_CPT ) {
        return $query;
    }

    $post_status = filter_input( INPUT_GET, 'post_status' );
    if ( $post_status == 'without_review' ) {
        $query->set( 'meta_query', array( array( 'key' => 'odwpcfs-metabox-3', 'value' => '0' ) ) );
    }

    return $query;
}
endif;
add_filter('pre_get_posts', 'odwpcfs_limit_cpt_to_without_review');


if ( !function_exists( 'odwpcfs_admin_scripts' ) ) :
    /**
     * Append our CSS styles and Javascripts for the WP admin.
     */
    function odwpcfs_admin_scripts() {
        wp_enqueue_style( 'odwpcfs-admin-css', plugins_url( '/css/admin.css', ODWPCFS_FILE ) );
        wp_enqueue_script( 'odwpcfs-admin-js', plugins_url( '/js/admin.js', ODWPCFS_FILE ) );
    }
endif;
add_action( 'admin_enqueue_scripts', 'odwpcfs_admin_scripts' );


if ( !function_exists( 'odwpcfs_public_scripts' ) ) :
    /**
     * Append our CSS styles and JavaScripts for the front-end.
     */
    function odwpcfs_public_scripts() {
        wp_enqueue_style( 'odwpcfs-admin-css', plugins_url( '/css/public.css', ODWPCFS_FILE ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'odwpcfs_public_scripts' );


if ( !function_exists( 'odwpcfs_admin_menu' ) ) :
    /**
     * Add settings pages for our custom post type.
     */
    function odwpcfs_admin_menu() {
        add_submenu_page(
                'edit.php?post_type=' . ODWPCFS_CPT,
                __( 'Dostupné filtry', ODWPCFS_SLUG ),
                __( 'Filtry', ODWPCFS_SLUG ),
                'edit_posts',
                'odwpcfs_settings_filters_page',
                'odwpcfs_settings_filters_page_render'
        );
        add_submenu_page(
                'edit.php?post_type=' . ODWPCFS_CPT,
                __( 'Nastavení pluginu', ODWPCFS_SLUG ),
                __( 'Nastavení', ODWPCFS_SLUG ),
                'edit_posts',
                'odwpcfs_settings_page',
                'odwpcfs_settings_page_render'
        );
    }
endif;

if ( is_admin() === true ) {
    // Include source file for settings pages.
    include_once( ODWPCFS_PATH . '/src/settings.php' );
    include_once( ODWPCFS_PATH . '/src/settings-filters.php' );
    // Update WP_Admin menu
    add_action( 'admin_menu' , 'odwpcfs_admin_menu' );
}


if ( ! function_exists( 'odwpcfs_activate_plugin' ) ) :
    /**
     * Activate plugin (create our database table).
     * @global wpdb $wpdb
     * @link https://premium.wpmudev.org/blog/creating-database-tables-for-plugins/
     */
    function odwpcfs_activate_plugin() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(55) NOT NULL,
            type smallint(2) NOT NULL DEFAULT 0 ,
            description tinytext DEFAULT NULL,
            extra tinytext DEFAULT NULL,
            UNIQUE KEY id (id),
            UNIQUE KEY name (name)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
endif;
register_activation_hook( ODWPCFS_FILE, 'odwpcfs_activate_plugin' );


if ( !function_exists( 'odwpcfs_filter_type_name' ) ) :
    /**
     * Returns correct name for filter type.
     * @param integer $type
     * @return string
     */
    function odwpcfs_filter_type_name( $type ) {
        switch( (int) $type ) {
            case 0 : return __( 'Zaškrtávací pole', ODWPCFS_SLUG );
            case 1 : return __( 'Prostý text', ODWPCFS_SLUG );
            case 2 : return __( 'Výběr z možností', ODWPCFS_SLUG );
            default: return __( 'Neznámý', ODWPCFS_SLUG );
        }
    }
endif;


if ( !function_exists( 'odwpcfs_get_filter_by_id' ) ) :
    /**
     * Returns filter by its ID.
     * @global wpdb $wpdb
     * @link https://codex.wordpress.org/Class_Reference/wpdb#SELECT_a_Row
     * @param integer $id
     * @return object
     */
    function odwpcfs_get_filter_by_id( $id ) {
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;
        $sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d ", $id );

        return $wpdb->get_row( $sql );
    }
endif;


// Include shortcode "Tabulka společností"
include_once( ODWPCFS_PATH . '/src/shortcode-1.php' );
// Include shortcode "Přehled vlastností společnosti"
include_once( ODWPCFS_PATH . '/src/shortcode-2.php' );
// Include widget "Filtr společností"
include_once( ODWPCFS_PATH . '/src/widget-1.php' );
