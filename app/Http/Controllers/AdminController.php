<?php

namespace App\Http\Controllers;

use App\Models\FreeOfCharge;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\UserProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    const ADMIN_HOME_ROUTE_NAME = 'admin-home';

    protected $isHomePage;

    public function __construct()
    {
        $this->isHomePage = false;
        if (Route::currentRouteName() === self::ADMIN_HOME_ROUTE_NAME) {
            $this->isHomePage = true;
        }
    }

    public function home() {
        $users = User::select('id', 'name', 'email', 'role_id')->get();

        return view('pages.admin.home', [
            'isHomePage' => $this->isHomePage,
            'users' => $users
        ]);
    }

    public function createUser() {
        $formMode = 'create';

        $roles = Role::select('id', 'name')->get(); // Get roles
        $products = Product::select('id', 'name', 'price', 'img_name')->get(); // Get products
        $drivers = User::select('id', 'name')->where('role_id', Role::ROLE_DRIVER_ID)->get();

        return view('pages.admin.create-edit-user', [
            'isHomePage' => $this->isHomePage,
            'formMode' => $formMode,
            'roles' => $roles,
            'products' => $products,
            'drivers' => $drivers,
            'roleDriverId' => Role::ROLE_DRIVER_ID
        ]);
    }

    public function editUser($user_id) {
        $formMode = 'edit';
        
        $user = User::find($user_id);
        if ($user === null) { // If user id is not found
            abort(404);
        }
        $roles = Role::select('id', 'name')->get(); // Get roles
        $products = Product::select('id', 'name', 'price', 'img_name')->get(); // Get products
        $drivers = User::select('id', 'name')->where('role_id', Role::ROLE_DRIVER_ID)->get();

        $specialPriceInput = [];
        foreach ($user->products as $product) {
            $specialPriceInput[] = [
                'product_id' => $product->id,
                'special_price' => $product->pivot->special_price
            ];
        }

        return view('pages.admin.create-edit-user', [
            'isHomePage' => $this->isHomePage,
            'formMode' => $formMode,
            'roles' => $roles,
            'products' => $products,
            'roleDriverId' => Role::ROLE_DRIVER_ID,
            'user' => $user,
            'drivers' => $drivers,
            'specialPriceInput' => json_encode($specialPriceInput)
        ]);
    }

    public function createUserSubmit(Request $request) {
        try {
            // Validate request
            $rules = [
                'name' => 'required|max:150',
                'email' => 'required|email|unique:users',
                'role' => 'required',
            ];
            if ($request->get('role') == Role::ROLE_DRIVER_ID) {
                $rules = array_merge($rules, [
                    'password' => 'required',
                ]);
            }
            if ($request->get('foc')) {
                switch ($request->get('foc')) {
                    case 'foc-1':
                        $focType = FreeOfCharge::FOC_TYPE_1;
                        break;
                    case 'foc-2':
                        $focType = FreeOfCharge::FOC_TYPE_2;
                        break;
                    case 'foc-3':
                        $focType = FreeOfCharge::FOC_TYPE_3;
                        break;
                }
                if ($focType == FreeOfCharge::FOC_TYPE_2) {
                    $rules = array_merge($rules, [
                        'foc_2_n' => 'required',
                        'foc_2_prod' => 'required'
                    ]);
                } else if ($focType == FreeOfCharge::FOC_TYPE_3) {
                    $rules = array_merge($rules, [
                        'foc_3_n' => 'required',
                        'foc_3_m' => 'required',
                        'foc_3_prod' => 'required'
                    ]);
                }
            }
            
            $validator = Validator::make($request->all(), $rules, [
                'name.required' => 'Please enter a name',
                'name.max' => 'Name must not be greater than 150 characters',
                'email.required' => 'Please enter an email',
                'email.unique' => 'This email address has already been taken',
                'password.required' => 'Please enter a password',
                'role.required' => 'Please select a role',
                'foc_2_n' => 'Please enter N',
                'foc_2_prod' => 'Please select a product to apply foc',
                'foc_3_n' => 'Please enter N',
                'foc_3_m' => 'Please enter M',
                'foc_3_prod' => 'Please select a product to apply foc',
            ]);

            if ($validator->fails()) {
                return redirect(route('create-user'))->withErrors($validator)->withInput();
            }
    
            $inputs = $validator->validated();
            $inputs['role_id'] = $inputs['role'];
            $inputs['driver_id'] = $request->get('driver') ?? null;
            if ($inputs['role_id'] == Role::ROLE_DRIVER_ID) {
                $inputs['password'] = Hash::make($inputs['password']);
            }
            unset($inputs['role']);
            // Store user in DB
            $user = User::create($inputs);
            // Store special price if there is any for customer
            if ($inputs['role_id'] != Role::ROLE_DRIVER_ID && $request->get('special-price') !== null) {
                $specialProducts = json_decode($request->get('special-price'));
                for ($i=0; $i < count($specialProducts); $i++) { 
                    $specialProducts[$i]->user_id = $user->id;
                    $user->products()->attach($specialProducts[$i]->product_id, [
                        'special_price' => $specialProducts[$i]->special_price
                    ]);
                }
            }
            // FOC
            if (isset($focType)) {
                FreeOfCharge::create([
                    'user_id' => $user->id,
                    'product_id' => $focType == FreeOfCharge::FOC_TYPE_2 ? $request->get('foc_2_prod') : ($focType == FreeOfCharge::FOC_TYPE_3 ? $request->get('foc_3_prod') : null),
                    'type' => $focType,
                    'foc_2_val' => $request->get('foc_2_n'),
                    'foc_3_val' => $focType == FreeOfCharge::FOC_TYPE_3 ? $request->get('foc_3_n') . '-' . $request->get('foc_3_m') : null
                ]);
            }

            return redirect()->back()->with('success', 'User Created');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong... Please try again later');
        }
    }
    
    public function editUserSubmit(Request $request, $user_id) {
        try {
            $user = User::find($user_id);
            // If user id is not found
            if ($user === null) { 
                return redirect()->back()->with('error', 'Something went wrong... Please try again later');
            }
            $rules = [
                'name' => 'required|max:150',
            ];
            if ($request->get('foc')) {
                switch ($request->get('foc')) {
                    case 'foc-1':
                        $focType = FreeOfCharge::FOC_TYPE_1;
                        break;
                    case 'foc-2':
                        $focType = FreeOfCharge::FOC_TYPE_2;
                        break;
                    case 'foc-3':
                        $focType = FreeOfCharge::FOC_TYPE_3;
                        break;
                }
                if ($focType == FreeOfCharge::FOC_TYPE_2) {
                    $rules = array_merge($rules, [
                        'foc_2_n' => 'required',
                        'foc_2_prod' => 'required'
                    ]);
                } else if ($focType == FreeOfCharge::FOC_TYPE_3) {
                    $rules = array_merge($rules, [
                        'foc_3_n' => 'required',
                        'foc_3_m' => 'required',
                        'foc_3_prod' => 'required'
                    ]);
                }
            }
            // Validate request
            $validator = Validator::make($request->all(), $rules, [
                'name.required' => 'Please enter a name',
                'name.max' => 'Name must not be greater than 150 characters',
                'foc_2_n' => 'Please enter N',
                'foc_2_prod' => 'Please select a product to apply foc',
                'foc_3_n' => 'Please enter N',
                'foc_3_m' => 'Please enter M',
                'foc_3_prod' => 'Please select a product to apply foc',
            ]);
     
            if ($validator->fails()) {
                return redirect(route('edit-user', ['id' => $user_id]))->withErrors($validator)->withInput();
            }
    
            $inputs = $validator->validated();
            // Update in DB
            $user->name = $inputs['name'];
            if ($user->role_id != Role::ROLE_DRIVER_ID && $request->get('driver')) {
                $user->driver_id = $request->get('driver');
            }
            if ($user->role_id == Role::ROLE_DRIVER_ID && $request->get('password')) {
                $user->password = Hash::make($request->get('password'));
            }
            $user->save();
            // Insert special price if there is any
            if ($request->get('special-price') !== null) {
                $specialProducts = json_decode($request->get('special-price'));
                $user->products()->detach();
                for ($i=0; $i < count($specialProducts); $i++) { 
                    $specialProducts[$i]->user_id = $user->id;
                    $user->products()->attach($specialProducts[$i]->product_id, [
                        'special_price' => $specialProducts[$i]->special_price
                    ]);
                }
            }
            // FOC
            if ($request->get('foc')) {
                $user->foc->type = $focType;
                $user->foc->foc_2_val = $focType == FreeOfCharge::FOC_TYPE_2 && $request->get('foc_2_n') ? $request->get('foc_2_n') : null;
                $user->foc->foc_3_val = $focType == FreeOfCharge::FOC_TYPE_3 ? $request->get('foc_3_n') . '-' . $request->get('foc_3_m') : null;
                $user->foc->save();
            }

            return redirect()->back()->with('success', 'Info Updated');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong... Please try again later');
        }
    }

    public function deleteUser($user_id) {
        try {
            $user = User::find($user_id);
            // If user id is not found
            if ($user === null) { 
                return redirect()->back()->with('error', 'Something went wrong... Please try again later');
            }
            $user->products()->detach();
            $user->orders()->delete();
            $user->foc()->delete();
            $user->delete();

            return redirect()->back()->with('success', 'User Deleted');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Something went wrong... Please try again later');
        }
    }
}
