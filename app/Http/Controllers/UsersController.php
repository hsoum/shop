<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Event;
use App\User;

class UsersController extends Controller
{

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function participate(Request $request, $event_id)
    {
        $user_events = \Auth::user()->participations();
        $action = null;

        if ($user_events->where('events.id', $event_id)->exists()) {
            $user_events->detach($event_id);
            $action = 'detach';
        } else {
            $user_events->attach($event_id);
            $action = 'attach';
        }

        return response()->json([
            'action' => $action,
            'participantsCount' => Event::find($event_id)->participants()->count()
        ]);
    }

    public function toggleLike(Request $request, Event $event)
    {
        if ($event->liked()) {
            $event->unlike();
        } else {
            $event->like();
        }

        return response()->json([
            'likesCount' => $event->likeCount
        ]);
    }

    public function events()
    {
        $participations = \Auth::user()->participations()
            ->whereRaw('events.user_id != ?', \Auth::user()->id)->get();
        $events = \Auth::user()->events()->get();

        return view('users.events', compact('participations', 'events'));
    }

    public function reports()
    {
        $events = \App\Event::onlyTrashed()->get();
        $comments = \App\Comment::onlyTrashed()->get();
        return view('users.reports', compact('events', 'comments'));
    }


    public function index()
    {
        $roles = array_values(User::getPossibleEnumValues('role'));
        if (\Auth::user()->hasRole('admin')) {
            $users = User::all();
            return view('users.index', compact('users', 'roles'));
        } else {
            $users = \Auth::user()->get();
            return view('users.index', compact('users', 'roles'));
        }
    }

    public function update(Request $request, User $user)
    {
        if (\Auth::user()->hasRole('admin')) {
            # code...
            $user->role = $request->input('role');
            $user->save();
        } else {
            $validatedData = $request->validate();
            $user->update($validatedData);
        }

        return redirect()->route('users.index');
    }
}
