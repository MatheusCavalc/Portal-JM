<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoicings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id');
            $table->decimal('nfe_value', 10, 2);
            $table->decimal('bol_value', 10, 2);
            $table->date('initial_date');
            $table->date('final_date');
            $table->string('month_sale', 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoicings');
    }
};
