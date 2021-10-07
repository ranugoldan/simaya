<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Procurement;

class ProcurementsController extends Controller
{
    public function index()
    {
        $this->authorize('view', Procurement::class);
        return view('procurements/index');
    }

    public function create()
    {
        $this->authorize('create', Procurement::class);
        return view('procurements/edit')
            ->with('item', new Procurement);
    }
}
