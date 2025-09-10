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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('job_no')->nullable();
            $table->string('style_no')->nullable();
            $table->date('po_date')->nullable();
            $table->date('ship_date')->nullable();
            $table->string('fabrics')->nullable();
            $table->integer('gsm')->nullable();
            $table->string('buyer')->nullable();
            $table->integer('order_qty')->nullable();
            $table->string('file_path')->nullable();
            $table->string('body_color')->nullable();

            $table->foreignId('pack_id')->nullable()->constrained('packs')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
