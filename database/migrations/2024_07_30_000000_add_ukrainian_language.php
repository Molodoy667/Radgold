<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Modules\Language\Entities\Language;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Добавляем украинский язык в базу данных
        Language::create([
            'name' => 'Українська',
            'code' => 'uk',
            'icon' => 'flag-icon-ua',
            'direction' => 'ltr',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Удаляем украинский язык
        Language::where('code', 'uk')->delete();
    }
};