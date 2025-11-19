<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('report_name', 150)->nullable();
            $table->enum('report_type', ['excel', 'pdf'])->default('excel');
            $table->string('file_path', 255)->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('generated_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};