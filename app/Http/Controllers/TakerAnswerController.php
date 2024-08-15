<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;

use App\Models\SurveyForm;
use App\Models\SurveyQuestion;
use App\Models\SurveyTaker;
use App\Models\TakerAnswer;

class TakerAnswerController extends Controller
{
    public function getScore($highest, $answer) {
        if ($highest === 'SA') {
            if ($answer === 'SA') return 5;
            else if ($answer === 'A') return 4;
            else if ($answer === 'U') return 3;
            else if ($answer === 'D') return 2;
            else return 1;
        } else {
            if ($answer === 'SA') return 1;
            else if ($answer === 'A') return 2;
            else if ($answer === 'U') return 3;
            else if ($answer === 'D') return 4;
            else return 5;
        }
    }

    public function view(Request $request, $id) {
        $taker_pre_survey = SurveyTaker::where([['survey_form_id', $id], ['email', $request->email], ['type', 'pre-survey']])->first();
        $taker_post_survey = SurveyTaker::where([['survey_form_id', $id], ['email', $request->email], ['type', 'post-survey']])->first();

        if (is_null($taker_pre_survey))
        {
            return response(['message' => 'No pre-survey', 'status' => false], 201);
        }

        if (is_null($taker_post_survey))
        {
            return response(['message' => 'No post-survey', 'status' => false], 201);
        }

        $pre_answer_collection = new Collection();
        $post_answer_collection = new Collection();

        $taker_pre_survey_answers = $taker_pre_survey->getTakerAnswers($taker_pre_survey->id);
        $pre_survey_score = 0;

        foreach ($taker_pre_survey_answers as $taker_pre_survey_answer) {
            $question = SurveyQuestion::find($taker_pre_survey_answer->survey_question_id);

            $score = $this->getScore($question->highest_answer, $taker_pre_survey_answer->answer);

            $pre_answer_collection->push([
                "id" => $taker_pre_survey_answer->id,
                "survey_taker_id" => $taker_pre_survey_answer->survey_taker_id,
                "survey_question_id" => $taker_pre_survey_answer->survey_question_id,
                "question" => $question->question,
                "answer" => $taker_pre_survey_answer->answer,
                "score" => $score
            ]);

            $pre_survey_score += $score;
        }

        $pre_survey_appropriate_score = $pre_survey_score / (count($taker_pre_survey_answers) * 5) * 100;

        $taker_post_survey_answers = $taker_post_survey->getTakerAnswers($taker_post_survey->id);
        $post_survey_score = 0;

        foreach ($taker_post_survey_answers as $taker_post_survey_answer) {
            $question = SurveyQuestion::find($taker_post_survey_answer->survey_question_id);

            $score = $this->getScore($question->highest_answer, $taker_post_survey_answer->answer);

            $post_answer_collection->push([
                "id" => $taker_post_survey_answer->id,
                "survey_taker_id" => $taker_post_survey_answer->survey_taker_id,
                "survey_question_id" => $taker_post_survey_answer->survey_question_id,
                "question" => $question->question,
                "answer" => $taker_post_survey_answer->answer,
                "score" => $score
            ]);

            $post_survey_score += $score;
        }

        $post_survey_appropriate_score = $post_survey_score / (count($taker_post_survey_answers) * 5) * 100;

        return response(['pre_survey_result' => round($pre_survey_appropriate_score, 2), 'post_survey_result' => round($post_survey_appropriate_score, 2),"pre_answers" => $pre_answer_collection, "post_answers" => $post_answer_collection ,'status' => true], 201);
    }

    public function viewPerCourse(Request $request, $id) {
        $forms = SurveyForm::where('course_id', $id)->get();
        
        $collection = new Collection();


        foreach ($forms as $form) {
            $taker_pre_survey = SurveyTaker::where([['survey_form_id', $form->id], ['email', $request->email], ['type', 'pre-survey']])->first();
            $taker_post_survey = SurveyTaker::where([['survey_form_id', $form->id], ['email', $request->email], ['type', 'post-survey']])->first();

            if (is_null($taker_pre_survey))
            {
                return response(['message' => 'You lack pre-survey', 'status' => false], 201);
            }
            
            if (is_null($taker_post_survey))
            {
                return response(['message' => 'You lack post-survey', 'status' => false], 201);
            }
            
            $taker_pre_survey_answers = $taker_pre_survey->getTakerAnswers($taker_pre_survey->id);
            $pre_survey_score = 0;
            
            foreach ($taker_pre_survey_answers as $taker_pre_survey_answer) {
                $question = SurveyQuestion::find($taker_pre_survey_answer->survey_question_id);
                $score = $this->getScore($question->highest_answer, $taker_pre_survey_answer->answer);
                $pre_survey_score += $score;
            }
            
            $pre_survey_appropriate_score = $pre_survey_score / (count($taker_pre_survey_answers) * 5) * 100;
            
            $taker_post_survey_answers = $taker_post_survey->getTakerAnswers($taker_post_survey->id);
            $post_survey_score = 0;
            
            foreach ($taker_post_survey_answers as $taker_post_survey_answer) {
                $question = SurveyQuestion::find($taker_post_survey_answer->survey_question_id);
                $score = $this->getScore($question->highest_answer, $taker_post_survey_answer->answer);
                $post_survey_score += $score;
            }
            
            $post_survey_appropriate_score = $post_survey_score / (count($taker_post_survey_answers) * 5) * 100;

            $collection->push([
                'id' => $form->id,
                'form_title' => $form->form_title,
                'pre_survey_result' => round($pre_survey_appropriate_score, 2),
                'post_survey_result' => round($post_survey_appropriate_score, 2),
            ]);
        }
        

        

        return response(['result' => $collection, 'status' => true], 201);
    }
}
