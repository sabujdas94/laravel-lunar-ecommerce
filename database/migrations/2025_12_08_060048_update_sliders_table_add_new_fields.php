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
        Schema::table('sliders', function (Blueprint $table) {
            // Remove old title and link columns
            $table->dropColumn(['title', 'link']);
            
            // Add new fields
            $table->string('heading')->after('image');
            $table->string('sub_heading')->nullable()->after('heading');
            $table->string('button1_label')->nullable()->after('sub_heading');
            $table->string('button1_url')->nullable()->after('button1_label');
            $table->string('button2_label')->nullable()->after('button1_url');
            $table->string('button2_url')->nullable()->after('button2_label');
            $table->string('tag')->nullable()->after('button2_url');
            $table->tinyInteger('tag_style')->default(1)->after('tag')->comment('1, 2, or 3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            // Remove new fields
            $table->dropColumn([
                'heading',
                'sub_heading',
                'button1_label',
                'button1_url',
                'button2_label',
                'button2_url',
                'tag',
                'tag_style'
            ]);
            
            // Restore old fields
            $table->string('title')->after('id');
            $table->string('link')->nullable()->after('image');
        });
    }
};
