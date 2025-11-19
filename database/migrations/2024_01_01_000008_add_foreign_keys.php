<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Foreign keys sudah ditambahkan di migration masing-masing tabel
        // File ini untuk additional foreign keys jika diperlukan
    }

    public function down()
    {
        // Drop foreign keys jika diperlukan
    }
};