<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validation = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
        ];

        $this->validate($request, $validation);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $id = auth()->user()->id;
        $validation = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
        ];

        $this->validate($request, $validation);

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->password) {



            if (!password_verify($request->old_password, $user->password)) {
                return response()->json([
                    'errors' => [
                        'old_password' => [
                            'old password not match'
                        ]
                    ]

                ], 422);
            }


            $request->validate(['password' => 'min:6', 'password_confirm' => 'same:password']);
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }
}
