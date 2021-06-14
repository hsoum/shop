<?php

namespace App\Http\Controllers;

use App\Event;
use App\User;

use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EventPromoted;
use App\Notifications\NewReport;


class EventsController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type)
    {
        $month = date('m');
        $year = date('Y');
        $events = Event::where(['status' => null, 'deleted_at' => null]);

        switch ($type) {
            case 'month':
                $events = $events->whereRaw('YEAR(planned_on) = ? AND MONTH(planned_on) = ?', [$year, $month]);
                break;
            case 'past':
                $events = $events->whereRaw('YEAR(planned_on) <= ? AND MONTH(planned_on) < ?', [$year, $month]);
                break;
            case 'future':
                $events = $events->whereRaw('YEAR(planned_on) >= ? AND MONTH(planned_on) > ?', [$year, $month]);
                break;
            case 'suggestions':
                $events = Event::where('status', 1);
                break;
        }
        $events = $events->orderBy('planned_on', 'asc')->get();

        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $event = new Event;
        return view('events.create', compact('event'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EventRequest $request)
    {
        $validatedData = $request->validated();

        if (\Auth::user()->hasRole('client') || \Auth::user()->hasRole('It') || $request->input('status') === 'on') {
            $validatedData['status'] = 1;
        } elseif ($request->input('status') === null) {
            $validatedData['status'] = null;
        }

        $fileName = time() . '.' . $validatedData['image']->getClientOriginalExtension();
        $validatedData['image']->storeAs('photos', $fileName, 'public');
        $validatedData['image'] = $fileName;
        \Auth::user()->events()->create($validatedData);

        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(EventRequest $request, Event $event)
    {
        $validatedData = $request->validated();

        if ($request->input('status') === null) {
            $validatedData['status'] = null;
            if ($event->status === 1) {
                $author = $event->author;
                $author->notify(new EventPromoted($event));
            }
        } elseif ($request->input('status') === 'on') {
            $validatedData['status'] = 1;
        }

        if ($request->file('image')) {
            $fileName = time() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('photos', $fileName, 'public');
            $validatedData['image'] = $fileName;
        }

        $event->update($validatedData);

        return redirect()->route('home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $event_id)
    {
        if ($event = Event::find($event_id)) {
            $event->delete();

            $admin_members = User::where('role', 'admin')->get();
            Notification::send($admin_members, new NewReport());
        } elseif ($event = Event::withTrashed()->find($event_id)) {
            $event->participants()->detach();
            $event->forceDelete();
        }
        return response()->json(['done']);
    }

    public function restore($id)
    {
        $event = Event::withTrashed()->find($id);
        if ($event) {
            $event->deleted_at = null;
            $event->save();
        }
        return response()->json(['done']);
    }

    public function participants(Event $event)
    {
        $participants = $event->participants;
        $pdf = \PDF::loadView('pdf.participants', compact('participants', 'event'));
        return $pdf->download($event->name . ' - Participants.pdf');
    }
}
