<?php

namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatBotController extends Controller
{
    public function index(){
        $jsonFile = public_path('chatbot_data.json'); 
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);
        return response()->json(['answer' => $data]);
    }
    public function getAnswer(Request $request)
    {
        $selectedOption = $request->input('selectedOption');

        $jsonFile = public_path('chatbot_data.json'); 
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);

        $answer = $data[$selectedOption] ?? 'Xin lỗi, không có câu trả lời cho lựa chọn này.';

        return response()->json(['answer' => $answer]);
    }
    public function addData(Request $request)
    {
        $selectedOption = $request->input('selectedOption'); 
        $newOption = $request->input('newOption');
        $answer = $request->input('answer'); 

        $jsonFile = public_path('chatbot_data.json');
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);

        if (isset($data[$selectedOption])) {
            $data[$selectedOption][$newOption] = $answer;
        } else {
            $data[$selectedOption] = [
                $newOption => $answer
            ];
        }

        file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Thêm câu hỏi và câu trả lời thành công']);
    }

    public function editData(Request $request)
    {
        $selectedOption = $request->input('selectedOption');
        $editedOption = $request->input('editedOption'); 
        $editedAnswer = $request->input('editedAnswer'); 

        $jsonFile = public_path('chatbot_data.json');
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);

        if (isset($data[$selectedOption][$editedOption])) {
            $data[$selectedOption][$editedOption] = $editedAnswer;

            file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));

            return response()->json(['message' => 'Chỉnh sửa câu hỏi và câu trả lời thành công']);
        } else {
            return response()->json(['message' => 'Lựa chọn cha hoặc lựa chọn cần sửa không tồn tại']);
        }
    }
    public function deleteData(Request $request)
    {
        $selectedOption = $request->input('selectedOption'); 
        $deletedOption = $request->input('deletedOption');
    
        $jsonFile = public_path('chatbot_data.json');
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);
    
        if (isset($data[$selectedOption][$deletedOption])) {
            unset($data[$selectedOption][$deletedOption]);
    
            file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
    
            return response()->json(['message' => 'Xóa câu hỏi và câu trả lời thành công']);
        } else {
            return response()->json(['message' => 'Lựa chọn cha hoặc lựa chọn cần xóa không tồn tại']);
        }
    }

}
