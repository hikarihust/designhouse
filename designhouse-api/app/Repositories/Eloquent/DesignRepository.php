<?php
namespace App\Repositories\Eloquent;
use App\Repositories\Contracts\IDesign;
use App\Models\Design;

class DesignRepository implements IDesign
{
    public function all()
    {
        return Design::all();
    }
}
