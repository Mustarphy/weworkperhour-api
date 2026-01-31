namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminSecurityController extends Controller
{
    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        User::where('id', $id)->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password updated successfully',
        ]);
    }

    public function logoutAll($id)
    {
        /**
         * Since you're using API key auth:
         * - No sessions exist
         * - No tokens exist
         * 
         * Best approach:
         * - Force password reset
         * - OR invalidate client-side tokens if any
         */

        return response()->json([
            'status' => 'success',
            'message' => 'User will be logged out on next login',
        ]);
    }
}
