<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function show($id)
    {
        $data = User::join('sub_plans', 'sub_plans.sub_id', 'users.plan_id')
            ->where('users.id', $id)
            ->first();
        return response()->json($data);
    }

    public function update(Request $request, $id)  {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'telp' => 'required'
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        $data = User::find($id);

        if($request->hasFile('image')){
            if($data->image){
                $imagePath = public_path('user/') . $data->image;
                if (file_exists($imagePath)){
                    unlink($imagePath);
                }
                $image = $request->file('image');
                $imageName = time() . $image->getClientOriginalExtension();
                $image->move(public_path('user'), $imageName);
                $data->image = $imageName;
            }
        }
        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = $request->password;
        $data->telp = $request->telp;
        $data->save();

        return response()->json($data);


    }
}
