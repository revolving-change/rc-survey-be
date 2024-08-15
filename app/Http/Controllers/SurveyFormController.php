<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

use App\Models\SurveyForm;
use App\Models\SurveyQuestion;
use App\Models\SurveyTaker;

class SurveyFormController extends Controller
{
    public function create(Request $request) {
        $validator = Validator::make($request->all(), [
			'form_title' => 'required',
            'questions' => 'required',
            'course_id' => 'required',
		]);
		
		if ($validator->fails()) {
			return response([
				'message'=> 'Data is required.'
			], 400);
		}
		
		$newSurveyForm = new SurveyForm();
		$newSurveyForm->fill([
			'form_title' => $request->form_title,
            'course_id' => $request->course_id,
		]);

		if ($newSurveyForm->save()) {
            foreach ($request->questions as $question) {
                $newSurveyQuestion = new SurveyQuestion();
                $newSurveyQuestion->fill([
                    'question' => $question['question'],
                    'highest_answer' => $question['highest_answer'],
                ]);
                $newSurveyForm->surveyQuestions()->save($newSurveyQuestion);
            }
            return response(["data" => $newSurveyForm, "status" => true], 201);
        }
		else return response([
			'message' => 'Error in creating survey form.',
            'status' => false
		], 500); 
    }

    public function list() {
        return response(SurveyForm::orderBy("id", "desc")->get(), 201);
    }

    public function view($id, Request $request) {
        $collection = new Collection();

        $surveyTaker = SurveyTaker::where([['survey_form_id', $id], ['email', $request->email], ['type', $request->type]])->first();

        if ($surveyTaker) return response(['status' => false], 201);

        $surveyForm = SurveyForm::find($id);
        $surveyQuestions = $surveyForm->getSurveyQuestions($id);

        return response(['survey_form' => $surveyForm, 'survey_questions' => $surveyQuestions, 'status' => true], 201);
    }

    public function form($id) {
        $collection = new Collection();

        $surveyForm = SurveyForm::find($id);
        $surveyQuestions = $surveyForm->getSurveyQuestions($id);


        return response(['survey_form' => $surveyForm, 'survey_questions' => $surveyQuestions, 'status' => true], 201);
    }

    public function forms($id) {
        $surveyForms = SurveyForm::where('course_id', $id)->get();

        return response(['survey_forms' => $surveyForms, 'status' => true], 201);
    }

    public function edit(Request $request, $id) {
        $surveyForm = SurveyForm::find($id);

        $surveyForm->form_title = $request->form_title;
        $surveyForm->save();

        $collection = new Collection();

        foreach ($request->questions as $question) {
            if ($question['id']) {
                $surveyQuestion = SurveyQuestion::find($question['id']);
                $surveyQuestion->fill([
                    'question' => $question['question'],
                    'highest_answer' => $question['highest_answer'],
                ]);
                $surveyQuestion->save();

                $collection->push($question['id']);
            }
            else {
                $newSurveyQuestion = new SurveyQuestion();
                $newSurveyQuestion->fill([
                    'question' => $question['question'],
                    'highest_answer' => $question['highest_answer'],
                ]);
    
                $surveyForm->surveyQuestions()->save($newSurveyQuestion);
                $collection->push($newSurveyQuestion->id);
            }
        }

        $surveyQuestions = SurveyQuestion::where('survey_form_id', $id)->get();
        foreach ($surveyQuestions as $surveyQuestion) {
            if(!$collection->contains($surveyQuestion->id)) {
                $surveyQuestion->delete();

            }
        }
        return response(["data" => $surveyForm, "status" => true], 201);
    }
}
