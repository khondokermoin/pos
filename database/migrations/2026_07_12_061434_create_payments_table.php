<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->nullableMorphs('payable'); // payable_type, payable_id (Sale or Purchase)
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('payment_method', ['cash', 'card', 'mobile_banking', 'bank_transfer'])->default('cash');
            $table->string('reference')->nullable();
            $table->text('note')->nullable();
            $table->date('payment_date');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
