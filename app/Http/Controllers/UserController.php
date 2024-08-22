<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Country;
use App\Models\SocialMedia;
use App\Models\States;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api');
    // }
    
    /**
     * @OA\Get(
     *      path="/api/v1/users",
     *      operationId="index",
     *      tags={"Users"},
     *      summary="Get list of users",
     *      description="Returns list of users",
     *      security={{ "apiAuth": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *       @OA\Response(
     *          response=404,
     *          description="Not found"
     *      ),
     *     )
     *
     * Returns list of users
     */
    public function index()
    {
        try {
            $users = User::all();
            return response()->json(['users' => $users]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }


    public function update(Request $request)
    {
        $this->validate($request, [
            
        ]);
        $user = auth()->user();
        if($request->file("file")){
            $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
            $request->file('file')->move(public_path('uploads'), $fileName);
            $user->avatar = "/uploads/".$fileName;
        }
        if($request->country) {
            $isCountry = Country::where("code", $request->country)->first();
            if($isCountry) {
                $user->country = $request->code;
            }
        }

        if($request->state) {
            $isState = States::where("country_code", $request->country)->where("name", $request->state)->first();
            if($isState) {
                $user->state = $request->state;
            }
        }

        $user->name = $request->name ? $request->name : $user->name;
        $user->bio = $request->bio ? $request->bio : $user->bio;
        $user->city = $request->city ? $request->city : $user->city;
        $user->phone_no = $request->phone_no ? $request->phone_no : $user->phone_no;
        $user->save();
        return okResponse("profile updated");
    }

    public function show()
    {
        $id = auth()->id();
        $user = User::with(["socials", "Role"])->where("id", $id)->first();
        return new UserResource($user);
    }

    public function deleteAvatar()
    {
        $id = auth()->id();
        $user = auth()->user();
        if (File::exists(public_path($user->avatar))) {
            // Delete the file
            File::delete(public_path($user->avatar));
        }
        $user->avatar = null;
        $user->save();
        $user = User::with(["socials", "Role"])->where("id", $id)->first();
        return new UserResource($user);
    }
    public function updateSocial(Request $request, $id) {
        $val = $request->value;
        $soc = SocialMedia::where("user_id", auth()->id())->where("id", $id)->first();
        $soc->value = $val;
        $soc->save();
        return okResponse("success");
    }

    public function deleteSocial(Request $request, $id) {
        SocialMedia::where("user_id", auth()->id())->where("id", $id)->delete();
        return okResponse("success");
    }

    public function addSocial(Request $request) {
        SocialMedia::create([
            "label" => $request->label,
            "value" => $request->value,
            "user_id" => auth()->id()
        ]);
        return okResponse("added");
    }

    public function changePassword(Request $request) {
        $validated = $request->validate(['current_password' => "required", 'password' => 'required|string|confirmed|min:8']);
        try {
            $user= User::where('email', auth()->user()->email)->first();
            if(!$user){
                return errorResponse("User not found");
            }
            if(!Hash::check($validated["current_password"], $user->password))return errorResponse("Current password is invalid");
            User::where('email', auth()->user()->email)
                ->update(['password' => bcrypt($validated['password'])]);
                return okResponse("Password updated");
        } catch (\Exception $e) {
            return response()->json(['message' => 'Password update failed']);
        }
    }
    public function deleteAccount(Request $request) {
        $user= User::where('email', auth()->user()->email)->first();
        $user->status = "deleted";
        $user->save();
        return okResponse("Account deleted");
    }
}
