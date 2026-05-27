<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        /*
        |--------------------------------------------------------------------------
        | Remove IDs inválidos
        |--------------------------------------------------------------------------
        */

        DB::table('class_offering_disciplines')
            ->whereNotNull('teacher_id')
            ->whereNotIn(
                'teacher_id',
                function ($query) {

                    $query->select('id')
                        ->from('scholarship_holders');
                }
            )
            ->update([
                'teacher_id' => null
            ]);

        /*
        |--------------------------------------------------------------------------
        | Recria FK
        |--------------------------------------------------------------------------
        */

        Schema::table('class_offering_disciplines', function (Blueprint $table) {

            $table->foreign(
                'teacher_id',
                'fk_cod_teacher'
            )
            ->references('id')
            ->on('scholarship_holders')
            ->nullOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | Limpa IDs inválidos
        |--------------------------------------------------------------------------
        */

        DB::table('class_offering_disciplines')
            ->whereNotNull('teacher_id')
            ->whereNotIn(
                'teacher_id',
                function ($query) {

                    $query->select('id')
                        ->from('scholarship_holders');
                }
            )
            ->update([
                'teacher_id' => null
            ]);
    }
};