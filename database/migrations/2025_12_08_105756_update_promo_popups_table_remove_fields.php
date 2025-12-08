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
        Schema::table('promo_popups', function (Blueprint $table) {
            // Remove description and button_text columns if they exist
            if (Schema::hasColumn('promo_popups', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('promo_popups', 'button_text')) {
                $table->dropColumn('button_text');
            }

            // Rename link to banner_link
            $table->renameColumn('link', 'banner_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo_popups', function (Blueprint $table) {
            // Rename banner_link back to link
            $table->renameColumn('banner_link', 'link');

            // Restore removed columns if they don't exist
            if (!Schema::hasColumn('promo_popups', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('promo_popups', 'button_text')) {
                $table->string('button_text')->nullable()->after('image');
            }
        });
    }
};
