<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('place');
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_free')->default(false);
            $table->integer('capacity');
            $table->string('image')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['actif', 'archivé'])->default('actif');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('events'); }
};
