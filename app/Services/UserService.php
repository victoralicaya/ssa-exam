<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserService implements UserServiceInterface
{
    /**
     * Define the validation rules for the model.
     *
     * @param  int $id
     * @return array
     */
    public function rules($id = null)
    {
        return [
            'prefixname' => ['nullable', 'string', 'in:Mr.,Ms.,Mrs.'],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'suffixname' => ['nullable', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png,gif'],
            'password' => !$id ? ['required', 'min:8'] : ['nullable'],
        ];
    }

    /**
     * Retrieve all resources and paginate.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
    */
    public function usersList()
    {
        $users = User::paginate(10);
        $count = 10;
        $perPage = $count;

        return new LengthAwarePaginator($users, $count, $perPage);
    }

    /**
     * Create model resource.
     *
     * @param  array $attributes
    */
    public function addUser(array $request): User
    {
        $user = User::create($request);

        return $user;
    }

    /**
     * Retrieve model resource details.
     * Abort to 404 if not found.
     *
     * @param  integer $id
     * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function findUser(int $id):? User
    {
        $user = User::findOrFail($id);
        return $user;
    }

    /**
     * Update model resource.
     *
     * @param  integer $id
     * @param  array   $attributes
    */
    public function updateUser(int $id, array $request)
    {
        $user = User::findOrFail($id);

        if ($request['password'] === null) {
            unset($request['password']);
        }

        if ($request['photo'] === null) {
            unset($request['photo']);
        }

        $user->update($request);

        return $user;
    }

    /**
     * Soft delete model resource.
     *
     * @param  integer|array $id
     * @return void
    */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user) {
            $user->delete();
            return true;
        }

        return false;
    }

    /**
     * Include only soft deleted records in the results.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function listTrashed()
    {
        $users = User::onlyTrashed()->paginate(10);
        $count = 10;
        $perPage = $count;

        return new LengthAwarePaginator($users, $count, $perPage);
    }

    /**
     * Restore model resource.
     *
     * @param  integer|array $id
     * @return void
    */
    public function restore(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return true;
    }

    /**
     * Permanently delete model resource.
     *
     * @param  integer|array $id
     * @return void
     */
    public function forceDeleteUser(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return true;
    }

    /**
     * Generate random hash key.
     *
     * @param  string $key
     * @return string
    */
    public function hash(string $key): string
    {
        return Hash::make($key);
    }

    /**
     * Upload the given file.
     *
     * @param  \Illuminate\Http\UploadedFile $file
     * @return string|null
     */
    public function uploadFile(UploadedFile $file)
    {
        $photo = $file->store('public/images');
        $path = Storage::url($photo);
        $image = url('/').'/'.$path;

        return $image;
    }
}
