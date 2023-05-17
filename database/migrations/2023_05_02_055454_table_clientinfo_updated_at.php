<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('gkojgnvu_client_info', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->float('additional_fee')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('gkojgnvu_client_info');
    }
};
