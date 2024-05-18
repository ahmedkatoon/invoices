<?php

namespace App\Http\Controllers;

use App\Models\invoice_attachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceAttachmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $this->validate($request, [
            "file_name" => "mimes:png,jpg,jpeg,pdf"
        ]);
        $image = $request->file("file_name");
        $file_name = $image->getClientOriginalName();

        $attachment = new invoice_attachments();
        $attachment->file_name = $file_name;
        $attachment->invoice_number = $request->invoice_number;
        $attachment->invoice_id = $request->invoice_id;
        $attachment->Created_by = Auth::user()->name;
        $attachment->save();

        $imageName = $request->file_name->getClientOriginalName();
        $request->file_name->move(public_path("attachments/" . $request->invoice_number),$imageName);
        session()->flash("Add","تم اضافه المرفق بنجاخ");
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(invoice_attachments $invoice_attachments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(invoice_attachments $invoice_attachments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoice_attachments $invoice_attachments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(invoice_attachments $invoice_attachments)
    {
        //
    }
}
