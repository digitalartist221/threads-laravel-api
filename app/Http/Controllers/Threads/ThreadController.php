<?php

namespace App\Http\Controllers\Threads;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThreadRequest;
use App\Http\Resources\ThreadResource;
use App\Models\Comment;
use App\Models\Like;
use App\Models\SubComment;
use App\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThreadController extends Controller
{   

    public function index(){
        try {
            // Authenticate the user using Sanctum token
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response([
                    'message' => 'Unauthorized. Invalid token.',
                ], 401);
            }
            //code...
            $threads = Thread::with('user')->with('likes')->with('comments')->latest()->get();
            $threads = ThreadResource::collection($threads);
            response([
                'threads' => $threads
            ]);
        } catch (\Exception $e) {
            //throw $th;
            return response([
                'message' =>  $e->getMessage(),
            ], 500);
        }
    }
    //
    public function store(ThreadRequest $threadRequest){


        try {

            // Authenticate the user using Sanctum token
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                return response([
                    'message' => 'Unauthorized. Invalid token.',
                ], 401);
            }
            $validatedData = $threadRequest->validated();

            $data = [
                'body' => $validatedData['body']
            ];
            
            // New thread instance
            $thread = new Thread();
            
            // Check if the request contains an image file
            if ($threadRequest->hasFile('image')) {
                // Validate that the image is indeed an image file
                $threadRequest->validate([
                    'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048' // Adding more validations (optional)
                ]);
            
                $imagePath = 'public/images/threads';
                $image = $threadRequest->file('image');
                $image_name = time() . '_' . $image->getClientOriginalName(); // Using time() to avoid name collision
                $path = $image->storeAs($imagePath, $image_name);
            
                $data['image'] = $path;
            }
            
            // Assign the validated data to the thread
            $thread->body = $data['body'];
            $thread->image = $data['image'] ?? null; // Assign the image if it exists
            $thread->user_id = $user->id;
            
            // Save the thread and return a response
            if ($thread->save()) {
                return response([
                    'message' => 'success',
                ], 201);
            } else {
                return response([
                    'message' => 'error',
                ], 500); // Change to 500 as it's an internal server error
            }
            
            
        } catch (\Exception $e) {
            //throw $th;
            return response([
                'message' =>  $e->getMessage(),
            ], 500);
        }


    }

    public function react($thread_id){
        try{

            $thread = Like::whereThreadId($thread_id)->whereUserId(auth()->id())->first();

            if ($thread) {
                Like::whereThreadId($thread_id)->whereUserId(auth()->id())->delete();
                return response([
                    'message' => 'unliked',
                ], 201);
            }else{
                Like::create(
                    [
                        'user_id' => auth()->id(),
                        'thread_id' =>$thread_id
                    ]
                    );
                
                    return response([
                        'message' => 'liked',
                    ], 201);
            }

        } catch (\Exception $e) {
            //throw $th;
            return response([
                'message' =>  $e->getMessage(),
            ], 500);
        }
    }

    public function comment(Request $request)  {

        try{
            $request->validate([
                'thread_id' => 'required',
                'body' => 'required|string'
            ]);

            $comment = Comment::create([
                'user_id' => auth()->id(),
                'thread_id' => $request->thread_id,
                'body' => $request->body
            ]);

            if($comment){
                return response([
                    'message' => 'success',
                ], 201);
            }else{
                
                return response([
                    'message' => 'error',
                ], 201);
            }

        } catch (\Exception $e) {
            //throw $th;
            return response([
                'message' =>  $e->getMessage(),
            ], 500);
        }
        
    }


    
    public function subcomment(Request $request)  {

        try{
            $request->validate([
                'comment_id' => 'required',
                'body' => 'required|string'
            ]);

            $subcomment = SubComment::create([
                'user_id' => auth()->id(),
                'comment_id' => $request->comment_id,
                'body' => $request->body
            ]);

            if($subcomment){
                return response([
                    'message' => 'success',
                ], 201);
            }else{
                
                return response([
                    'message' => 'error',
                ], 201);
            }

        } catch (\Exception $e) {
            //throw $th;
            return response([
                'message' =>  $e->getMessage(),
            ], 500);
        }
        
    }
}
