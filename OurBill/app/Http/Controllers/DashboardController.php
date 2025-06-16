<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $bills = BillUser::where('user_id', auth()->id())->with('bill', 'user')->get();

        foreach ($bills as $billUser) {
            $bill = Bill::where('id', $billUser->bill->id)->with('participants')->first();
            $allPaid = $bill->participants->every(function ($participant) {
                return $participant->is_paid == 1;
            });
            $bill->status = $allPaid ? 'done' : 'undone';
            $billUser->bill->status = $bill->status;
            foreach($billUser->bill->user as $user){
                $user->profile = User::where('id', $user->user_id)->first()->profile;
            }
        }
        return view('dashboard.index', [
            'bills' => $bills,
        ]);
    }
}
