<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKyandaRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'kyanda_requests',
            function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->string('status');
                $table->integer('status_code');
                $table->string('reference')->unique();
                $table->string('message');
                $table->string('provider');

                $table->unsignedBigInteger('relation_id')->nullable();
                $table->index('relation_id');

                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kyanda_requests');
    }
}
