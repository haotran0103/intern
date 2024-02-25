<?php

namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\chatbot;
class ChatBotController extends Controller
{

    public function index()
    {
        $questions = chatbot::with(['parentQuestion', 'nextQuestion'])->get();

        return view('questions.index', compact('questions'));
    }

    public function create()
    {
        return view('questions.create');
    }

    public function store(Request $request)
    {
        chatbot::create($request->all());

        return redirect()->route('questions.index')->with('success', 'Câu hỏi đã được thêm thành công!');
    }

    public function edit(chatbot $question)
    {
        return view('questions.edit', compact('question'));
    }

    public function update(Request $request, chatbot $question)
    {
        $question->update($request->all());

        return redirect()->route('questions.index')->with('success', 'Câu hỏi đã được cập nhật thành công!');
    }

    public function destroy(chatbot $question)
    {
        $question->delete();

        return redirect()->route('questions.index')->with('success', 'Câu hỏi đã được xóa thành công!');
    }

}
