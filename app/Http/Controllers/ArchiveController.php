<?php

namespace App\Http\Controllers;

use App\Models\invoice;
use Illuminate\Http\Request;
use App\Models\invoice_attachments;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = invoice::onlyTrashed()->get();
        return view("invoices.Archive_ivoices", compact("invoices"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $id = $request->invoice_id;
        invoice::withTrashed()->where('id', $id)->restore();
        session()->flash("restore_invoice");
        return redirect("invoices");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = invoice::withTrashed()->where("id", $id)->first();
        $attachments = invoice_attachments::where("invoice_id", $id)->get();
        foreach ($attachments as $attachment) {
            $dir = "attachments";
            $invoice_number = $attachment->invoice_number;
            $file_name = $attachment->file_name;
            $file = public_path($dir . '/' . $invoice_number . '/' . $file_name);

            if (file_exists($file)) {
                unlink($file);
            }
            // $fileDir = public_path($dir. '/' . $invoice_number );
            // if(empty($fileDir)){
            //     Storage::deleteDirectory($fileDir);
            // }
        }
        $invoices->forceDelete();
        session()->flash("delete_invoice");
        return redirect("Archive");
    }
}
