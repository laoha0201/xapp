<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
	 * 添加了username和avatar字段，原admin_user设置
	 * 添加status身份,普通则空，如admin,teacher等，可确定可进入后台
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
			$table->string('username')->nullable()->unique();
			$table->string('avatar')->nullable();
			$table->string('status')->nullable();
			$table->index('status', 'my_status');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
