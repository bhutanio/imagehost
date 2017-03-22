<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateImagesTableAddFlags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('images', function (Blueprint $table) {
            $table->boolean('adult')->unsigned()->default(false)->after('image_height');
            $table->index('adult');
            $table->boolean('private')->unsigned()->default(true)->after('adult');
            $table->index('private');
            $table->timestamp('expire')->nullable()->after('private');
            $table->text('image_description')->nullable()->change();
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->boolean('adult')->unsigned()->default(false)->after('album_description');
            $table->index('adult');
            $table->boolean('private')->unsigned()->default(true)->after('adult');
            $table->index('private');
            $table->timestamp('expire')->nullable()->after('private');
            $table->text('album_description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->string('album_description')->nullable()->default(null)->change();
            $table->dropColumn(['adult', 'private', 'expire']);
        });

        Schema::table('images', function (Blueprint $table) {
            $table->string('image_description')->nullable()->default(null)->change();
            $table->dropColumn(['adult', 'private', 'expire']);
        });
    }
}
