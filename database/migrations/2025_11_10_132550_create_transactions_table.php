<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('donat_id')->constrained('donats')->cascadeOnDelete();
            $table->unsignedInteger('qty');
            $table->decimal('total_harga', 15, 2);
            $table->string('status')->default('pending');
            $table->string('kode_transaksi')->unique();
            $table->string('snap_token')->nullable();

            $table->string('nama_penerima');
            $table->string('no_hp', 40);
            $table->text('alamat');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
