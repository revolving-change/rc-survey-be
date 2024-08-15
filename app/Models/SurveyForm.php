<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\SurveyQuestion;
use App\Models\SurveyTaker;

class SurveyForm extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'form_title',
        'course_id'
    ];

    public function surveyQuestions()
    {
        return $this->hasMany(SurveyQuestion::class);
    }

    public function surveyTakers()
    {
        return $this->hasMany(SurveyTaker::class);
    }

    public function getSurveyQuestions($id)
	{
		return $this->surveyQuestions()->where('survey_form_id', $id)->get();
	}
}
