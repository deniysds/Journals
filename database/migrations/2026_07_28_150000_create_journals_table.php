<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Runs the migrations for the journals table.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_name')->nullable();
            $table->string('issn_p')->nullable();
            $table->string('issn_e')->nullable();
            $table->text('description')->nullable();
            $table->text('scope')->nullable();
            $table->text('guidelines')->nullable();
            $table->text('publication_ethics')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->index(['name', 'is_active']);
        });
    }

    /**
     * Reverses the migrations for the journals table.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
