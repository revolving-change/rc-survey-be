<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTakerAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taker_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_taker_id');
            $table->foreign('survey_taker_id')->references('id')->on('survey_takers')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('survey_question_id');
            $table->foreign('survey_question_id')->references('id')->on('survey_questions')->constrained()->onDelete('cascade');
            $table->string('answer');
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
        Schema::dropIfExists('taker_answers');
    }
}
