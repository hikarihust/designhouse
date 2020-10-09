<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendInvitationToJoinTeam;
use App\Models\Team;

class InvitationsController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;

    public function __construct(IInvitation $invitations,
        ITeam $teams, IUser $users
    )
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $users;
    }

    public function invite(Request $request, $teamId)
    {
        // get the team
        $team = $this->teams->find($teamId);

        $this->validate($request, [
            'email' => ['required', 'email']
        ]);
        $user = auth()->user();
        // check if the user owns the team
        if(! $user->isOwnerOfTeam($team)){
            return response()->json([
                'email' => 'You are not the team owner'
            ], 401);
        }

        // check if the email has a pending invitation
        if($team->hasPendingInvite($request->email)){
            return response()->json([
                'email' => 'Email already has a pending invite'
            ], 422);
        }

        // get the recipient by email
        $recipient = $this->users->findByEmail($request->email);

        // if the recipient does not exist, send invitation to join the team
        if(! $recipient){
            $this->createInvitation(false, $team, $request->email);

            return response()->json([
                'message' => 'Invitation sent to user'
            ], 200);
        }

        // check if the team already has the user
        if($team->hasUser($recipient)){
            return response()->json([
                'email' => 'This user seems to be a team member already'
            ], 422);
        }

        // send the invitation to the user
        $this->createInvitation(true, $team, $request->email);
        return response()->json([
            'message' => 'Invitation sent to user'
        ], 200);
    }

    public function resend($id)
    {

    }

    public function respond(Request $request, $id)
    {

    }

    public function destroy($id)
    {

    }

    protected function createInvitation(bool $user_exists, Team $team, string $email)
    {
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(microtime()))
        ]);

        Mail::to($email)
            ->send(new SendInvitationToJoinTeam($invitation, $user_exists));
    }
}
