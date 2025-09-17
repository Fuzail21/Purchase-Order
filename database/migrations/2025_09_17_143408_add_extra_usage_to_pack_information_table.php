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
        Schema::table('pack_information', function (Blueprint $table) {
            $table->decimal('pack_extra_percent', 5, 2)->default(0.00)->after('pack_qty');
            $table->integer('pack_extra_qty')->default(0)->after('pack_extra_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pack_information', function (Blueprint $table) {
            $table->dropColumn('pack_extra_percent');
            $table->dropColumn('pack_extra_qty');
        });
    }
};
