<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPermissionsMigrations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
    if (!Schema::hasTable('roles')) {
			Schema::create('roles', function(Blueprint $table) {
				$table -> increments('id');
				$table->string('name');
				$table -> timestamps();

			});
		}

		if (!Schema::hasTable('role_user')) {
			Schema::create('role_user', function(Blueprint $table) {
				$table -> increments('id');
				$table -> integer('role_id') -> unsigned() -> index();
				$table -> foreign('role_id') -> references('id') -> on('roles') -> onDelete('cascade');
				$table -> integer('user_id') -> unsigned() -> index();
				$table -> foreign('user_id') -> references('id') -> on('users') -> onDelete('cascade');
				$table -> timestamps();

			});
		}

    if (!Schema::hasTable('permissions')) {
			Schema::create('permissions', function(Blueprint $table) {
        $table -> increments('id');
				$table->string('name');
				$table -> timestamps();

			});
		}

    if (!Schema::hasTable('permission_role')) {
			Schema::create('permission_role', function(Blueprint $table) {
				$table -> increments('id');
				$table -> integer('permission_id') -> unsigned() -> index();
				$table -> foreign('permission_id') -> references('id') -> on('permissions') -> onDelete('cascade');
				$table -> integer('role_id') -> unsigned() -> index();
				$table -> foreign('role_id') -> references('id') -> on('roles') -> onDelete('cascade');
				$table -> timestamps();

			});
		}

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		if (Schema::hasTable('role_user')) {
			Schema::drop('role_user');
		}
		if (Schema::hasTable('permission_role')) {
			Schema::drop('permission_role');
		}
		if (Schema::hasTable('roles')) {
			Schema::drop('roles');
		}
		if (Schema::hasTable('permissions')) {
			Schema::drop('permissions');
		}
	}

}
