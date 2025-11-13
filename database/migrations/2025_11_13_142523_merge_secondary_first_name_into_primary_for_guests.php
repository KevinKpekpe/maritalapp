<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Cette migration fusionne les noms des invités de type "couple" :
     * - Pour les couples avec secondary_first_name, on concatène les deux noms dans primary_first_name
     * - Ensuite on supprime la colonne secondary_first_name
     */
    public function up(): void
    {
        // Étape 1 : Fusionner les noms des couples existants
        // Pour les invités de type "couple" qui ont un secondary_first_name,
        // on concatène les deux noms dans primary_first_name
        DB::table('guests')
            ->where('type', 'couple')
            ->whereNotNull('secondary_first_name')
            ->where('secondary_first_name', '!=', '')
            ->update([
                'primary_first_name' => DB::raw("CONCAT(primary_first_name, ' & ', secondary_first_name)")
            ]);

        // Étape 2 : Supprimer la colonne secondary_first_name
        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('secondary_first_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * En cas de rollback, on recrée la colonne mais on ne peut pas récupérer les données fusionnées
     */
    public function down(): void
    {
        // Recréer la colonne secondary_first_name
        Schema::table('guests', function (Blueprint $table) {
            $table->string('secondary_first_name')->nullable()->after('primary_first_name');
        });

        // Note: On ne peut pas récupérer les noms séparés car ils ont été fusionnés
        // Les données seront perdues lors du rollback
    }
};
