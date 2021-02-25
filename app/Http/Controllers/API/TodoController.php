<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function index()
    {
        return Todo::where('is_complete', 0)->paginate(3);
    }

    public function store(Request $request)
    {
        $newTodo = new Todo([
            'task' => $request->get('todo'),
        ]);
        $newTodo->save();
    }

    public function completedTasks()
    {
        return Todo::where('is_complete', 1)->get();
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::find($id);
        if ($request->get('check')) {
            $todo->is_complete = 1;
        } else {
            $todo->task = $request->get('todo');
        }
        $todo->save();
    }

    public function destroy($id)
    {
        Todo::find($id)->delete();
    }
}
