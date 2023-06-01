<?php

namespace LudicDrive\WordpressDatabaseMigrations\CLI;

class Command extends \WP_CLI_Command {

	/**
	 * Data migration command
	 *
	 * ## OPTIONS
	 *
	 * [<migration>]
	 * : Class name for the migration
	 *
	 * [--rollback]
	 * : If we are reverting a migration
	 *
	 * [--setup]
	 * : Set up the migrations table
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @throws \WP_CLI\ExitException
	 */
	public function __invoke( $args, $assoc_args ) {
		$migrator = \LudicDrive\WordpressDatabaseMigrations\Database\Migrator::instance();

		if ( isset( $assoc_args['setup'] ) ) {
			if ( ! $migrator->setup() ) {
				return \WP_CLI::warning( 'Migrations already setup' );
			}

			\WP_CLI::success( 'Migrations setup' );

			return;
		}

		$migration = null;
		if ( ! empty( $args[0] ) ) {
			$migration = $args[0];
		}

		$rollback = false;
		if ( isset( $assoc_args['rollback'] ) ) {
			$rollback = true;
		}

		$total = $migrator->run( $migration, $rollback );
		if ( 0 === $total ) {
			\WP_CLI::warning( 'There are no migrations to run.' );
		} else {
			$action = $rollback ? 'rolled back' : 'run';
			/* translators: %s: Number of migrations. */
			\WP_CLI::success( sprintf( _n( '%1$d migration %2$s.', '%1$d migrations %2$s.', $total, 'wdm' ), $total, $action ) );
		}
	}
}
