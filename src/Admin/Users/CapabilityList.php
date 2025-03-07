<?php
/**
 * The file that manage the user capabilities list page.
 *
 * @link       https://getskilltriks.com
 * @since      1.0.0
 *
 * @package    ST\Lms\Admin\Users
 */

namespace ST\Lms\Admin\Users;

/**
 * Declare Class `CapabilityList`
 */
class CapabilityList extends \ST\Lms\Admin\Users\Capability {

	/**
	 * Prepare items
	 */
	public function prepare_items() {
		$search_by_name = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : ''; // phpcs:ignore
		$this->items    = $this->get_roles( $search_by_name );
		$per_page       = $this->per_page;
		$total_item     = 0;
		$this->set_pagination_args(
			array(
				'total_items' => $total_item,
				'per_page'    => (int) $per_page,
				'total_pages' => (int) ceil( $total_item / $per_page ),
			)
		);

		$columns               = $this->get_columns();
		$this->_column_headers = array( $columns, array() );
		$this->views();
	}

	/**
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a `column_cb()` method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information.
	 */
	public function get_columns() {
		$columns = array(
			'cb'   => '<input type="checkbox" />',
			'name' => __( 'Role Name', 'skilltriks-lms' ),
		);
		return $columns;
	}

	/**
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param object $item        A singular item (one full row's worth of data).
	 * @param string $column_name The name/slug of the column to be processed.
	 * @return string Text or HTML to be placed inside the column <td>.
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
				return $item->$column_name;
			default:
				return __( 'No Data Found', 'skilltriks-lms' );
		}
	}

	/**
	 * View Record button.
	 *
	 * @param string $item email.
	 * @return string Field and action
	 */
	public function column_name( $item ) {
		$action = array(
			'edit'   => sprintf(
				'<a href="%s">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'page' => 'stlms_manage_roles',
							'role' => sanitize_title( $item ),
						),
						admin_url( 'admin.php' )
					)
				),
				esc_html__( 'Edit', 'skilltriks-lms' )
			),
			'delete' => sprintf(
				'<a href="%s" onclick="return confirm(\'%s\');">%s</a>',
				esc_url(
					add_query_arg(
						array(
							'page'   => 'stlms_manage_roles',
							'action' => 'delete_role',
							'id'     => sanitize_title( $item ),
						),
						admin_url( 'admin.php' )
					)
				),
				esc_html__( 'Are you sure you want to delete this role?', 'skilltriks-lms' ),
				esc_html__( 'Delete', 'skilltriks-lms' )
			),
		);
		return sprintf( '%1$s %2$s', $item, $this->row_actions( $action ) );
	}

	/**
	 * Get value for checkbox column.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="role_ids[]" value="%s" />', (string) $item );
	}

	/**
	 * No roles found.
	 */
	public function no_items() {
		esc_html_e( 'No roles found.', 'skilltriks-lms' );
	}

	/**
	 * Prints column headers, accounting for hidden and sortable columns.
	 *
	 * @since 3.1.0
	 *
	 * @param bool $with_id Whether to set the ID attribute or not.
	 */
	public function print_column_headers( $with_id = true ) {
		global $wp;
		list( $columns, $hidden, $primary ) = $this->get_column_info();

		if ( ! empty( $columns['cb'] ) ) {
			static $cb_counter = 1;
			$columns['cb']     = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' .
				/* translators: Hidden accessibility text. */
				__( 'Select All', 'skilltriks-lms' ) .
			'</label>' .
			'<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
			++$cb_counter;
		}

		foreach ( $columns as $column_key => $column_display_name ) {
			$class = array( 'manage-column', "column-$column_key" );

			if ( in_array( $column_key, $hidden, true ) ) {
				$class[] = 'hidden';
			}

			if ( 'cb' === $column_key ) {
				$class[] = 'check-column';
			} elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ), true ) ) {
				$class[] = 'num';
			}

			if ( $column_key === $primary ) {
				$class[] = 'column-primary';
			}

			$tag   = ( 'cb' === $column_key ) ? 'td' : 'th';
			$scope = ( 'th' === $tag ) ? 'scope="col"' : '';
			$id    = $with_id ? "id='$column_key'" : '';
			$class = "class='" . implode( ' ', $class ) . "'";

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo "<$tag $scope $id $class>$column_display_name</$tag>";
		}
	}
}
