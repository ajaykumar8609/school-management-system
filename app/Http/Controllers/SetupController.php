<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    /**
     * One-time setup: create/reset admin user.
     * Use ?key=YOUR_SECRET from APP_SETUP_KEY env, or only when no users exist.
     */
    public function admin(Request $request)
    {
        $key = $request->query('key');
        $expectedKey = config('app.setup_key', 'school-setup-2025');

        if ($key !== $expectedKey) {
            return response('Unauthorized. Use ?key=YOUR_APP_SETUP_KEY', 403);
        }

        $user = User::updateOrCreate(
            ['email' => 'admin@school.com'],
            ['name' => 'Admin']
        );

        // Set password directly to avoid cast double-hash
        $user->forceFill(['password' => Hash::make('password')])->save();

        return response('Admin user ready. Email: admin@school.com, Password: password. You can remove APP_SETUP_KEY now.', 200);
    }
}
