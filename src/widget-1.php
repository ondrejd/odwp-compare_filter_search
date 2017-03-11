<?php
/**
 * Class for widget "Filtr společností".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * Widget "Filtr společností".
 */
class odwpcfs_filter_widget extends WP_Widget {
    /**
     * Constructor.
     */
    public function __construct() {
        $opts = array(
            'classname' => 'odwpcfs_filter_widget',
            'description' => __( 'Widget s filtrem společností', ODWPCFS_SLUG ),
        );
        parent::__construct( 'odwpcfs_filter_widget', __( 'Filtr společností', ODWPCFS_SLUG ), $opts );
    }

    /**
     * Renders the widget.
     * @global wpdb $wpdb
     * @param array $args
     * @param array $instance
     * @link https://codex.wordpress.org/Class_Reference/wpdb#SELECT_Generic_Results
     * @todo Use `filter_input` instead direct access of `$_POST` or `$_GET`!
     */
    public function widget( $args, $instance ) {
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;

        // Get all available filters
        $filters = $wpdb->get_results( "SELECT * FROM $table_name WHERE 1 ORDER BY `name`, `type` ", OBJECT_K );
        $filters_val = array();

        if ( isset( $_POST['odwpcfs-filter-submit'] ) ) {
            foreach ( $_POST as $key => $val ) {
                if ( strpos( 'odwpcfs-filter-', $key ) != 0 ) {
                    continue; // It is not a filter value, skip it.
                }

                $filter_id  = (int) str_replace( 'odwpcfs-filter-', '', $key );
                $filter_val = $val;
                $filter     = array_key_exists( $filter_id, $filters ) ? $filters[$filter_id] : null;

                if ( is_null( $filter ) ) {
                    continue;
                }

                if ( (int) $filter->type == 2 && ! is_array( $filter_val ) ) {
                    $filter_val = array( $filter_val );
                }

                $filters_val[$filter_id] = $filter_val;
            }
        }
        elseif ( isset( $_GET['ocptpf'] ) ) {
            $json = str_replace( "\\", '', $_GET['ocptpf'] );
            $data = json_decode( $json, true );
echo '<pre>';var_dump($data);
            foreach ( $data as $data_item ) {
                $filter_id  = (int) str_replace( 'odwpcfs-metabox-5-', '', $data_item['key'] );
                $filter_val = $data_item['value'];
                $filter     = array_key_exists( $filter_id, $filters ) ? $filters[$filter_id] : null;

                if ( is_null( $filter ) ) {
                    continue;
                }

                if ( (int) $filter->type == 0 ) {
                    $filter_val = (int) $filter_val == 1 ? 'on' : 'off';
                }
                elseif ( (int) $filter->type == 1 ) {
                    $filter_val = $filter_val;
                }
                if ( (int) $filter->type == 2 && ! is_array( $filter_val ) ) {
                    $filter_val = array( $filter_val );
                }

                $filters_val[$filter_id] = $filter_val;
            }
        }

        // Render template
        ob_start( function() {} );
        include_once( ODWPCFS_PATH . '/templates/widget-1.phtml' );
        $html = ob_get_flush();
        echo apply_filters( 'odwpcfs-widget-1', $html );
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
    }
}

// Register our widget
add_action( 'widgets_init', function() { register_widget( 'odwpcfs_filter_widget' ); } );
