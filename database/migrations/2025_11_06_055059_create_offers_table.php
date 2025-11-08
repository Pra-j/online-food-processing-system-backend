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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('title');

            $table->enum('type', ['global', 'product']);
            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('offer_kind', ['percentage', 'fixed_amount', 'buy_x_get_y'])
                ->default('percentage');
            $table->decimal('value', 10, 2)->nullable(); // for percentage or fixed
            $table->unsignedInteger('buy_quantity')->nullable(); // buy-x-get-y
            $table->unsignedInteger('get_quantity')->nullable(); // buy-x-get-y

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->unsignedInteger('max_usage')->default(5);
            $table->unsignedInteger('num_used')->default(0); // 0 = unlimited usage
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
