<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function createTask(Request $request) {
        $rules = [
            'task_name' => 'required|max:100',
          ];
         
          $messages = ['required' => '必須項目です', 'max' => '100文字以下にしてください。'];
         
          Validator::make($request->all(), $rules, $messages)->validate();
         
          //モデルをインスタンス化
          $task = new Task;
         
          //モデル->カラム名 = 値 で、データを割り当てる
          $task->name = $request->input('task_name');
         
          //データベースに保存
          $task->save();
         
          //レスポンス
          return response()->json([
            "name" => $task->name
          ], 201);
    }

    public function getAllTasks() {
        $tasks = Task::where('status', '=', '0')->get()->toJson(JSON_PRETTY_PRINT);
        return response($tasks, 200);
    }
    
    public function getTask($id) {
        $task = Task::where('id', $id)->get()->toJson(JSON_PRETTY_PRINT);
        return response($task, 200);
    }

    
    public function updateTask(Request $request, $id) {
      
      // バリデーションルールとメッセージの設定
      $rules = [
          'task_name' => 'required|max:100',
      ];
  
      $messages = [
          'required' => '必須項目です',
          'max' => '100文字以下にしてください。',
      ];
      // dd($request->all());
      // バリデーション実行
      $validator = Validator::make($request->all(), $rules, $messages);
      if ($validator->fails()) {
          return response()->json($validator->errors(), 400); // バリデーションエラーがある場合のレスポンス
      }
  
      // 該当のタスクを検索
      $task = Task::find($id);
  
      if (!$task) {
          return response()->json(['message' => '指定されたタスクが見つかりません'], 404); // タスクが見つからない場合のレスポンス
      }
  
      // タスク名の更新
      $task->name = is_null($request->input('task_name')) ? $task->name : $request->input('task_name');
  
      // データベースに保存
      $task->save();
  
      return response()->json([
          "name" => $task->name
      ], 200);
    }
    
    public function deleteTask($id) {
      $task = Task::find($id);
      $task->delete();
      return response()->json([
        "message" => "records deleted"
      ], 202);
    }

    public function completeTask($id) {
      $task = Task::find($id);

      if (!$task) {
        // タスクが存在しない場合のエラーハンドリング
        return response()->json(['error' => 'タスクが見つかりませんでした'], 404);
    }

      $task->status = 1;
      $task->save();
      return response()->json([
        "status" => $task->status
      ], 200);
    }
}
