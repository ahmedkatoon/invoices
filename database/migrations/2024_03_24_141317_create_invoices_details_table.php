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
        Schema::create('invoices_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("id_invoice");
            $table->foreign("id_invoice")->references("id")->on("invoices")->onUpdate("cascade")->onDelete("cascade");
            $table->string("invoice_number", 255);
            $table->string("product", 50);
            $table->string("section", 255);
            $table->string("status", 50);
            $table->integer("value_status");
            $table->date("Payment_Date")->nullable();
            $table->text("note")->nullable();
            $table->string("user",100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_details');
    }
};
