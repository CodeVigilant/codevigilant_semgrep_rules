<?php
// POSITIVE repro: WP_List_Table column_* method returns stored object
// property / array element without escaping.
class Repro_List_Table extends WP_List_Table {
	protected function column_channel( $item ) {
		if ( empty( $item->channel ) ) {
			return '';
		}

		$term = get_term_by( 'slug', $item->channel, 'some_tax' );

		if ( empty( $term ) or is_wp_error( $term ) ) {
			return $item->channel; // unescaped stored data -> printed by framework
		}

		return $item->fields['raw_value']; // unescaped array element
	}

	protected function column_name( $item ) {
		return $item->name;
	}
}
