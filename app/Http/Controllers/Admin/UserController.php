<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\AddUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function get()
    {
        $users = $this->userService->get();
        return response($users, $users['status']);
    }
    public function add(AddUserRequest $request)
    {
        $user = $this->userService->add($request->validated());
        return response($user, $user['status']);
    }
    public function show($id)
    {
        $user = $this->userService->show($id);
        return response($user, $user['status']);
    }
    public function update(UpdateUserRequest $request, $id)
    {
        $user = $this->userService->update($id, $request->validated());
        return response($user, $user['status']);
    }
    public function delete($id)
    {
        $user = $this->userService->delete($id);
        return response($user, $user['status']);
    }
}
