<?php

namespace App\Http\Controllers;

use App\Models\Convarsetion;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function getMessage (Request $request, $teacher_id) {
        $datas = [];
        $con = Convarsetion::where('user_id', auth()->id())
                           ->where('teacher_id', $teacher_id)->first();
        $messages = Message::where('convarsation_id', $con->id)->latest()->get();
        foreach ($messages as $message) {
            $datas[] = [
                'text' => $message->text,
                'user_id' => $message->user_id
            ];
        }
        return response()->json($datas, 200);
    }
    public function getMessageWithStudentId (Request $request, $student_id) {
        $datas = [];

        $con = Convarsetion::where('user_id', $student_id)
                           ->where('teacher_id', auth()->id())->first();

        $messages = Message::where('convarsation_id', $con->id)->latest()->get();

        foreach ($messages as $message) {
            $datas[] = [
                'text' => $message->text,
                'user_id' => $message->user_id
            ];
        }
        return response()->json($datas, 200);
    }
    public function sendMessage (Request $request) {

        $this->validate($request, [
            'text' => 'required',
            'student_id' =>'required',
        ]);

        $con = Convarsetion::firstOrCreate([
            'user_id' => $request->student_id,
            'teacher_id' => auth()->id()
        ]);

        Message::create([
            'convarsation_id' => $con->id,
            'text' => $request->text,
            'user_id' => auth()->id(),
        ]);

        return response()->json(['success' => 'success'], 200);

    }
    public function sendMessageFromStudent (Request $request) {
        
        $this->validate($request, [
            'text' => 'required',
            'teacher_id' =>'required',
        ]);

        DB::beginTransaction();
        try {
            $con = Convarsetion::firstOrCreate([
                'user_id' => auth()->id(),
                'teacher_id' => $request->teacher_id
            ]);

            Message::create([
                'convarsation_id' => $con->id,
                'text' => $request->text,
                'user_id' => auth()->id(),
            ]);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error' => $ex->getMessage()], 500);
        }
        

        return response()->json(['success' => 'success'], 200);

    }


}
