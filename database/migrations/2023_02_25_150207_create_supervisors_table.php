<?php

use App\Models\Supervisor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupervisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervisors', function (Blueprint $table) {
            $table->id();
            $table->string('fname', 20);
            $table->string('sname', 20);
            $table->string('tname', 20);
            $table->string('lname', 20);
            $table->string('identity_no', 9)->unique();
            $table->string('phone', 13)->unique();
            $table->text('local_region')->nullable();
            $table->string('password');
            $table->enum('gender', Supervisor::GENDER);
            $table->enum('status', Supervisor::STATUS);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('supervisors');
    }
}
