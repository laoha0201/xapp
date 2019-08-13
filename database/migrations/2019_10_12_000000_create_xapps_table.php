<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateXappsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xapps', function (Blueprint $table) {
			$table->engine = 'InnoDB';
            $table->Increments('id');
            $table->string('name')->unique();
			$table->string('title');
            $table->string('table');
			$table->text('ctrl')->nullable();
			$table->text('sets')->nullable();
			$table->integer('parent_id')->default(0);
			$table->integer('order')->default(0);
            $table->timestamps();
			$table->softDeletes();
        });

        Schema::create('cates', function (Blueprint $table) {
			$table->engine = 'InnoDB';
            $table->increments('id');
			$table->integer('xapp_id');
            $table->integer('parent_id')->default(0);
            $table->integer('order')->default(0);
            $table->string('name');
            $table->string('title');
			$table->string('groups')->nullable();
			$table->text('sets')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
			$table->engine = 'InnoDB';
            $table->uuid('id');
            $table->Biginteger('user_id')->nullable();
			$table->integer('xapp_id')->nullable();
            $table->integer('cate_id')->nullable();
			$table->string('group')->nullable();
			$table->string('pos')->nullable();
            $table->string('title');
			$table->string('note')->nullable();
            $table->text('content');
            $table->text('html')->nullable();
            $table->text('images')->nullable();
            $table->text('extend')->nullable();
            $table->tinyInteger('checked')->default(1);
            $table->timestamps();
			$table->softDeletes();
			$table->primary('id');
			$table->index('cate_id', 'my_cate_id');
			$table->index('checked', 'my_checked');
			$table->index('pos', 'my_pos');
			$table->index('xapp_id', 'my_xapp_id');
			$table->index('user_id', 'my_user_id');
        });

        Schema::create('attachments', function (Blueprint $table) {
			$table->engine = 'InnoDB';
            $table->Bigincrements('id');
			$table->uuid('root_id')->nullable();
			$table->integer('xapp_id');
			$table->Biginteger('user_id')->nullable();
            $table->string('type',100)->nullable();
            $table->string('title')->nullable();
            $table->string('cover')->nullable();
			$table->string('attach')->nullable();
			$table->text('attaches')->nullable();
			$table->string('url')->nullable();			
			$table->string('note')->nullable();
            $table->timestamps();
			$table->index('xapp_id', 'my_xapp_id');
			$table->index('user_id', 'my_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xapps');
		Schema::dropIfExists('cates');
		Schema::dropIfExists('posts');
		Schema::dropIfExists('attachments');
    }
}
