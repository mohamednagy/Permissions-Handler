<?php

use Doctrine\Common\Inflector\Inflector;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPermissionsMigrations extends Migration
{
    /**
     * @var array
     */
    private $tables;

    /**
     * __construct.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tables = config('permissionsHandler.tables');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable($this->tables['roles'])) {
            Schema::create($this->tables['roles'], function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable($this->tables['role_user'])) {
            Schema::create($this->tables['role_user'], function (Blueprint $table) {
                $foreignKeyName = Inflector::singularize($this->tables['roles']).'_id';

                $table->integer($foreignKeyName)->unsigned()->index();
                $table->foreign($foreignKeyName)->references('id')->on($this->tables['roles'])->onDelete('cascade');

                $table->morphs('model');

                $table->timestamps();

                $table->primary([$foreignKeyName, 'model_id', 'model_type']);
            });
        }

        if (! Schema::hasTable($this->tables['permissions'])) {
            Schema::create($this->tables['permissions'], function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable($this->tables['permission_role'])) {
            Schema::create($this->tables['permission_role'], function (Blueprint $table) {
                $permissionsForeignKeyName = Inflector::singularize($this->tables['permissions']).'_id';
                $rolesForeignKeyName = Inflector::singularize($this->tables['roles']).'_id';

                $table->integer($permissionsForeignKeyName)->unsigned()->index();
                $table->foreign($permissionsForeignKeyName)->references('id')->on($this->tables['permissions'])->onDelete('cascade');

                $table->integer($rolesForeignKeyName)->unsigned()->index();
                $table->foreign($rolesForeignKeyName)->references('id')->on($this->tables['roles'])->onDelete('cascade');

                $table->timestamps();

                $table->primary([$permissionsForeignKeyName, $rolesForeignKeyName]);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable($this->tables['role_user'])) {
            Schema::drop($this->tables['role_user']);
        }

        if (Schema::hasTable($this->tables['permission_role'])) {
            Schema::drop($this->tables['permission_role']);
        }

        if (Schema::hasTable($this->tables['roles'])) {
            Schema::drop($this->tables['roles']);
        }

        if (Schema::hasTable($this->tables['permissions'])) {
            Schema::drop($this->tables['permissions']);
        }
    }
}
