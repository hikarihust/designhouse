<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\ITeam;
use Illuminate\Support\Str;
use App\Http\Resources\TeamResource;

class TeamsController extends Controller
{

    protected $teams;

    public function __construct(ITeam $teams)
    {
        $this->teams = $teams;
    }

    /**
     * Get list of all teams (eg for Search)
     */
    public function index(Request $request)
    {

    }

    /**
     * Save team to database
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name']
        ]);

        // create team in database
        $team = $this->teams->create([
            'owner_id' => auth()->id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        // current user is inserted as
        // team member using boot method in Team model

        return new TeamResource($team);
    }

    /**
     * Update team information
     */
    public function update(Request $request, $id)
    {
        $team = $this->teams->find($id);
        $this->authorize('update', $team);

        $this->validate($request, [
            'name' => ['required', 'string', 'max:80', 'unique:teams,name,'.$id]
        ]);

        $team = $this->teams->update($id, [
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return new TeamResource($team);
    }

    /**
     * Find a team by its ID
     */
    public function findById($id)
    {
        $team = $this->teams->find($id);
        return new TeamResource($team);
    }

    /**
     * Get the teams that the current user belongs to
     */
    public function fetchUserTeams()
    {
        $teams = $this->teams->fetchUserTeams();
        return TeamResource::collection($teams);
    }

    /**
     * Get team by slug for Public view
     */
    public function findBySlug($slug)
    {

    }

    /**
     * Destroy (delete) a team
     */
    public function destroy($id)
    {

    }
}
