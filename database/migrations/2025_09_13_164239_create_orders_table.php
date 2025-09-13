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
            $table->string('job_no');
            $table->string('style_no');
            $table->date('po_date')->nullable();
            $table->date('ship_date')->nullable();
            $table->string('fabrics')->nullable();
            $table->integer('gsm')->nullable();
            $table->string('buyer')->nullable();
            $table->integer('order_qty');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('po_label')->nullable();
            $table->string('care_label')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('final_total');
            $table->softDeletes();
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
