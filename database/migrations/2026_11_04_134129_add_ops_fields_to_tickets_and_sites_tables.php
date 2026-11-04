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
        Schema::table('sites', function (Blueprint $table) {
            $table->string('contact_email')->nullable()->after('url');
            $table->string('contact_phone', 40)->nullable()->after('contact_email');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('site_id')->constrained()->nullOnDelete();
            $table->timestamp('escalated_at')->nullable()->after('closed_at');
            $table->unsignedTinyInteger('csat_score')->nullable()->after('escalated_at');
            $table->timestamp('csat_submitted_at')->nullable()->after('csat_score');

            $table->index('escalated_at');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
            $table->dropIndex(['escalated_at']);
            $table->dropColumn(['escalated_at', 'csat_score', 'csat_submitted_at']);
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['contact_email', 'contact_phone']);
        });
    }
};
