<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function getContact()
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

        return view('pages.contact', [
            'contacts' => $allContacts,
        ]);
    }
    public function getAddContact()
    {
        return view('pages.add-contact');
    }

    public function searchContact(Request $request)
    {
        $username = $request->input('username');

        if (empty($username) || !preg_match('/^[a-zA-Z0-9]+$/', $username) || strlen($username) > 255) {
            return view('pages.add-contact')->with('error', 'Invalid username. Please provide a valid username.');
        }

        $user = User::where('username', $username)->first();

        if ($user) {
            if ($user->id === auth()->id()) {
                return view('pages.add-contact')->with('error', 'You cannot add yourself as a contact.');
            }
            return view('pages.add-contact')->with('user', $user);
        } else {
            return view('pages.add-contact')->with('error', 'User not found.');
        }
    }

    public function addContact(Request $request)
    {
        $request->validate([
            'username' => 'required',
        ]);
        $friend = User::where('username','=',$request->username)->first();
        if (!$friend) {
            return view('pages.add-contact')->with('error', 'User not found.');
        }
        if ($friend->id === auth()->id()) {
            return view('pages.add-contact')->with('error', 'You cannot add yourself as a contact.');
        }
        $friendExist = Contact::where('user_id', auth()->id())
            ->where('friend_id', $friend->id)
            ->first();
        if ($friendExist) {
            return view('pages.add-contact')->with('error', 'This user is already in your contacts.');
        }

        $contact = Contact::create([
            'user_id' => auth()->id(),
            'friend_id' => $friend->id,
        ]);

        return redirect()->route('dashboard')->with('success', 'Contact added successfully!');
    }

    public function acceptContact(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
        ]);

        $contact = Contact::find($request->contact_id);
        if($contact){
            Contact::create([
                'user_id' => auth()->id(),
                'friend_id' => $contact->user_id,
            ]);
        }
        if ($contact->friend_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        return redirect()->route('contact')->with('success', 'Contact request accepted.');
    }

    public function rejectContact(Request $request)
    {
        $request->validate([
            'contact_id' => 'required|exists:contacts,id',
        ]);

        $contact = Contact::find($request->contact_id);
        if ($contact) {
            $contact->delete();
            return redirect()->route('contact')->with('success', 'Contact request rejected.');
        }

        return redirect()->route('contact')->with('error', 'Contact request not found.');
    }
}
