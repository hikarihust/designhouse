<?php
namespace App\Repositories\Eloquent;
use App\Repositories\Contracts\IUser;
use App\Models\User;
use Illuminate\Http\Request;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements IUser
{
    public function model()
    {
        return User::class;
    }

    public function findByEmail($email)
    {
        return $this->model
                    ->where('email', $email)
                    ->first();
    }

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();

        // only designers who have designs
        if($request->has_designs){
            $query->has('designs');
        }

        // check for available_to_hire
        if($request->available_to_hire){
            $query->where('available_to_hire', true);
        }

        // Geographic Search
        $lat = $request->latitude;
        $lng = $request->longitude;
        $dist = $request->distance;
        $unit = $request->unit;

        if($lat && $lng && $dist){
            // $point = new Point($lat, $lng);
            $unit == 'km' ? $dist *= 1000 : $dist *=1609.34;
            // $query->distanceSphereExcludingSelf('location', $point, $dist);
            $query->whereRaw('st_distance(location,POINT('.$lat.','.$lng.')) < '.$dist);
        }

        // order the results
        if($request->orderBy=='closest'){
            // $query->orderByDistanceSphere('location', $point, 'asc');
        } else if($request->orderBy=='latest'){
            $query->latest();
        } else {
            $query->oldest();
        }

        return $query->get();
    }

    public function distance(Request $request) {
        $lat = $request->latitude;
        $lng = $request->longitude;
        $dist = $request->distance;
        $unit = $request->unit;

        $raw = 'SELECT
            id,
            ST_X(location) AS longitude,
            ST_Y(location) AS latitude,
            st_distance(location, POINT(?, ?)) AS dist
        FROM
            users
        HAVING
            dist < ?
        ORDER BY
            dist';
        if($lat && $lng && $dist){
            $unit == 'km' ? $dist *= 1000 : $dist *=1609.34;
        }
        $pointsWithDist = DB::select($raw, [$lng, $lat, $dist]);

        return $pointsWithDist;
    }
}
