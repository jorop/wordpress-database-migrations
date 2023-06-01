<?php

namespace LudicDrive\WordpressDatabaseMigrations\Database;

abstract class AbstractMigration {

	/**
	 * Get database collation.
	 *
	 * @return string
	 */
	protected function get_collation() {
		global $wpdb;

		if ( ! $wpdb->has_cap( 'collation' ) ) {
			return '';
		}

		return $wpdb->get_charset_collate();
	}

	/**
	 * @return mixed
	 */
	abstract public function run();
}