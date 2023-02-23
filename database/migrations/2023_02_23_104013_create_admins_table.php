<?php

use App\Models\Admin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('fname', 20);
            $table->string('sname', 20);
            $table->string('tname', 20);
            $table->string('lname', 20);
            $table->string('identity_no', 9)->unique();
            $table->string('phone', 13)->unique();
            $table->text('local_region')->nullable();
            $table->string('password');
            $table->enum('gender', Admin::GENDER);
            $table->enum('status', Admin::STATUS);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('admins');
    }
}
