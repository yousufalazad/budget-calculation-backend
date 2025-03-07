<?php

namespace App\Http\Controllers;

use App\Mail\SuperAdminUserRegisteredMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\IndividualUserRegisteredMail;
use App\Mail\OrgUserRegisteredMail;
// use App\Http\Controllers\Validator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Http\Response;





class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8',
            'type' => 'required|string|in:individual,superadmin,guest'
        ]);

        // Create a new user record
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'type' => $request->type,
            'password' => Hash::make($request->password),
            // 'azon_id' => $request->azon_id,
        ]);

        // Add default management and storage subscription for organisation users
        // if ($request->type == 'individual') {
        //     $user->managementSubscription()->create([
        //         'user_id' => $user->user_id,
        //         'management_package_id' => 1, // Default management package ID
        //         'start_date' => now(),
        //         'subscription_status' => 'active',
        //         'is_active' => 1,
        //         'created_at' => now(),
        //     ]);

        //     $user->storageSubscription()->create([
        //         'user_id' => $user->user_id,
        //         'storage_package_id' => 1, // Default storage package ID
        //         'start_date' => now(),
        //         'subscription_status' => 'active',
        //         'is_active' => 1,
        //         'created_at' => now(),
        //     ]);
        // }

        // Send email function call
        // $this->sendEmail($user);

        // Return a success response
        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'data' => $user
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_token' => 'required|boolean',
        ]);

        $credentials = request(['email', 'password']);
        $remember_token = $request->remember_token;
        if (!Auth::attempt($credentials, $remember_token)) {
            return $this->error('Unauthorized user');
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return $this->success(message: 'Successfully logged in', data: [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'type' => $user->type,
            'azon_id' => $user->azon_id,
            'username' => $user->username,

            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,

            'accessToken' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    // public function sendEmail($user)
    // {
    //     if ($user->type == 'individual') {
    //         Mail::to($user->email)->send(new IndividualUserRegisteredMail($user));
    //     } elseif ($user->type == 'organisation') {
    //         Mail::to($user->email)->send(new OrgUserRegisteredMail($user));
    //     } elseif ($user->type == 'superadmin') {
    //         Mail::to($user->email)->send(new SuperAdminUserRegisteredMail($user));
    //     }
    // }

    // Method to handle successful responses
    protected function success($message, $data = [], $status = 200)
    {
        return response()->json(data: [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], status: $status);
    }

    // Method to handle error responses
    protected function error($message, $errors = [], $status = 422)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    public function verify($uuid)
    {
        // Find the user by UUID in the database
        $user = User::where('verification_token', $uuid)->first();

        if (!$user) {
            return redirect('/')->with('error', 'Invalid verification link.');
        }

        // Update the email_verified_at column
        $user->email_verified_at = Carbon::now();
        $user->verification_token = null; // Optionally remove the token
        $user->save();

        return redirect('/')->with('success', 'Your email has been verified!');
    }

    // Method to handle register user
    

    //WHY THIS FUNCTION??????????
    // public function user(Request $request)
    // {
    //     return response()->json($request->user());
    // }


    // No need this function for updating localStorage data
    // public function getUserDataLocalUpdate($userId){

    //     $user = User::find($userId);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'User all updated data',
    //         'data' => $user
    //     ]);
    // }

    // public function nameUpdate(Request $request, $userId){
    //     $request->validate([
    //         'name' =>'required|string|max:100',
    //     ]);
    //     // $id=Auth::user()->id;
    //     // $id = Auth::id();
    //     $user = User::where('id', $userId)->first();
    //     $user->name = $request->name;
    //     $user->save();

    //     return response()->json([
    //        'status' => true,
    //        'message' => 'Name updated successfully',
    //         'data' => $user
    //     ]);
    // }



    public function nameUpdate(Request $request, $userId)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        try {
            // Find the user or throw a 404 error if not found
            $user = User::findOrFail($userId);

            // Optional: Ensure only authorized users can update their name (add your own authorization logic here)
            // if (auth()->id() !== $user->id) {
            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Unauthorized access',
            //     ], 403); // 403 Forbidden status
            // }

            // Update the user's name
            $user->name = $validated['name'];
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Name updated successfully',
                'data' => $user
            ], 200); // 200 OK status

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404); // 404 Not Found status
        } catch (\Exception $e) {
            // Handle any other exception
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the name',
                'error' => $e->getMessage(),
            ], 500); // 500 Internal Server Error status
        }
    }


    public function usernameUpdate(Request $request, $userId)
    {
        // Validate that the username is required, a string, max 30 characters, and unique
        $request->validate([
            'username' => 'required|string|max:30|unique:users,username,' . $userId,
        ]);
        // $id=Auth::user()->id;
        // $id = Auth::id();
        
        // Retrieve the user by ID
        $user = User::findOrFail($userId);

        // Update the username
        $user->username = $request->username;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Username updated successfully',
            'data' => $user
        ]);
    }

    public function userEmailUpdate(Request $request, $userId)
    {
        $request->validate([
            'email' => 'required|string|max:100',
        ]);

        $user = User::where('id', $userId)->first();
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Email updated successfully',
            'data' => $user
        ]);
    }

    //     public function updatePassword(Request $request, $userId)
    // {
    //     // Validate the request
    //     $validator = Validator::make($request->all(), [
    //         'old_password' => 'required|string',
    //         'password' => 'required|string|min:8|confirmed', // Ensure password confirmation matches
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     try {
    //        // Check if the authenticated user matches the $userId or is authorized to update
    //         if (auth()->id() != $userId) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Unauthorized to update this user\'s password.',
    //             ], 403); // Forbidden status code
    //         }

    //         // Find the user by ID
    //         $user = User::findOrFail($userId); // Throws an exception if user is not found

    //         // Check if the old password matches
    //         if (!Hash::check($request->old_password, $user->password)) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'The current password is incorrect.',
    //             ], 422);
    //         }

    //         // Update the user's password
    //         $user->password = Hash::make($request->password);
    //         $user->save();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Password updated successfully.',
    //         ], 200);

    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         // Handle user not found
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found.',
    //         ], 422);
    //     } catch (\Exception $e) {
    //         // Handle any other exception that occurs during the process
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An error occurred while updating the password: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

    //----------------------------------------------------------------

    //Password update
    // public function updatePassword(Request $request, $userId)
    // {
    //     // Validate the request
    //     $validator = Validator::make($request->all(), [
    //         'password' => 'required|string|min:8',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     try {
    //         // Find the user by ID
    //         $user = User::findOrFail($userId); // This will throw an exception if the user is not found

    //         // Update the user's password
    //         $user->password = Hash::make($request->password);
    //         $user->save();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Password updated successfully.',
    //         ], 200);
    //     } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
    //         // Handle user not found
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'User not found.',
    //         ], 422);
    //     } catch (\Exception $e) {
    //         // Handle any other exception that occurs during the process
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'An error occurred while updating the password: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

    //----------------------------------------------------------------
    //not checking $userId vs auth()

    public function updatePassword(Request $request, $userId)
    {
        // Validate the request
        // $validator = Validator::make($request->all(), [
        //     'old_password' => 'required|string|min:8',
        //     'password' => 'required|string|min:8|confirmed', // Use 'confirmed' to ensure password confirmation matches
        // ]);
        // if ($validator->fails()) {
        //     return $this->error('Validation Error', $validator->errors());
        // }


        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            // 'old_password' => 'required|string|min:8',
            'old_password' => 'required',
            'password' => 'required|confirmed|string|min:8',
            //|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[!@#$%^&*(),.?":{}|<>]/
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Find the user by ID
            $user = User::findOrFail($userId);

            // Check if the old password matches
            // if (!Hash::check($request->old_password, $user->password)) {
            //     return response()->json(['errors' => ['oldPassword' => ['Current password does not match.']]], 422);
            // }

            // Check if the old password matches
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'The current password is incorrect.',
                ], 422);
            }

            // Update the user's password
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handle user not found
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 422);
        } catch (\Exception $e) {
            // Handle any other exception that occurs during the process
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating the password: ' . $e->getMessage(),
            ], 500);
        }
    }

    //----------------------------------------------------------------
    //with auth()->user() (need to test this code for auth())
    //     public function updatePassword(Request $request)
    // {
    //     $request->validate([
    //         'old_password' => 'required',
    //         'password' => 'required|string|min:8|confirmed',  // Include new password validation
    //     ]);

    //     // Check if old password is correct
    //     if (!Hash::check($request->old_password, auth()->user()->password)) {
    //         return response()->json(['status' => false, 'message' => 'Current password is incorrect.'], 422);
    //     }

    //     // Update the password
    //     auth()->user()->update(['password' => Hash::make($request->password)]);

    //     return response()->json(['status' => true, 'message' => 'Password updated successfully.']);
    // }

    // Method to handle logout process
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
