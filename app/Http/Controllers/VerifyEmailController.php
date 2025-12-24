<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerifyEmailController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        // try {
        //     $user = User::find($id);
        // } catch (\Exception $e) {
        //     return response()->json(['message' => $e->getMessage()]);
        // }

        // if (!isset($user)) {
        //     return response()->json(['message' => 'No user record']);
        // }
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403, 'Invalid verification link');
        }

        if ($user->hasVerifiedEmail()) {
                return view('verified.email');
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return view('verified.email');
    }
}
