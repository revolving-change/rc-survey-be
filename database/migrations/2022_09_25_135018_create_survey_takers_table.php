<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveyTakersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('survey_takers', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('type');
            $table->string('status');
            $table->unsignedBigInteger('survey_form_id');
            $table->foreign('survey_form_id')->references('id')->on('survey_forms')->constrained()->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('survey_takers');
    }
}
