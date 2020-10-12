<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\IUser;
use App\Models\User;
use Illuminate\Http\Request;
use App\Repositories\Eloquent\Criteria\EagerLoad;

class UserController extends Controller
{
    protected $users;

    public function __construct(IUser $users)
    {
        $this->users = $users;
    }
    public function index()
    {
        $users = $this->users->withCriteria([
            new EagerLoad(['designs'])
        ])->all();

        return UserResource::collection($users);
    }

    public function search(Request $request)
    {
        $designers = $this->users->search($request);
        $designers = UserResource::collection($designers);

        $distance = $this->users->distance($request);
        foreach ($designers as $key => $value) {
            foreach ($distance as $value1) {
                if($value1->id == $value['id']) {
                    $designers[$key]->dist = $value1->dist;
                }
            }
        }
        return $designers;
    }

}
