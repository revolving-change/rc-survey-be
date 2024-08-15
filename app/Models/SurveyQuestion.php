<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\SurveyForm;
use App\Models\TakerAnswer;

class SurveyQuestion extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'question',
        'survey_form_id',
        'highest_answer'
    ];

    public function surveyForm()
    {
        return $this->belongsTo(SurveyForm::class);
    }

    public function takerAnswers()
    {
        return $this->hasMany(TakerAnswer::class);
    }
}
