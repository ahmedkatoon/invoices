<?php

namespace App\Http\Controllers;

use App\Models\invoice;
use App\Models\invoice_attachments;
use App\Models\invoices_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoicesDetailsController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(invoices_details $invoices_details)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoices = invoice::where("id", $id)->first();
        $details = invoices_details::where("id_invoice", $id)->get();
        $attachments = invoice_attachments::where("invoice_id", $id)->get();
        return view("invoices.details_invoices", compact("invoices", "details", "attachments"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoices_details $invoices_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $invoices = invoice_attachments::findOrFail($request->id_file);

        // Storage::disk("public_uploads")->delete($request->invoice_number . '/' . $request->file_name);

        $dir = "attachments";
        $invoice_number = $request->invoice_number;
        $file_name = $request->file_name;
        $file = public_path($dir . '/' . $invoice_number . '/' . $file_name);

        if (file_exists($file)){
            unlink($file);


        }
        $invoices->delete();

        session()->flash("delete", "تم الحذف بنجاح");
        return back();

    }

    public function open_file($invoice_number, $file_name)
    {
        // $file = Storage::disk("public_uploads")->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        // return response()->file($file); ----------------not work-----------

        // $file = Storage::disk('public_uploads')->get("/".$invoice_number.'/'.$file_name);
        // return $file;
        $dir = "attachments";
        $file = public_path($dir . '/' . $invoice_number . '/' . $file_name);
        if(file_exists($file) == false)
        {

            return back();
        }else{
            return response()->file($file); 
        }
    }
    public function get_file($invoice_number, $file_name)
    {
        // $file = Storage::disk("public_uploads")->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        // return response()->download($file);
        $dir = "attachments";
        $file = public_path($dir . '/' . $invoice_number . '/' . $file_name);
        if(file_exists($file) == false)
        {
            return back();
        }else{
            return response()->download($file);
        }

    }
}
