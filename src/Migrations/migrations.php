<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserPermissionsMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private $actorsTable;
    private $actor;

    public function __construct()
    {
        $config = config('permissionsHandler');
        if(key_exists('table',$config)){
            $this->actorsTable = $config['table'];
        }
        $this->actor=substr($this->actorsTable,0,-1);
    }

    public function up()
    {

        if(!Schema::hasTable($this->actorsTable)){
            Schema::create($this->actorsTable, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('role_'.$this->actor)) {
            Schema::create('role_'.$this->actor, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('role_id')->unsigned()->index();
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->integer($this->actor.'_id')->unsigned()->index();
                $table->foreign($this->actor.'_id')->references('id')->on($this->actorsTable)->onDelete('cascade');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permission_role')) {
            Schema::create('permission_role', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('permission_id')->unsigned()->index();
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
                $table->integer('role_id')->unsigned()->index();
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->timestamps();
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
        if (Schema::hasTable($this->actorsTable)) {
            Schema::drop($this->actorsTable);
        }
    }
}
