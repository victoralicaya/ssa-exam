<?php

namespace App\Http\Controllers;

use App\Events\UserSaved;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\Cast\Array_;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paginator = $this->userService->usersList();
        $users = $paginator->items();
        return view('home', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('adduser');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        if ($request->hasFile('photo')) {
            $photo = $this->userService->uploadFile($request->file('photo'));
            $validated = $request->validated();
            $validated['photo'] = $photo;
        } else {
            $validated = $request->validated();
            $validated['photo'] = null;
        }
        $user = $this->userService->addUser($validated);

        event(new UserSaved($user));

        return redirect()->route('users.index')->with('success', 'User Added Successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user = $this->userService->findUser($user->id);
        return view('showuser', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('edituser', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, User $user)
    {
        if ($request->hasFile('photo')) {
            $photo = $this->userService->uploadFile($request->file('photo'));
            $validated = $request->validated();
            $validated['photo'] = $photo;
        } else {
            $validated = $request->validated();
            $validated['photo'] = null;
        }
        $user = $this->userService->updateUser($user->id, $validated);

        Log::channel('user')->info('user', ['user' => $user]);

        event(new UserSaved($user));

        return redirect()->route('users.index')->with('success', 'User Updated Successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->userService->deleteUser($user->id);
        return redirect()->route('users.index')->with('success', 'User Deleted Successfully.');
    }

    /**
     * List of Soft Deleted Users
     */
    public function trashed()
    {
        $trashedUsers = $this->userService->listTrashed();

        return view('trashedusers', compact('trashedUsers'));
    }

    /**
     * Restore Trashed Users
     *
     * @param int $user
     */
    public function restore($userId)
    {
        $this->userService->restore($userId);

        return redirect()->route('users.index')->with('success', 'User Restored Successfully.');
    }

    /**
     * Permanently delete a user
     *
     * @param User $user
     */
    public function permanentlyDelete($userId)
    {
        $this->userService->forceDeleteUser($userId);

        return redirect()->route('users.index')->with('success', 'User Permanently Deleted.');
    }

}
