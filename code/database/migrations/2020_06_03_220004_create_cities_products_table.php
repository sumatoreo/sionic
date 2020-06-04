<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities_products', function (Blueprint $table) {
            $table->uuid('city_id')
                ->comment('Внешний ключ к таблице городов');
            $table->integer('product_id')
                ->comment('Внешний ключ к таблице продуктов');
            $table->integer('count')
                ->comment('Количество продукта в городе');
            $table->float('price', 3)
                ->comment('Цена продукта в городе');

            $table->foreign('city_id')->references('id')
                ->on('cities');
            $table->foreign('product_id')->references('id')
                ->on('products');

            $table->unique(['city_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cities_products', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropForeign(['product_id']);
            $table->dropUnique(['city_id', 'product_id']);
            $table->dropIfExists();
        });
    }
}
