<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Group;
use App\Models\GroupUser;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function getCreateGroup()
    {
        $contacts = Contact::where('user_id', auth()->id())
            ->with('Friend')
            ->get()
            ->map(function ($contact) {
                $isRealContact = Contact::where('user_id', $contact->friend_id)
                    ->where('friend_id', auth()->id())
                    ->exists();

                if ($isRealContact) {
                    $contact->status = 'friend';
                } else {
                    $contact->status = 'your_request';
                }

                return $contact;
            });

        $pendingRequests = Contact::where('friend_id', auth()->id())
            ->whereNotIn('user_id', $contacts->pluck('friend_id'))
            ->with('User')
            ->get()
            ->map(function ($contact) {
                $contact->status = 'their_request';
                return $contact;
            });

        $allContacts = $contacts->merge($pendingRequests);
        return view('pages.create-group')->with([
            'contacts' => $allContacts,
        ]);
    }

    public function createGroup(Request $request)
    {
        $group_name = Auth()->user()->username . "'s Group";
        if($request->group_name !== null || $request->group_name != ''){
            $group_name = $request->group_name;
        }
        
        $group = Group::create([
            'name' => $group_name,
            'creator_id' => auth()->id(),
        ]);

        foreach($request->friend_ids as $friend_id) {
            GroupUser::create([
                'group_id' => $group->id,
                'user_id' => $friend_id,
            ]);
        }

        return redirect()->route('groups')->with('success', 'Group created successfully!');
    }

    public function getGroup()
    {
        $user = auth()->id();

        $groups = Group::with(['creator','members'])
            ->where('creator_id', $user)
            ->orWhereHas('members', function($q) use ($user) {
                $q->where('user_id', $user);
            })
            ->get(['id','name','creator_id']);

        return view('pages.groups', [
            'groups' => $groups,
        ]);
    }
}
