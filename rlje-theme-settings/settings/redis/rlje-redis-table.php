<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/screen.php' );
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

// https://plugins.svn.wordpress.org/custom-list-table-example/trunk/list-table-example.php
class RLJE_Redis_Table extends WP_List_Table {

	protected $redis;
	/**
	 * Constructor, we override the parent to pass our own arguments
	 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'rlje_theme_delete_redis_cache', // Singular label
				'plural'   => 'rlje_theme_delete_redis_caches', // plural label, also this well be one of the table css class
				'ajax'     => false, // We won't support Ajax for this table
				'screen'   => 'rlje_redis_table', // Add this to prevent PHP notice in the log
			)
		);

		$this->redis = new Redis();
		$this->redis->connect( WP_REDIS_HOST, WP_REDIS_PORT );
	}

	/**
	 * Add extra markup in the toolbars before or after the list
	 *
	 * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
	 */
	public function extra_tablenav( $which ) {
		if ( $which == 'top' ) {
			// The code that goes before the table is here
			echo "Hello, I'm before the table";
		}
		if ( $which == 'bottom' ) {
			// The code that goes after the table is there
			echo "Hi, I'm after the table";
		}
	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'key':
			case 'value':
			case 'ttl':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes
		}
	}

	// NOT USE AT THE MOMENT
	public function rlje_column_title( $item ) {
		// Build row actions
		$actions = array(
			'edit'   => sprintf( '<a href="?page=%s&action=%s&movie=%s">Edit</a>', $_REQUEST['page'], 'edit', $item['key'] ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&movie=%s">Delete</a>', $_REQUEST['page'], 'delete', $item['key'] ),
		);

		// Return the title contents
		return sprintf(
			'%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
			/*$1%s*/ $item['title'],
			/*$2%s*/ $item['key'],
			/*$3%s*/ $this->row_actions( $actions )
		);
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie")
			/*$2%s*/ $item['key']                // The value of the checkbox should be the record's id
		);
	}

	/**
	 * Define the columns that are going to be used in the table
	 *
	 * @return array $columns, the array of columns to use with the table
	 */
	public function get_columns() {
		$columns = array(
			'cb'    => '<input type="checkbox" />',
			'key'   => 'Key',
			'value' => 'Value',
			'ttl'   => 'TTL',
		);

		return $columns;
	}

	/**
	 * Decide which columns to activate the sorting functionality on
	 *
	 * @return array $sortable_columns, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'key'   => array( 'key', true ),
			'value' => array( 'value', false ),
			'ttl'   => array( 'ttl', true ),
		);

		return $sortable_columns;
	}

	public function get_bulk_actions() {
		$actions = array(
			'delete' => 'Delete',
		);

		return $actions;
	}

	// NOT USE AT THE MOMENT
	public function rlje_process_bulk_action() {
		// Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			// wp_die( 'Items deleted (or they would be if we had items to delete)!' );
			switch ( $_REQUEST['rlje_action'] ) {
				case 'delete_selected_redis_caches':
					if ( ! empty( $_REQUEST['rlje_theme_delete_redis_cache'] ) ) {
						foreach ( $_POST['rlje_theme_delete_redis_cache'] as $cache_key ) {
							$this->redis->del( $_REQUEST['rlje_theme_delete_redis_cache'] );
						}
					}
					break;
				default:
					// Do nothing
			}
		}
	}

	/**
	 * Checks the current user's permissions
	 */
	// public function ajax_user_can() {
	// }
	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	public function prepare_items() {
		global $wpdb, $_wp_column_headers;
		$screen = get_current_screen();

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$data = $this->get_redis_caches();

		$current_page = $this->get_pagenum();

		$total_items = count( $data );

		$total_pages = 1;
		$per_page    = 200;

		$this->items = $data;
		// How many pages do we have in total? $totalpages = ceil($totalitems/$perpage);
		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page'    => $per_page,
			)
		);
	}

	// NOT USE AT THE MOMENT
	/**
	 * Display the rows of records in the table
	 *
	 * @return string, echo the markup of the rows
	 */
	public function rlje_display_rows() {
		// Get the records registered in the prepare_items method
		$records = $this->items;
		// Get the columns registered in the get_columns and get_sortable_columns methods
		list( $columns, $hidden ) = $this->get_column_info();
		// Loop for each record
		if ( ! empty( $records ) ) {
			foreach ( $records as $rec ) {
				// Open the line
				echo '<tr id="record_' . $rec->link_id . '">';
				foreach ( $columns as $column_name => $column_display_name ) {
					// Style attributes for each col
					$class = "class='$column_name column-$column_name'";
					$style = '';
					if ( in_array( $column_name, $hidden ) ) {
						$style = ' style="display:none;"';
					}
					$attributes = $class . $style;

					// Display the cell
					switch ( $column_name ) {
						case 'key':
							echo '<td ' . $attributes . '>' . stripslashes( $rec['key'] ) . '</td>';
							break;
						case 'value':
							echo '<td ' . $attributes . '>' . stripslashes( $rec['value'] ) . '</td>';
							break;
						case 'ttl':
							echo '<td ' . $attributes . '>' . stripslashes( $rec['ttl'] ) . '</td>';
							break;
						case 'cb':
						default:
									echo $column_display_name;
					}
				}
				// Close the line
				echo '</tr>';
			}
		}
	}
	public function get_redis_caches() {
		$prefix           = ( ! empty( $this->theme_settings['current_theme'] ) ) ? $this->theme_settings['current_theme'] : 'acorn';
		$api_cache_groups = apply_filters( 'rlje_redis_api_cache_groups', array(
			'homepage',
			'initialJson',
			'collections',
			'schedule',
			'detail_',
			'season',
			'episode',
			// 'userStatus',
			// 'userEmail',
			// 'userWebPayment',
			// 'userStripeCustomerID',
			'browse',
			'browse_orderby',
			'franchises',
			'home_franchises',
			'browse_franchises',
			'contentPage_items',
			// 'atv_userProfile_',
		) );

		$data = array();
		foreach ( $api_cache_groups as $partial_key ) {
			$found_keys = $this->redis->keys( '*' . $partial_key . '*' );
			foreach ( $found_keys as $key ) {
				$data[ $key ] = array(
					'key'   => $key,
					'value' => $this->redis->get( $key ),
					'ttl'   => $this->redis->ttl( $key ),
				);
			}
		}

		return $data;
	}

	public function delete_redis_caches( $caches ) {
		// Delete each transient caches if it exists
		foreach ( $caches as $cache ) {
			$transient_exists = get_transient( $cache );
			if ( false !== $transient_exists ) {
				delete_transient( $cache );
			}
		}

		$is_deleted = $this->redis->del( $caches );

		return $is_deleted;
	}
}
