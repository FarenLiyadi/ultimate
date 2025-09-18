<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
        Schema::create('variation_unit_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('variation_id');
            $table->unsignedInteger('unit_id');
            $table->unsignedInteger('price_group_id')->nullable(); // NULL=Default
            $table->decimal('price_inc_tax', 15, 4)->nullable();      // simpan INC tax (gaya Ultimate POS)
            $table->timestamps();

            $table->unique(['variation_id','unit_id','price_group_id'], 'uq_vuprice');
            $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('price_group_id')->references('id')->on('selling_price_groups')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::dropIfExists('variation_unit_prices');
    }
};
