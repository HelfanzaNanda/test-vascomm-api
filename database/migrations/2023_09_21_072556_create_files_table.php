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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('filecode')->unique();
            $table->string('original_name');
            $table->string('filepath')->nullable();
            $table->string('fileurl')->nullable();
            $table->string('filename');
            $table->string('filesize');
            $table->string('mime_type');
            $table->string('extension');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable();
			$table->foreignId('updated_by')->nullable();
			$table->foreignId('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
