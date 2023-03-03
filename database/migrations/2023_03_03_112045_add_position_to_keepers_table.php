<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPositionToKeepersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('keepers', function (Blueprint $table) {
            //
            $table->string('position')->default(\App\Models\Keeper::POSITION)->after('identity_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('keepers', function (Blueprint $table) {
            //
            $table->dropColumn('position');
        });
    }
}
