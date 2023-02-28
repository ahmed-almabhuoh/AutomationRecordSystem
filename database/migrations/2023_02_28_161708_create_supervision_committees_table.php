<?php

use App\Models\SupervisionCommittee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupervisionCommitteesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervision_committees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 25)->unique();
            $table->string('image')->nullable();
            $table->enum('status', SupervisionCommittee::STATUS);
            $table->enum('type', SupervisionCommittee::TYPES);
            $table->text('region', 50)->nullable();
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
        Schema::dropIfExists('supervision_committees');
    }
}
