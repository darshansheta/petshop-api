<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('order_status_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreignId('payment_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->json('products');
            $table->json('address');
            $table->decimal('delivery_fee', 10, 2)->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamp('shipped_at');
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
