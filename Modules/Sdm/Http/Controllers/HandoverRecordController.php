<?php

namespace Modules\Sdm\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Sdm\Entities\HandoverRecord;

class HandoverRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('sdm::handover.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('sdm::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('sdm::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('sdm::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function get_data(Request $request)
    {
        $query = HandoverRecord::with(['fromEmployee', 'toEmployee', 'verifier', 'user']);

        return datatables()->of($query)
            // Kolom Tanggal
            ->editColumn('handover_date', fn($row) => $row->handover_date->format('d M Y'))
            ->addColumn('from_employee', fn($row) => $row->fromEmployee->name ?? '-')
            ->addColumn('to_employee', fn($row) => $row->toEmployee->name ?? '-')
            ->addColumn('verifier', fn($row) => $row->verifier->name ?? '-')
            ->addColumn('dibuat_oleh', function($row) {
                $createdBy = $row->user ? $row->user->name : 'System';
                $createdAt = $row->created_at ? $row->created_at->format('d M Y H:i') : '-';
                return '<div><span class="fw-bold">' . $createdBy . '</span><br><small class="text-muted">' . $createdAt . '</small></div>';
            })
            ->addColumn('action', function ($row) {
                $show = route('handover.detail', $row->id);
                $edit = route('handover.edit', $row->id);
                return '
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bars"></i>
                        </button>

                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="' . $show . '">
                                    <i class="fa-solid fa-eye text-primary"></i> Show
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="' . $edit . '">
                                    <i class="fa-solid fa-pen text-warning"></i> Edit
                                </a>
                            </li>

                            <li>
                                <button class="dropdown-item text-danger btn-delete" data-id="' . $row->id . '">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </li>
                        </ul>
                    </div>
                ';
            })

            ->rawColumns(['action', 'dibuat_oleh'])
            ->make(true);
    }
}
