<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chatbot extends Model
{
    use HasFactory;
    protected $fillable = ['question_text', 'parent_question_id', 'answer_text', 'next_question_id'];

    public function parentQuestion()
    {
        return $this->belongsTo(chatbot::class, 'parent_question_id');
    }

    public function nextQuestion()
    {
        return $this->belongsTo(chatbot::class, 'next_question_id');
    }
}
