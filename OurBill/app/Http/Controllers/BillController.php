<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItems;
use App\Models\BillItemUser;
use App\Models\BillUser;
use App\Models\Contact;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class BillController extends Controller
{
    public function getBill2(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.qty' => 'required|integer',
            'items.*.total' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'total_amount' => 'required|numeric',
        ]);

        $items = $request->input('items');
        $subtotal = 0;
        foreach ($items as $item) {
            $lineTotal = $item['price'] * $item['qty'];
            $subtotal += $lineTotal;
        }
        $tax = $request->input('tax');
        $totalAmount = $subtotal + $tax;

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

        $user = auth()->id();

        $groups = Group::with(['creator','members'])
            ->where('creator_id', $user)
            ->orWhereHas('members', function($q) use ($user) {
                $q->where('user_id', $user);
            })
            ->get(['id','name','creator_id']);
        
        return view('bill2', [
            'contacts' => $allContacts,
            'groups' => $groups,
            'items'        => $items,
            'subtotal'     => $subtotal,
            'tax'          => $tax,
            'totalAmount'  => $totalAmount,
        ]);
    }

    public function getBill3(Request $request)
    {
        $request->validate([
            'items'       => 'required|array',
            'items.*.name'  => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.qty'   => 'required|integer',
            'items.*.total' => 'required|numeric',
            'subtotal'      => 'required|numeric',
            'tax'           => 'required|numeric',
            'total_amount'  => 'required|numeric',
        ]);

        $billUsers = collect();

        if ($request->has('groups') && is_array($request->input('groups'))) {
            $groupIds = $request->input('groups');

            $groups = Group::with(['creator', 'members'])
                ->whereIn('id', $groupIds)
                ->get();

            foreach ($groups as $group) {
                $billUsers->push($group->creator);

                foreach ($group->members as $member) {
                    $billUsers->push($member);
                }
            }
        }

        if ($request->has('friends') && is_array($request->input('friends'))) {
            $friendIds = $request->input('friends');

            $friends = User::whereIn('id', $friendIds)->get();

            foreach ($friends as $friend) {
                $billUsers->push($friend);
            }
        }

        $billUsers->push(auth()->user());

        $billUsers = $billUsers
            ->unique(fn($user) => $user->id)
            ->values();

        return view('bill3', [
            'billUsers'   => $billUsers,
            'items'       => $request->items,
            'subtotal'    => $request->subtotal,
            'tax'         => $request->tax,
            'totalAmount' => $request->total_amount,
        ]);
    }

    public function createBill(Request $request)
    {
        $request->validate([
            'items'            => 'required|array',
            'items.*.name'     => 'required|string',
            'items.*.price'    => 'required|numeric',
            'items.*.qty'      => 'required|integer',
            'items.*.total'    => 'required|numeric',
            'items.*.users'    => 'required|string',
            'subtotal'         => 'required|numeric',
            'tax'              => 'required|numeric',
            'total_amount'     => 'required|numeric',
            'treat_percentage' => 'nullable|numeric',
            'treat_users'      => 'nullable|string',
        ]);

        $items = $request->input('items');
        $treatPercentage = $request->input('treat_percentage', 0);

        $treatUsers = collect(explode(',', $request->input('treat_users', '')))
                        ->map(fn($u) => trim($u))
                        ->filter(fn($u) => $u !== '');

        DB::beginTransaction();

        try {
            $calculatedSubtotal = collect($items)->sum('total');

            $tax = $request->input('tax', 0);
            $calculatedTotalAmount = $calculatedSubtotal + $tax;

            $bill = Bill::create([
                'name' => Auth()->user()->name,
                'subtotal'         => $calculatedSubtotal,
                'tax'              => $tax,
                'total_amount'     => $calculatedTotalAmount,
                'treat_percentage' => $treatPercentage,
                'created_by'       => auth()->id(),
            ]);

            $userItems = collect();

            foreach ($items as $itemData) {
                $billItem = BillItems::create([
                    'bill_id' => $bill->id,
                    'name'    => $itemData['name'],
                    'price'   => $itemData['price'],
                    'qty'     => $itemData['qty'],
                    'total'   => $itemData['total'],
                ]);

                $usersOnLine = array_map('trim', explode(',', $itemData['users']));
                $countUsers  = count($usersOnLine);
                $splitQty    = $itemData['qty'] / $countUsers;
                $splitTotal  = $itemData['total'] / $countUsers;

                foreach ($usersOnLine as $userId) {
                    BillItemUser::create([
                        'item_id' => $billItem->id,
                        'user_id'      => $userId,
                        'split_qty'    => $splitQty,
                        'split_total'  => $splitTotal,
                    ]);

                    if (! $userItems->has($userId)) {
                        $userItems[$userId] = collect();
                    }
                    $userItems[$userId]->push([
                        'name'  => $itemData['name'],
                        'price' => $itemData['price'],
                        'qty'   => $splitQty,
                        'total' => $splitTotal,
                    ]);
                }
            }

            foreach ($treatUsers as $tUser) {
                if (! $userItems->has($tUser)) {
                    $userItems[$tUser] = collect();
                }
            }

            $baseSubtotals = $userItems->map(fn($coll) => $coll->sum('total'));

            $deductions = [];
            $credits    = [];
            $totalDeduction = 0.0;

            if ($treatPercentage > 0 && $treatUsers->isNotEmpty()) {
                $actualTreaters = $treatUsers;
                $countTreaters  = $actualTreaters->count();

                $allUsers      = $userItems->keys();
                $nonTreatUsers = $allUsers->filter(fn($u) => ! $actualTreaters->contains($u));

                foreach ($nonTreatUsers as $nonUser) {
                    $subtotalValue = $baseSubtotals->get($nonUser, 0);
                    if ($subtotalValue <= 0) {
                        $deductions[$nonUser] = 0.0;
                        continue;
                    }
                    $deduction = $subtotalValue * ($treatPercentage / 100);
                    $deductions[$nonUser] = $deduction;
                    $totalDeduction += $deduction;

                    $userItems[$nonUser]->push([
                        'name'  => 'Treat Deduction',
                        'price' => -$deduction,
                        'qty'   => 1,
                        'total' => -$deduction,
                    ]);
                }

                if ($countTreaters > 0 && $totalDeduction > 0) {
                    $perTreatAllocation = $totalDeduction / $countTreaters;
                    foreach ($actualTreaters as $tUser) {
                        $credits[$tUser] = $perTreatAllocation;

                        $userItems[$tUser]->push([
                            'name'  => 'Treats',
                            'price' => $perTreatAllocation,
                            'qty'   => 1,
                            'total' => $perTreatAllocation,
                        ]);
                    }
                } else {
                    foreach ($actualTreaters as $tUser) {
                        $credits[$tUser] = 0.0;
                    }
                }
            }

            foreach ($userItems->keys() as $userId) {
                $isTreat    = $treatUsers->contains($userId);
                $userSubtotal   = $baseSubtotals->get($userId, 0.0);
                $treatDeduction = $deductions[$userId] ?? 0.0;
                $treatCredit    = $credits[$userId]    ?? 0.0;
                $finalTotal     = $userSubtotal - $treatDeduction + $treatCredit;

                BillUser::create([
                    'bill_id'         => $bill->id,
                    'user_id'         => $userId,
                    'user_subtotal'   => $userSubtotal,
                    'treat_deduction' => $treatDeduction,
                    'treat_credit'    => $treatCredit,
                    'final_total'     => $finalTotal,
                    'is_treater'      => $isTreat,
                ]);
            }

            DB::commit();

            $usersArray = $userItems
                ->map(function ($collectionOfItems, $username) {
                    $sumForUser = $collectionOfItems->sum('total');
                    return [
                        'user'  => $username,
                        'items' => $collectionOfItems->values(),
                        'total' => $sumForUser,
                    ];
                })
                ->values();

            return redirect()->route('totalamount', $bill->id);

        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('dashboard')->with('error', 'Failed to create bill. Please try again later.');
        }
    }

    public function getTotalAmount($billId)
    {
        $bill = Bill::findOrFail($billId);

        $billItemIds = BillItems::where('bill_id', $billId)->pluck('id');

        $pivotRows = BillItemUser::whereIn('item_id', $billItemIds)
                                 ->with('billItem')
                                 ->get();

        $billUsers = BillUser::with('user')
                        ->where('bill_id', $billId)
                        ->get();

        $usersOutput = $billUsers->map(function ($billUser) use ($pivotRows) {
            $userId = $billUser->user_id;

            $myPivots = $pivotRows->where('user_id', $userId);

            $itemList = $myPivots->map(function ($pivot) {
                return [
                    'name'  => $pivot->billItem->name,
                    'price' => $pivot->billItem->price,
                    'qty'   => $pivot->split_qty,
                    'total' => $pivot->split_total,
                ];
            })->values()->toArray();

            if ($billUser->treat_credit > 0) {
                $itemList[] = [
                    'name'  => 'Treats',
                    'price' => $billUser->treat_credit,
                    'qty'   => 1,
                    'total' => $billUser->treat_credit,
                ];
            }

            if ($billUser->treat_deduction > 0) {
                $deduction = -1 * $billUser->treat_deduction;
                $itemList[] = [
                    'name'  => 'Treat Deduction',
                    'price' => $deduction,
                    'qty'   => 1,
                    'total' => $deduction,
                ];
            }

            return [
            'user'  => [
                'id'      => $billUser->user->id,
                'name'    => $billUser->user->name,
                'profile' => $billUser->user->profile,
                'is_paid' => $billUser->is_paid,
            ],
            'items' => $itemList,
            'total' => $billUser->final_total,
        ];
        })->values();
        
        return view('totalamount', [
            'bill'         => $bill,
            'users'        => $usersOutput,
            'subtotal'     => $bill->subtotal,
            'tax'          => $bill->tax,
            'total_amount' => $bill->total_amount,
        ]);
    }

    public function confirmBill(Request $request, $id){
        $bill = Bill::where('id', $id)->where('created_by', auth()->id())->first();
        if($bill){
            try {
                foreach($request->confirmed as $userId){
                    $BillUser = BillUser::where('bill_id', $id)
                        ->where('user_id', $userId)
                        ->first();

                    $BillUser->is_paid = true;
                    $BillUser->save();
                }
                DB::commit();
                return view('confirmed');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('dashboard')->with('error', 'Failed to confirm bill. Please try again later.');
            }

        }

        return redirect()->route('dashboard')->with('error', 'Bill not found or you do not have permission to confirm it.');
    }

    public function getOcr(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,jpg,png|max:10240' // 10MB max
        ]);

        try {
            $file = $request->file('file');
            
            $response = Http::timeout(30)
                ->attach('file', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post('http://localhost:8001/ocr/parse');

            if (!$response->successful()) {
                throw new \Exception('OCR service error: ' . $response->body());
            }

            $ocrData = $response->json();
            
            if (!$ocrData['success']) {
                throw new \Exception('OCR parsing failed');
            }
            
            $items = $ocrData['line_items'] ?? [];
            $processedItems = [];

            foreach ($items as $item) {
                $processedItems[] = [
                    'name'  => $item['description'] ?? 'Unknown',
                    'price' => ($item['amount'] ?? 0) * 1000,
                ];
            }
            
            return view('bill1', [
                'items' => $processedItems,
                'subtotal' => array_sum(array_column($processedItems, 'price')),
            ]);
            
        } catch (\Exception $e) {            
            return back()->withErrors(['error' => 'Failed to process receipt: ' . $e->getMessage()]);
        }
    }
}


