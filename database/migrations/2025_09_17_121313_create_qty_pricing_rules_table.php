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
        Schema::create('qty_pricing_rules', function (Blueprint $table) {
          $table->increments('id'); // <- INT UNSIGNED, bukan $table->id()
    $table->unsignedInteger('variation_id');
    $table->unsignedInteger('unit_id');
    $table->unsignedInteger('price_group_id')->nullable();
    $table->unsignedInteger('min_qty');
    $table->enum('discount_type', ['fixed','percent'])->default('fixed');
    $table->decimal('discount_value', 15, 4)->default(0);
    $table->unsignedInteger('location_id')->nullable();
    $table->date('valid_from')->nullable();
    $table->date('valid_to')->nullable();
    $table->timestamps();

    $table->index(['variation_id','unit_id','price_group_id','location_id','min_qty'], 'idx_qpr_core');
    $table->unique(['variation_id','unit_id','price_group_id','location_id','min_qty'], 'uq_qpr_breakpoint');

    $table->foreign('variation_id')->references('id')->on('variations')->onDelete('cascade');
    $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
    $table->foreign('price_group_id')->references('id')->on('selling_price_groups')->onDelete('cascade');
    // jika pakai location_id, pastikan tipe 'business_locations.id' juga INT UNSIGNED lalu aktifkan:
    // $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');
        });
    }
    public function down(): void {
        Schema::table('qty_pricing_rules', function (Blueprint $table) {
            $table->dropIndex('idx_qpr_core');
            $table->dropUnique('uq_qpr_breakpoint');
        });
        Schema::dropIfExists('qty_pricing_rules');
    }
};
