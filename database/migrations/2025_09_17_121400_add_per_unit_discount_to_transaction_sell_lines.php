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
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->decimal('per_unit_discount', 15, 4)->default(0)->after('unit_price_before_discount');
            $table->unsignedInteger('qty_pricing_rule_id')->nullable()->after('per_unit_discount');
            $table->foreign('qty_pricing_rule_id')->references('id')->on('qty_pricing_rules')->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::table('transaction_sell_lines', function (Blueprint $table) {
            $table->dropForeign(['qty_pricing_rule_id']);
            $table->dropColumn(['per_unit_discount','qty_pricing_rule_id']);
        });
    }
};
