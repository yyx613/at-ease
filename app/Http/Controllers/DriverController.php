<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    const START_TRIP_STEP = 1;
    const END_TRIP_STEP = 2;
    
    public function customerList() {
        $customers = User::where([
            ['role_id', '!=', Role::ROLE_DRIVER_ID],
            ['driver_id', Auth::id()]
        ])->get();
        
        return view('pages.driver.customer-list', [
            'customers' => $customers,
            'nextStep' => route('start-trip'),
            'step' => self::START_TRIP_STEP
        ]);
    }
    
    public function startTrip() {
        $customersQuery = User::where([
            ['role_id', '!=', Role::ROLE_DRIVER_ID],
            ['driver_id', Auth::id()]
        ]);
        $customers = $customersQuery->get();
        $customersCount = $customersQuery->count();

        $customersIds = [];
        for ($i=0; $i < count($customers); $i++) { 
            array_push($customersIds, $customers[$i]->id);
        }
        // dd($customersIds);

        $customersPendingOrderCount = DB::table(Order::TABLE_NAME)
                                        ->select('customer_id', 'status_id')
                                        ->whereIn('customer_id', $customersIds)
                                        ->groupBy('customer_id')
                                        ->having('status_id', Order::ORDER_STATUS_PENDING)
                                        ->count();
        $deliveryProgress = floor((1 - ($customersPendingOrderCount / $customersCount))  * 100);

        return view('pages.driver.customer-list', [
            'customers' => $customers,
            'nextStep' => route('customer-list'),
            'step' => self::END_TRIP_STEP,
            'orderPendingId' => Order::ORDER_STATUS_PENDING,
            'deliveryProgress' => $deliveryProgress
        ]);
    }
}