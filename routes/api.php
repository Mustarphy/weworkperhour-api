<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\UploadsController;
use App\Http\Controllers\Candidate\AppliedJobsController;
use App\Http\Controllers\SkillstampController;
use App\Http\Controllers\AdminEmployerController;
use App\Http\Controllers\AdminFreelancerController;
use App\Http\Controllers\AdminJobController;
// use App\Http\Controllers\Employer\BrowseCandidatesController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmartGuideController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\EmployerPaymentController;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => 'XssSanitizer'], function () {
    Route::group(['middleware' => 'api', 'prefix' => 'v1'], function ($router) {

        Route::get('test-auth', function (Request $request) {
            return response()->json([
                'auth_user' => auth()->user() ? auth()->user()->id : null,
                'request_user' => $request->user() ? $request->user()->id : null,
            ]);
        })->middleware(['jwt.verify']);

        // Broadcast routes inside the v1 prefix group
        Route::post('broadcasting/auth', function (Request $request) {
            $user = auth()->user();
            $channelName = $request->input('channel_name');

            preg_match('/chat\.(\d+)/', $channelName, $matches);
            $chatId = $matches[1] ?? null;

            if (!$user) {
                return response()->json([
                    'error' => 'No authenticated user',
                    'debug' => [
                        'auth_user' => null,
                        'has_token' => $request->header('Authorization') ? true : false,
                        'channel' => $channelName,
                    ]
                ], 403);
            }

            $chat = \App\Models\Chat::find($chatId);
            if (!$chat) {
                return response()->json([
                    'error' => 'Chat not found',
                    'debug' => [
                        'user_id' => $user->id,
                        'chat_id' => $chatId,
                    ]
                ], 403);
            }
            
            $hasAccess = (int) $user->id === (int) $chat->user1 || 
                         (int) $user->id === (int) $chat->user2;
            
            if (!$hasAccess) {
                return response()->json([
                    'error' => 'User not authorized for this chat',
                    'debug' => [
                        'user_id' => $user->id,
                        'chat_id' => $chatId,
                        'chat_user1' => $chat->user1,
                        'chat_user2' => $chat->user2,
                    ]
                ], 403);
            }
            
            return Broadcast::auth($request);
        })->middleware(['jwt.verify']);

        Route::controller(AuthController::class)->group(function() {
            Route::post('/register', 'register');
            Route::post('/login', 'login');
            Route::get('/login', function() {
                return errorResponse("Unauthenticated", [], 321);
            })->name("login");

            Route::post('/forgot-password', 'forgotPassword');
            Route::post('/verify-token', 'verifyToken');
            Route::post('reset-password', 'resetPassword');
            Route::post('resend-otp', 'resendOtp');
            
            Route::middleware(['verified', 'jwt.verify'])->group(function () {
                Route::post('/logout', 'logout');
                Route::post('/change-password', 'changePassword');
            });
        });
        Route::get('/resources', [ResourceController::class, 'resource']);
        Route::get('/jobs', [JobsController::class, 'index']);
        Route::get('/jobs-alert', [JobsController::class, 'alert']);
        Route::post('/apply-job/{jobId}', [JobsController::class, 'applyJob']);
        // Route::get('/jobs/homepage', [JobsController::class, 'homepage']);
        Route::get('/jobs/saved', [JobsController::class, 'saved']);
        Route::post('/jobs/saved/{id}', [JobsController::class, 'savedPost']);
        Route::post('/jobs/saved/delete/{id}', [JobsController::class, 'deletesaved']);
        Route::get('/jobs/{slug}', [JobsController::class, 'show']);
        Route::get('/job/share/{id}', [JobsController::class, 'shareJob']);
        
        Route::get('/jobs/similar/{slug}', [JobsController::class, 'similarJobs']);
        Route::get('/fetch-job-types', [JobsController::class, 'fetchJobTypes']);
        
        Route::prefix("upload")->group(function() {
            Route::post('/file', [UploadsController::class, 'uploadFile']);
        });
        Route::prefix("countries")->group(function() {
            Route::get('/', [CountryController::class, 'index']);
            Route::get('/{code}', [CountryController::class, 'show']);
        });



        Route::middleware(['verified', 'jwt.verify', 'auth:api'])->group(function () {
            Route::controller(UserController::class)->group(function () {
                Route::get('/users', 'index');
                Route::post('/user/change-password', 'changePassword');
                Route::post('/user/delete', 'deleteAccount');

                Route::get('/profile', 'show');
                Route::post('/profile', 'update');
                Route::get('/profile/delete-avatar', 'deleteAvatar');
                Route::post('/profile/social/{id}', 'updateSocial');
                Route::post('/profile/social-delete/{id}', 'deleteSocial');
                Route::post('/profile/social-add', 'addSocial');
                Route::get('/smartguide', [SmartGuideController::class, 'show']);
                Route::post('/smartguide', [SmartGuideController::class, 'store']);
                Route::get('/smartguide/{guideId}', [SmartGuideController::class, 'showGuideContent']);
                Route::post('/smartguide/{guideId}/progress', [SmartGuideController::class, 'updateProgress']);
                Route::post('/cv', [CvController::class, 'generate']);

                Route::post('/skillstamp/award', [SkillstampController::class, 'award']);

                Route::post('/profile/update-smartcv', [UserController::class, 'updateSmartCv']);

                Route::get('/candidate/wallet/{userId}', [WalletController::class, 'getWallet']);
    Route::post('/candidate/wallet/generate-token', [WalletController::class, 'generateToken']);
            });

            // Employer Payment Routes
            Route::prefix('employer')->group(function () {
                Route::post('/validate-token', [EmployerPaymentController::class, 'validateToken']);
                Route::post('/fund-wallet', [EmployerPaymentController::class, 'fundWallet']);
                Route::post('/create-milestones', [EmployerPaymentController::class, 'createMilestones']);
                Route::post('/confirm-payment', [EmployerPaymentController::class, 'confirmPayment']);
                Route::get('/payments', [EmployerPaymentController::class, 'getPayments']);
                Route::post('/verify-payment', [EmployerPaymentController::class, 'verifyPayment']);
            });





            Route::get('candidate/applied-jobs', [AppliedJobsController::class, 'index']);

            Route::prefix("/resume")->controller(ResumeController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('update', 'update');
                Route::post('remove-resume', 'removeResume');
                Route::post('update/intro', 'updateIntro');
                Route::get('delete-portolio/{id}', 'deletePortfolio');

            });

            Route::prefix('candidates')->controller(\App\Http\Controllers\Candidate\CandidateController::class)->group(function () {
                Route::get('/{id}', 'show');
                Route::post('/update', 'update');
            });

            Route::prefix("/chat")->controller(ChatController::class)->group(function () {
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::post('send-chat', 'sendMessage');
            });

        });

        Route::get('/paystack/public-key', function () {
            return response()->json([
                'public_key' => config('paystack.public_key'),
            ]);
        });
        
    });

});

 Route::middleware(['api_key'])->group(function () {
    Route::get('/admin/employers', [AdminEmployerController::class, 'index']);
    Route::get('/admin/freelancers', [AdminFreelancerController::class, 'index']);
    
});

Route::get('/admin/jobs', [AdminJobController::class, 'index']);
