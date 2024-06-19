<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    // index
    public function index(Request $request)
    {
        // Get users with pagination
        // $users = User::paginate(6);
        $users = DB::table('users')
            ->when($request->input('name'), function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->paginate(5);

        return view('pages.user.index', compact('users'));
    }

    // create
    public function create()
    {
        return view('pages.user.create');
    }

    // store
    public function store(Request $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($request->input('password'));
        User::create($data);
        return redirect()->route('user.index');
    }

    // show
    public function show($id)
    {
        return view('pages.dashboard');
    }

    //edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.user.edit', compact('user'));
    }

    // update
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $user = User::findOrFail($id);
        //check if password is not empty
        if ($request->input('password')) {
            $data['password'] = Hash::make($request->input('password'));
        } else {
            //if password is empty, then use the old password
            $data['password'] = $user->password;
        }
        $user->update($data);

        Log::info('User after update', ['user' => $user]);
        return redirect()->route('user.index');
    }

    // destroy
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('user.index');
    }

    // Change Password
    // public function changePassword(Request $request)
    // {
    //     $request->validate([
    //         'current_password' => 'required',
    //         'new_password' => 'required|string|min:8|confirmed',
    //     ]);

    //     $currentUser = Auth::user();

    //     if (!(Hash::check($request->current_password, $currentUser->password))) {
    //         return response()->json(
    //             [
    //                 'message' => 'Password Anda Salah',
    //                 'errors' => [
    //                     'current_password' => ['Password Akun Anda tidak sesuai dengan Password yang Anda berikan.']
    //                 ]
    //             ],
    //             401
    //         );
    //     }

    //     $currentUser->password = Hash::make($request->new_password);
    //     $currentUser->save();

    //     return response()->json(['message' => 'Password berhasil dirubah !']);
    // }

    // Update Data
    public function updateData(Request $request, $id)
    {
        // $user = Auth::user();
        // $user = Auth::user()($id);

        $data = $request->all();
        $user = User::findOrFail($id);

        $validator = Validator::make($data, [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:18',
            'address' => 'sometimes|string|max:200',
            // 'roles' => 'sometimes|string',
            // Tambahkan validasi lainnya sesuai kebutuhan
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // $user->update($data);
        $user->fill($request->only('name', 'email', 'phone', 'address'));
        $user->save();
        Log::info('User after update', ['user' => $user]);

        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
    }
}
