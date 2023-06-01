# Wordpress Database Migrations

A WordPress library for managing database table schema upgrades and data seeding.

Ever had to create a custom table for some plugin or custom code to use? To keep the site updated with the latest version of that table you need to keep track of what version the table is at. This can get overly complex for lots of tables.

This package is forked from [deliciousbrains/wp-migrations](https://github.com/deliciousbrains/wp-migrations) and incorporates changes from [https://github.com/desaiuditd/enhanced-wp-migrations](https://github.com/desaiuditd/enhanced-wp-migrations).  

You create a new migration PHP file, add your schema update code, and optionally include a rollback method to reverse the change.

Run `wp wdm migrate` on the command line using WP CLI and any migrations not already run will be executed.

The great thing about making database schema and data updates with migrations, is that the changes are file-based and therefore can be stored in version control, giving you better control when working across different branches.

## Requirements

This package is designed to be used on a WordPress site project, not for a plugin or theme.

It needs to be running PHP 5.4 or higher.

You need to have access to run WP CLI on the server. Typically `wp wdm migrate` will be run as a last stage build step in your deployment process.

### Key Differences from wp-migrations introduced by enhanced-wp-migrations

- Removed support for multiple migration directories. I've made assumption that all of your migration files should be located at one place only. So that the package can follow [SemVer](https://semver.org/) in the migration names and filenames (Similar to [Flyway](https://flywaydb.org/)).
- Individual migrations will be identified with their respective semver number. E.g., `1`, `1.0.1`, `1.1` etc.
- `wp wdm migrate` command will use the version number of the migration instead of the class name. E.g., `wp wdm migrate 2.0.1`
- Migration file name conventions is `<version-number>.php`. E.g., `1.php`, `1.0.1.php`, `2.1.php` etc. Usually, you can start your migrations from `0.0.1.php`.
- Make sure you put only one class in one migration file.

### Key Differences from enhanced-wp-migrations

- Scaffold a migration

### Installation

- `composer require ludicdrive/wordpress-database-migrations`
- Bootstrap the package by adding `\LudicDrive\WordpressDatabaseMigrations\Database\Migrator::instance();` to an mu-plugin.
- Run `wp wdm migrate --setup` on the server.

### Migrations

By default, the command will look for migration files in `/app/migrations` directory alongside the vendor folder. This can be altered with the filter `wdm_wp_migrations_path`.

An example migration to create a table would look like:

```php
<!-- 0.0.1.php -->
<?php

use LudicDrive\WordpressDatabaseMigrations\Database\AbstractMigration;

class AddCustomTable extends AbstractMigration {

    public function run() {
        global $wpdb;

        $sql = "
            CREATE TABLE " . $wpdb->prefix . "my_table (
            id bigint(20) NOT NULL auto_increment,
            some_column varchar(50) NOT NULL,
            PRIMARY KEY (id)
            ) {$this->get_collation()};
        ";

        dbDelta( $sql );
    }

    public function rollback() {
        global $wpdb;
        $wpdb->query( 'DROP TABLE ' . $wpdb->prefix . 'my_table');
    }
}
```

For example, to add a new page:

```php
<!-- 0.0.1.php -->
<?php

use LudicDrive\WordpressDatabaseMigrations\Database\AbstractMigration;

class AddPricingPage extends AbstractMigration {

    public function run() {
        $pricing_page_id = wp_insert_post( array(
            'post_title'  => 'Pricing',
            'post_status' => 'publish',
            'post_type'   => 'page',
        ) );
        update_post_meta( $pricing_page_id, '_wp_page_template', 'page-pricing.php' );
    }
}
```

### Use

#### Run Migration

You can run specific migrations using the filename as an argument, eg. `wp wdm migrate 2.1.1`.

#### Rollback Migration

To rollback all migrations you can run `wp wdm migrate --rollback`, or just a specific migration `wp wdm migrate 2.1.1 --rollback`.

#### Scaffold Migration

To scaffold a new migration, run `wp scaffold migration <name>`.  
For example, `wp scaffold migration MyMigration` will create a new class named `MyMigration` in the default migration files directory with the correct filename and all required boilerplate code.
