<?php

use App\Models\Group;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 25)->unique();
            $table->string('image')->nullable();
            $table->enum('status', Group::STATUS);
            $table->text('region', 50)->nullable();
            $table->foreignId('center_id')->constrained('centers', 'id')->nullOnDelete();
            $table->foreignId('keeper_id')->constrained('keepers', 'id')->nullOnDelete();
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
        Schema::dropIfExists('groups');
    }
}
