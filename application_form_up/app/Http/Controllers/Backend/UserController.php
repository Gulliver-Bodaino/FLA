<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests\SaveUser;
use App\Http\Requests\DeleteUser;

use App\Models\User;

use Hash;

// use Auth;
// use Redirect;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware(function ($request, $next) {
    //         $this->user = Auth::guard('backend')->user();
    //         if ($this->user->privilege !== 1) {
    //             Redirect::to('backend/home')->send();
    //         }

    //         return $next($request);
    //     });
    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::query();

        $data = [
//            'request' => $request,
            'users' => $users->paginate(20),
        ];

        return view('backend.user.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();

        $data = [
            'title' => '新規登録',
            'action' => route('backend.users.store'),
            'method' => 'POST',
            'user' => $user,
        ];

        return view('backend.user.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveUser $request)
    {
        $user = new User();

        $merge = [
            'password' => Hash::make($request->password),
        ];

        $request->merge($merge);

        $user->fill($request->all())->save();

        return redirect()->route('backend.users.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $data = [
            'title' => '編集',
            'action' => route('backend.users.update', $id),
            'method' => 'PUT',
            'user' => $user,
        ];

        return view('backend.user.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SaveUser $request, $id)
    {
        $user = User::findOrFail($id);

        $merge = [];

        // パスワード
        if ($request->filled('password')) {
            $merge['password'] = Hash::make($request->password);
        } else {
            $merge['password'] = $user->password;
        }

        $request->merge($merge);

        $user->fill($request->all())->save();

        return redirect()->route('backend.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeleteUser $request, $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return new Response('', 204);
    }
}
