<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('favorito', function (Blueprint $table) {
            $table->dropUnique('uq_favorito_usuario_negocio');
        });
    }

    public function down(): void
    {
        Schema::table('favorito', function (Blueprint $table) {
            $table->unique(['id_usuario', 'id_negocio'], 'uq_favorito_usuario_negocio');
        });
    }
};
