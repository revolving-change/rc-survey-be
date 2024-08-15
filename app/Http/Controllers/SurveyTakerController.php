<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

use App\Models\SurveyForm;
use App\Models\SurveyQuestion;
use App\Models\SurveyTaker;
use App\Models\TakerAnswer;

class SurveyTakerController extends Controller
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

    public function create(Request $request, $id) {
        $surveyForm = SurveyForm::find($id);
        $findTaker = SurveyTaker::where([['survey_form_id', $id], ['email', $request->email], ['type', $request->type]])->first();

        if ($request->type === 'post-survey') {
            $checkPresurvey = SurveyTaker::where([['survey_form_id', $id], ['email', $request->email], ['type', 'pre-survey']])->first();
            if(is_null($checkPresurvey)) return response(['message' => 'You have not taken pre-survey'], 500);
        }

        if (is_null($findTaker)) {
            $surveyTaker = new SurveyTaker();
            $surveyTaker->fill([
                'email' => $request->email,
                'status' => $request->status,
                'type' => $request->type,
            ]);

            $surveyTaker = $surveyForm->surveyTakers()->save($surveyTaker);

            foreach ($request->answers as $answer) {
                $takerAnswer = new TakerAnswer();
                $takerAnswer->fill([
                    'survey_question_id' => $answer['question_id'],
                    'answer' => $answer['answer'],
                ]);

                $surveyTaker->takerAnswers()->save($takerAnswer);
            }

            return response(['message' => 'Successfully saved your answers', "status" => true], 201);
        }
        else return response(['message' => 'Already taken', "status" => false], 500);
    }

    public function list($id) {
        $surveyForm = SurveyForm::find($id);
        $takers = SurveyTaker::where('survey_form_id', $id)->orderBy('email', 'ASC')->get();

        $collection = new Collection();
        $takerResults = new Collection(); 

        foreach ($takers as $taker) {
            $results = TakerAnswer::where('survey_taker_id', $taker->id)->get();
            $survey_score = 0;
            
            foreach($results as $result) {
                $question = SurveyQuestion::find($result->survey_question_id);
                
                $score = $this->getScore($question->highest_answer, $result->answer);
                $survey_score += $score;
            }

            $collection->push([
                'id' => $taker->id,
                'email' => $taker->email,
                'type' => $taker->type,
                'status' => $taker->status,
                'score' => round($survey_score / (count($results) * 5) * 100, 2)
            ]);
        }

            for ($x = 0; $x < count($collection); $x++) {
                $preSurvey = 0;
                $postSurvey = 0;

                if ($collection[$x]['type'] === 'pre-survey') {
                    $preSurvey = $collection[$x]['score'];
                    if ($x < count($collection) - 1 && $collection[$x]['email'] === $collection[$x+1]['email']) {
                        $postSurvey = $collection[$x+1]['score'];
                    }
                } else {
                    $postSurvey = $collection[$x]['score'];
                    if ($x < count($collection) - 1 && $collection[$x]['email'] === $collection[$x+1]['email']) {
                        $preSurvey = $collection[$x+1]['score'];
                    }
                }

                $takerResults->push([
                    'id' => $collection[$x]['id'],
                    'email' => $collection[$x]['email'],
                    'status' => $collection[$x]['status'],
                    'pre_survey' => $preSurvey,
                    'post_survey' => $postSurvey,
                ]);

                if ($preSurvey !== 0 && $postSurvey !== 0) $x++;
            }

        return response(['form' => $surveyForm, 'data' => $takerResults, "status" => true], 201);
    }

    public function courseResult($id) {
        $surveyForms = SurveyForm::where('course_id', $id)->get();

        $preSurveyCourse = 0;
        $postSurveyCourse = 0;

        foreach ($surveyForms as $surveyForm) {
            $takers = SurveyTaker::where('survey_form_id', $surveyForm->id)->orderBy('email', 'ASC')->get();
            
            $collection = new Collection();
            $takerResults = new Collection(); 

            foreach ($takers as $taker) {
                $results = TakerAnswer::where('survey_taker_id', $taker->id)->get();
                $survey_score = 0;
                
                foreach($results as $result) {
                    $question = SurveyQuestion::find($result->survey_question_id);
                    
                    $score = $this->getScore($question->highest_answer, $result->answer);
                    $survey_score += $score;
                }

                if ($results->count()) {
                    $collection->push([
                        'id' => $taker->id,
                        'email' => $taker->email,
                        'type' => $taker->type,
                        'status' => $taker->status,
                        'score' => round($survey_score / ($results->count() * 5) * 100, 2)
                    ]);
                }
            }

            if (count($takers))
            {
                $preSurvey = 0;
                $postSurvey = 0;

                for ($x = 0; $x < count($collection); $x++) {
                    if ($collection[$x]['type'] === 'pre-survey') {
                        $preSurvey += $collection[$x]['score'];
                        if ($x < count($collection) - 1 && $collection[$x]['email'] === $collection[$x+1]['email']) {
                            $postSurvey += $collection[$x+1]['score'];
                        }
                    } else {
                        $postSurvey += $collection[$x]['score'];
                        if ($x < count($collection) - 1 && $collection[$x]['email'] === $collection[$x+1]['email']) {
                            $preSurvey += $collection[$x+1]['score'];
                        }
                    }

                    if ($preSurvey !== 0 && $postSurvey !== 0) $x++;
                }

                $preSurveyCourse += $preSurvey / (count($takers) / 2);
                $postSurveyCourse += $postSurvey / (count($takers) / 2);
            }
        }


        return response(['pre_survey' => round($preSurveyCourse / $surveyForms->count(), 2), 'post_survey' => round($postSurveyCourse / $surveyForms->count(), 2), "status" => true], 201);
    }
}
