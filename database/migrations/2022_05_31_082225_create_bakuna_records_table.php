<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bakuna_records', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vaccination_site_id')->constrained('vaccination_sites')->onDelete('cascade');
            $table->text('case_id');
            $table->date('case_date');
            $table->text('case_location');
            $table->string('animal_type');
            $table->string('bite_type');
            $table->string('body_site');
            $table->tinyInteger('category_level');
            $table->tinyInteger('washing_of_bite');
            $table->date('rig_date_given')->nullable();
            $table->string('pep_route');
            $table->date('d0_date');
            $table->date('d3_date');
            $table->date('d7_date');
            $table->date('d14_date');
            $table->date('d28_date');
            $table->text('brand_name');
            $table->string('outcome');
            $table->string('biting_animal_status');
            $table->text('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bakuna_records');
    }
};
