<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_groups', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('group');
            $table->string('group_code');
            $table->text('description')->nullable();
        });

        DB::table('user_groups')->delete();
        $user_groups = [
            [
                'group'      => "Super Admin",
                'group_code' => "SU",
            ],
            [
                'group'      => "Admin",
                'group_code' => "ADMIN",
            ],
            [
                'group'      => "Moderator",
                'group_code' => "MODERATOR",
            ],
            [
                'group'      => "Staff",
                'group_code' => "STAFF",
            ],
            [
                'group'      => "Member",
                'group_code' => "MEMBER",
            ],
            [
                'group'      => "Banned",
                'group_code' => "BANNED",
            ],
            [
                'group'      => "Deleted",
                'group_code' => "DELETED",
            ],
        ];
        DB::table('user_groups')->insert($user_groups);

        Schema::table('users', function (Blueprint $table) {
            $table->bigIncrements('id')->change();
            $table->integer('group_id')->unsigned()->after('id');
            $table->boolean('active')->unsigned()->default(false)->after('password');
        });

        DB::table('users')->update([
            'active'   => true,
            'group_id' => 5,
        ]);

        \App\Models\User::where('id', 2)->update(['group_id' => 1]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('user_groups');
        });

        Schema::create('user_profile', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->mediumText('welcome')->nullable();
            $table->date('birthday')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->integer('country_id')->nullable();
            $table->string('city')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('signature')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['group_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['group_id', 'active']);
        });

        Schema::enableForeignKeyConstraints();

        Schema::dropIfExists('user_profile');
        Schema::dropIfExists('user_groups');
    }
}
