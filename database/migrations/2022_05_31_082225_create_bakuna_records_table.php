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
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vaccination_site_id')->constrained('vaccination_sites')->onDelete('cascade');
            $table->text('case_id');
            $table->date('case_date');
            $table->text('case_location');
            $table->string('animal_type');
            $table->string('animal_type_others')->nullable();
            $table->date('bite_date')->nullable();
            $table->string('bite_type');
            $table->string('body_site');
            $table->tinyInteger('category_level');
            $table->tinyInteger('washing_of_bite');
            $table->date('rig_date_given')->nullable();
            $table->string('pep_route');
            $table->text('brand_name');
            $table->date('d0_date');
            $table->tinyInteger('d0_done')->default(0);
            $table->date('d3_date');
            $table->tinyInteger('d3_done')->default(0);
            $table->date('d7_date');
            $table->tinyInteger('d7_done')->default(0);
            $table->date('d14_date');
            $table->tinyInteger('d14_done')->default(0);
            $table->date('d28_date');
            $table->tinyInteger('d28_done')->default(0);
            $table->string('outcome');
            $table->string('biting_animal_status');
            $table->text('remarks')->nullable();

            $table->timestamps();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
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
