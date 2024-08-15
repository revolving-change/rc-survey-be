<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\SurveyTaker;
use App\Models\SurveyQuestion;

class TakerAnswer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'survey_taker_id',
        'survey_question_id',
        'answer',
    ];

    public function surveyTaker()
    {
        return $this->belongsTo(SurveyTaker::class);
    }

    public function surveyQuestion()
    {
        return $this->belongsTo(SurveyQuestion::class);
    }
}
