<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('catalog::index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     *
     * @return void
     */
    public function show(int $id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function edit() {}

    /**
     * Update the specified resource in storage.
     *
     * @return void
     */
    public function update(Request $request, int $id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    public function destroy(int $id) {}
}
