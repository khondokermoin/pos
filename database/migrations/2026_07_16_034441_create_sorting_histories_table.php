<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sorting_histories')) {
            return;
        }
        Schema::create('sorting_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignId('bulk_product_id')->constrained('products')->onDelete('cascade');
            $table->string('bulk_product_name'); // সোর্টিং এর সময়ের নাম
            $table->integer('bulk_quantity_received'); // কত পিস বাল্ক রিসিভ করা হয়েছে
            $table->json('sorted_items'); // JSON: [{"product_id": 1, "quantity": 100, "type": "Shirt"}, ...]
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // কে সোর্ট করেছে
            $table->timestamp('sorted_at');
            $table->string('reference_number')->nullable(); // ট্রান্সফার/পারচেজ রেফারেন্স
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorting_histories');
    }
};
