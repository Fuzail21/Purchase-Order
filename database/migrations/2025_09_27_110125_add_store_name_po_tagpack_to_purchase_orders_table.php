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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('store_name')->nullable()->after('file_path'); // or adjust position
            $table->string('po_file')->nullable()->after('store_name'); // for attachment (file path)
            $table->string('tag_pack')->nullable()->after('po_file');   // for attachment (file path)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['store_name', 'po_file', 'tag_pack']);
        });
    }
};
