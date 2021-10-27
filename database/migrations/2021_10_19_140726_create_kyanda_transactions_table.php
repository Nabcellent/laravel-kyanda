<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKyandaTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'kyanda_transactions',
            function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->string('transaction_reference')->unique();

                $table->string('category');
                $table->string('source');
                $table->string('destination')->nullable();
                $table->string('merchant_id');

                $table->string('status');
                $table->integer('status_code');
                $table->string('message')->nullable();
                $table->json('details');

                $table->integer('amount');
                $table->timestamp('transaction_date');

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
        Schema::dropIfExists('kyanda_transactions');
    }
}
