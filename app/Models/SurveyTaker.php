<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\SurveyForm;
use App\Models\TakerAnswer;

class SurveyTaker extends Model
{
    use HasFactory, SoftDeletes;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'type',
        'status',
        'survey_form_id'
    ];

    public function takerAnswers()
    {
        return $this->hasMany(TakerAnswer::class);
    }

    public function surveyForm()
    {
        return $this->belongsTo(SurveyForm::class);
    }

    public function getTakerAnswers($id)
    {
        return $this->takerAnswers()->where('survey_taker_id', $id)->get();
    }
}
