<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('email_templates', 'name')) {
                $table->string('name')->after('id');
            }
            if (! Schema::hasColumn('email_templates', 'slug')) {
                $table->string('slug')->unique()->after('name');
            }
            if (! Schema::hasColumn('email_templates', 'subject')) {
                $table->string('subject')->after('slug');
            }
            if (! Schema::hasColumn('email_templates', 'body')) {
                $table->longText('body')->after('subject');
            }
            if (! Schema::hasColumn('email_templates', 'variables')) {
                $table->json('variables')->nullable()->after('body');
            }
            if (! Schema::hasColumn('email_templates', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('variables');
            }
        });

        // Also upgrade barcode_settings if it is a stub
        if (Schema::hasTable('barcode_settings')) {
            Schema::table('barcode_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('barcode_settings', 'name')) {
                    $table->string('name')->after('id')->default('Default');
                }
                if (! Schema::hasColumn('barcode_settings', 'barcode_type')) {
                    $table->string('barcode_type')->default('CODE128')->after('name');
                }
                if (! Schema::hasColumn('barcode_settings', 'width')) {
                    $table->unsignedSmallInteger('width')->default(150)->after('barcode_type');
                }
                if (! Schema::hasColumn('barcode_settings', 'height')) {
                    $table->unsignedSmallInteger('height')->default(80)->after('width');
                }
                if (! Schema::hasColumn('barcode_settings', 'show_text')) {
                    $table->boolean('show_text')->default(true)->after('height');
                }
                if (! Schema::hasColumn('barcode_settings', 'show_price')) {
                    $table->boolean('show_price')->default(true)->after('show_text');
                }
                if (! Schema::hasColumn('barcode_settings', 'show_product_name')) {
                    $table->boolean('show_product_name')->default(true)->after('show_price');
                }
                if (! Schema::hasColumn('barcode_settings', 'show_company_name')) {
                    $table->boolean('show_company_name')->default(false)->after('show_product_name');
                }
                if (! Schema::hasColumn('barcode_settings', 'labels_per_row')) {
                    $table->unsignedTinyInteger('labels_per_row')->default(4)->after('show_company_name');
                }
                if (! Schema::hasColumn('barcode_settings', 'is_default')) {
                    $table->boolean('is_default')->default(false)->after('labels_per_row');
                }
                if (! Schema::hasColumn('barcode_settings', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('is_default');
                }
            });
        }

        // Also upgrade invoice_templates if it is a stub
        if (Schema::hasTable('invoice_templates')) {
            Schema::table('invoice_templates', function (Blueprint $table) {
                if (! Schema::hasColumn('invoice_templates', 'name')) {
                    $table->string('name')->after('id');
                }
                if (! Schema::hasColumn('invoice_templates', 'slug')) {
                    $table->string('slug')->unique()->after('name');
                }
                if (! Schema::hasColumn('invoice_templates', 'type')) {
                    $table->string('type')->default('pos')->after('slug');
                }
                if (! Schema::hasColumn('invoice_templates', 'html_content')) {
                    $table->longText('html_content')->nullable()->after('type');
                }
                if (! Schema::hasColumn('invoice_templates', 'settings')) {
                    $table->json('settings')->nullable()->after('html_content');
                }
                if (! Schema::hasColumn('invoice_templates', 'is_default')) {
                    $table->boolean('is_default')->default(false)->after('settings');
                }
                if (! Schema::hasColumn('invoice_templates', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('is_default');
                }
            });
        }
    }

    public function down(): void
    {
        // Intentionally left empty — dropping columns is destructive
    }
};
