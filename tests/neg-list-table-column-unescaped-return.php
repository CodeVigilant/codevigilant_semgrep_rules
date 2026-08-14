<?php
// NEGATIVE repro: column_* methods that escape / format before returning
// must NOT fire the rule.
class Safe_List_Table extends WP_List_Table {
	protected function column_subject( $item ) {
		return esc_html( $item->subject );
	}

	protected function column_from( $item ) {
		return sprintf( '<strong>%s</strong>', esc_html( $item->from ) );
	}

	protected function column_status( $item ) {
		return esc_attr( $item->status );
	}

	protected function column_actions( $item ) {
		$actions = array();
		return $this->row_actions( $actions );
	}

	protected function column_id( $item ) {
		return $item->id();
	}

	protected function column_empty( $item ) {
		return '';
	}

	protected function column_date( $item ) {
		$t_time = sprintf( '%1$s at %2$s', '2026-01-01', '10:00' );
		return $t_time; // local derived value, not stored data access
	}
}
