<?php

use App\Models\Base\Area;
use App\Models\Base\City;
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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(City::class)->constrained('cities');
        });


        $cities = json_decode(file_get_contents(resource_path('json/governorates.json'))); 
        $locations = json_decode(file_get_contents(resource_path('json/cities.json'))); 

        foreach ($cities as $c) {
            City::newCity($c->governorate_name_ar);
        }
        foreach ($locations as $loc) {
            Area::newArea($loc->city_name_ar, $loc->governorate_id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
        Schema::dropIfExists('cities');
    }
};
