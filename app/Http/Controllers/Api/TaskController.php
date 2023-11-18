<?php

namespace App\Http\Controllers\Api;

use App\Events\TaskCreated;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller

{
    private $sucess_status = 200;

/**
 * Get Create of Todo
 * @OA\Info(
 *     version="1.0",
 *     title="CRUD Tasks"
 * ),
 * @OA\PathItem(path="/api"),
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="bearerAuth",
 *         type="http",
 *         scheme="bearer",
 *     )
 * ),
 * @OA\Post(
 *     path="/api/create-task",
 *     @OA\OpenApi(
 *         security={{"bearerAuth": {}}}
 *     ),
 *     summary="Add a new task",
 *     tags={"Task"},
 *     @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="title",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="description",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="user_id",
 *                     type="integer"
 *                 ),
 *                 example={"title": "task1", "description": "buy food"}
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\Property(property="data", type="array",
 *             @OA\Property(property="title", type="string", example="some title"),
 *             @OA\Property(property="description", type="string", example="some description"),
 *         )
 *     )
 * ),
 **/
    public function createTask(Request $request)
    {
        $user = Auth::user();
        $validator  = Validator::make(
            $request->all(),
            [
                "title"  =>  "required",
                "description" =>  "required",
            ]
        );
        if ($validator->fails()) {
            return response()->json(["validation_errors" => $validator->errors()]);
        }

        $task_array = array(
            "title"  =>  $request->title,
            "description" =>  $request->description,
            "status"  =>  $request->status,
            "user_id" =>  $user->id
        );

        $task_id  = $request->task_id;

        if ($task_id != "") {
            $task_status  = Task::where("id", $task_id)->update($task_array);

            if ($task_status == 1) {
                return response()->json(["status" => $this->sucess_status, "success" => true, "message" => "To-do updated successfully", "data" => $task_array]);
            } else {
                return response()->json(["status" => $this->sucess_status, "success" => true, "message" => "To-do not updated"]);
            }
        }

        $task = Task::create($task_array);

        if (!is_null($task)) {
            event(new TaskCreated($task));
            return response()->json(["status" => $this->sucess_status, "success" => true, "data" => $task]);
        } else {
            return response()->json(["status" => "failed", "success" => false, "message" => "Task not created."]);
        }
    }


/**
* Get list of Todo
* @OA\Get(
*    path="/api/tasks",
*    @OA\OpenApi(
*         security={{"bearerAuth": {}}}
*     ),
*    tags={"Task"},
*    summary="Get list of Tasks",
*    description="Get list of Tasks",
*    @OA\Response(
*          response=200, description="Success",
*    @OA\JsonContent(
*        @OA\Property(
*            property="data",
*            type="array",
*            description="List of Tasks",
*            @OA\Items(
*                @OA\Property(
*                    property="id",
*                    type="integer",
*                    example="1"
*                ),
*                @OA\Property(
*                    property="user_id",
*                    type="integer",
*                    example="1"
*                ),
*                @OA\Property(
*                    property="title",
*                    type="string",
*                    example="string"
*                ),
*                @OA\Property(
*                    property="description",
*                    type="string",
*                    example="string"
*                ),
*       )
*    )
*  )
*       )
*  )
*/

    public function tasks()
    {
        $tasks =  array();
        $user = Auth::user();
        if ($user->role == "Administrator") {
            $tasks = Task::all();
        } else {
            $tasks = Task::where("user_id", $user->id)->get();
        }
        if (count($tasks) > 0) {
            return response()->json(["status" => $this->sucess_status, "success" => true, "count" => count($tasks), "data" => $tasks]);
        } else {
            return response()->json(["status" => "failed", "success" => false, "message" => "To-do not found"]);
        }
    }


      /**
     * Get Detail Todo
     * @OA\Get (
     *     path="/api/task/{task_id}",
     *     tags={"Task"},
     *     @OA\OpenApi(
     *         security={{"bearerAuth": {}}}
     *     ),
     *     @OA\Parameter(
     *         in="path",
     *         name="task_id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="title", type="string", example="title"),
     *              @OA\Property(property="description", type="string", example="content"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *         )
     *     )
     * )
     */

    public function task($task_id)
    {
        if ($task_id == 'undefined' || $task_id == "") {
            return response()->json(["status" => "failed", "success" => false, "message" => "Enter the task id"]);
        }
        $user = Auth::user();
        $task = Task::find($task_id);

        if (!is_null($task && ($user->id == $task->user_id || $user->role == "Administrator"))) {
            return response()->json(["status" => $this->sucess_status, "success" => true, "data" => $task]);
        } else {
            return response()->json(["status" => "failed", "success" => false, "message" => "To-do not found"]);
        }
    }

        /**
     * Update Todo
     * @OA\Put (
     *     path="/api/task/{task_id}",
     *      @OA\OpenApi(
     *         security={{"bearerAuth": {}}}
     *     ),
     *     tags={"Task"},
     *     @OA\Parameter(
     *         in="path",
     *         name="task_id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="title",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="description",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "title":"example title",
     *                     "description":"example content"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="title", type="string", example="title"),
     *              @OA\Property(property="description", type="string", example="content"),
     *              @OA\Property(property="updated_at", type="string", example="2021-12-11T09:25:53.000000Z"),
     *              @OA\Property(property="created_at", type="string", example="2021-12-11T09:25:53.000000Z")
     *          )
     *      )
     * )
     */

    public function updateTask(Request $request, $task_id)
    {
        $input = $request->all();
        $user = Auth::user();
        $task = Task::find($task_id);
        $validator = Validator::make($input, [
            'title' => 'required',
            'description' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["validation_errors" => $validator->errors()]);
        }
        if ($user->id == $task->user_id || $user->role == "Administrator") {
            $task->title = $input['title'];
            $task->description = $input['description'];
            $task->save();
        } else {
            return response()->json(["status" => "failed", "success" => false, "message" => "To-do not update"]);
        }

        return response()->json([
            "success" => true,
            "message" => "Task updated successfully.",
            "data" => $task
        ]);
    }
       /**
     * Delete Todo
     * @OA\Delete (
     *     path="/api/task/{task_id}",
     *     @OA\OpenApi(
     *         security={{"bearerAuth": {}}}
     *     ),
     *     tags={"Task"},
     *         @OA\Parameter(
     *         in="path",
     *         name="task_id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *         @OA\JsonContent(
     *             @OA\Property(property="msg", type="string", example="delete todo success")
     *         )
     *     )
     * )
     */

    public function deleteTask($task_id)
    {
        if ($task_id == 'undefined' || $task_id == "") {
            return response()->json(["status" => "failed", "success" => false, "message" => "Enter the task id"]);
        }

        $task = Task::find($task_id);
        $user = Auth::user();
        if (!is_null($task)) {

            $delete_status  = Task::where("id", $task_id)->delete();

            if ($delete_status == 1 && ($user->id == $task->user_id || $user->role == "Administrator")) {

                return response()->json(["status" => $this->sucess_status, "success" => true, "message" => "Success! to-do deleted"]);
            } else {
                return response()->json(["status" => "failed", "success" => false, "message" => "To-do not deleted"]);
            }
        } else {
            return response()->json(["status" => "failed", "success" => false, "message" => "To-do not found"]);
        }
    }
}
