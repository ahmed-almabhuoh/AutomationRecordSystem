<?php

use App\Models\APIKEY;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAPIKEYSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('a_p_i_k_e_y_s', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('key', 50)->unique();
            $table->string('secret');
            $table->string('name', 50)->nullable();
            $table->enum('status', APIKEY::STATUS);
            $table->bigInteger('rat_limit')->unsigned();
            $table->foreignId('manager_id')->constrained('managers', 'id')->nullOnDelete();
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
        Schema::dropIfExists('a_p_i_k_e_y_s');
    }
}
