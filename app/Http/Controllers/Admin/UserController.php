<?php

namespace App\Http\Controllers\Admin;

use App\Actions\User\CreateUser;
use App\Actions\User\UpdateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserFormRequest;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use ValidatesRequests;

    public $user;

    public function __construct()
    {
        $this->middleware('access_limitation')->only(['update']);

        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();

            return $next($request);
        });
    }

    public function dashboard()
    {
        try {
            session(['layout_mode' => 'left_nav']);
            if (is_null($this->user) || ! $this->user->can('dashboard.view')) {
                abort(403, 'Sorry !! You are Unauthorized to view dashboard.');
            }

            return view('admin.index');
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('admin.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view users.');
        }
        try {
            $users = Admin::where('id', '!=', 1)->SimplePaginate(10);

            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('admin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create users.');
        }
        try {
            $roles = Role::all();

            return view('admin.users.create', compact('roles'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return RedirectResponse
     */
    public function store(UserFormRequest $request)
    {
        if (is_null($this->user) || ! $this->user->can('admin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to store users.');
        }

        try {
            CreateUser::create($request);

            flashSuccess('User Created Successfully');

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Admin $user)
    {
        if (is_null($this->user) || ! $this->user->can('admin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit users.');
        }
        try {
            $roles = Role::all();

            return view('admin.users.edit', compact('roles', 'user'));
        } catch (\Exception $e) {
            flashError('An error occurred: '.$e->getMessage());

            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return RedirectResponse
     */
    public function update(UserFormRequest $request, Admin $user)
    {
        if (is_null($this->user) || ! $this->user->can('admin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to update users.');
        }

        try {
            UpdateUser::update($request, $user);

            flashSuccess('User Updated Successfully');

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return RedirectResponse
     */
    public function destroy(Admin $user)
    {
        if (is_null($this->user) || ! $this->user->can('admin.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete users.');
        }

        try {
            if (! is_null($user)) {
                $user->delete();
            }

            flashSuccess('User Deleted Successfully');

            return back();
        } catch (\Throwable $th) {
            flashError($th->getMessage());

            return back();
        }
    }

    // block user
    public function block(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $authUser = auth()->user();

        // Check if the user is already blocked
        if (! $authUser->blockedUsers()->where('blocked_user_id', $user->id)->exists()) {
            // Add the user to the blocked list if not already blocked
            $authUser->blockedUsers()->attach($user->id);
            $message = 'User has been blocked.';
        } else {
            // If the user is already blocked
            $message = 'User is already blocked.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function getBlockedUsers()
    {
        $user = Auth::user();
        $blockedUserIds = $user->blockedUsers->pluck('id')->toArray();
        $blockedByUserIds = $user->blockedBy->pluck('id')->toArray();

        return response()->json([
            'blockedUsers' => $blockedUserIds,
            'blockedByUsers' => $blockedByUserIds,
        ]);
    }

    public function showBlockedUsers()
    {
        $blockedUserIds = Auth::user()->blockedUsers->pluck('id')->toArray();
        $blockedUsers = User::whereIn('id', $blockedUserIds)->get();

        return view('frontend.seller.blocked-users', compact('blockedUsers'));
    }

    public function unblockUser($id)
    {
        $user = Auth::user();
        $user->blockedUsers()->detach($id);

        return redirect()->route('get-blocked.users')->with('status', 'User unblocked successfully!');
    }
}
