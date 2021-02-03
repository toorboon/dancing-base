<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $allUsersEmailsArray = User::pluck('email')->toArray();

        if (isset($request['name'])){
            $user->name = $request['name'];
            $user->save();
            return back()->with('success', 'Username changed');
        }

        if (isset($request['email'])){
            $userEmail = $request['email'];

            // If the user_email is already taken return with the user_id which is blocking
            if (in_array($userEmail, $allUsersEmailsArray)){
                $blockingUser = User::select('name')->where('email', $userEmail)->pluck('name');
                return back()->with('error', $userEmail.' already taken by '.$blockingUser[0]);
            }
            $user->email = $request['email'];
            $user->save();
            return back()->with('success', 'User email changed');
        }

        return back()->with('error', 'Couldn\'t update anything on user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($userId)
    {
        $loggedInUser = auth()->id();

        if ($userId == $loggedInUser) {
            return back()->with('error', 'You cannot delete logged in user');
        }

        $user = User::find($userId);
        $user->delete();
        return back()->with('success', 'User deleted');
    }
}
