<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('order::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('order::create');
    }

    /**
     * Store a newly created resource in storage.
     * @return void
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     * @return void
     */
    public function show(int $id)
    {
        //        return view('order::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return void
     */
    public function edit(int $id)
    {
        //        return view('order::edit');
    }

    /**
     * Update the specified resource in storage.
     * @return void
     */
    public function update(Request $request, int $id) {}

    /**
     * Remove the specified resource from storage.
     * @return void
     */
    public function destroy(int $id) {}
}
