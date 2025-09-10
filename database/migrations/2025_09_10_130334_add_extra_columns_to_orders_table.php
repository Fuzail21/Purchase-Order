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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('title')->nullable()->after('pack_id'); 
            $table->longText('description')->nullable()->after('title');
            $table->text('po_label')->nullable()->after('description');
            $table->text('care_label')->nullable()->after('po_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'po_label', 'care_label']);
        });
    }
};
