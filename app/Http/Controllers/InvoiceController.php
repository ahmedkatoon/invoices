<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\invoice;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\invoices_details;
use Illuminate\Support\Facades\DB;
use App\Models\invoice_attachments;
use App\Notifications\AddInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = invoice::all();
        return view("invoices.invoices", ["invoices" => $invoices]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sections = Section::all();
        return view("invoices.add_invoice", ["sections" => $sections]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);
        $invoice_id = invoice::latest()->first()->id;
        invoices_details::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        $request->validate([
            "pic" => "required|image|mimes:png,jpg,JPG"
        ]);

        if ($request->hasFile("pic")) {
            $invoice_id = invoice::latest()->first()->id;
            $image = $request->file("pic");
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new invoice_attachments();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->invoice_id = $invoice_id;
            $attachments->Created_by = Auth::user()->name;
            $attachments->save();

            $image_name = $request->pic->getClientOriginalName();
            $request->pic->move(public_path("attachments/" . $invoice_number), $image_name);

            // session()->flash("Add", "تم اضافه الفاتوره بنجاح");
            // return redirect("invoices");
        }
        // $user = User::first();

        // Notification::send($user, new AddInvoice($invoice_id));

        $user = User::get();
        $invoices = invoice::latest()->first();
        $user->notify(new AddInvoice($invoices));
        session()->flash("Add", "تم اضافه الفاتوره بنجاح");
        return redirect("invoices");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $invoices = invoice::where("id", $id)->first();
        return view("invoices.status_update", ["invoices" => $invoices]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $invoices = invoice::where("id", $id)->first();
        $sections = Section::all();
        return view("invoices.edit", compact("invoices", "sections"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, invoice $invoice)
    {
        // return $request;
        $invoices = invoice::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);
        session()->flash("edit", "تم تعديل الفاتوره بنجاح");
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = Invoice::where("id", $id)->first();
        $attachments = invoice_attachments::where("invoice_id", $id)->get();
        $id_page = $request->id_page;

        if (!$id_page == 2) {
            foreach ($attachments as $attachment) {
                $dir = "attachments";
                $invoice_number = $attachment->invoice_number;
                $file_name = $attachment->file_name;
                $file = public_path($dir . '/' . $invoice_number . '/' . $file_name);

                if (file_exists($file)) {
                    unlink($file);
                }
            }
            $invoices->forceDelete();
            session()->flash("delete_invoice");
            return redirect("invoices");
        } else {
            $invoices->delete();
            session()->flash("archive_invoice");
            return redirect("Archive");
        }
    }

    public function getProducts($id)
    {
        $products = DB::table("products")->where("section_id", $id)->pluck("product_name", "id");
        return json_encode($products);
    }

    public function Status_Update($id, Request $request)
    {
        $invoices = invoice::findOrFail($id);

        if ($request->Status == "مدفوعه") {
            $invoices->update([
                "Value_Status" => 1,
                "Status" => $request->Status,
                "Payment_Data" => $request->Payment_date
            ]);

            invoices_details::create([
                "id_invoice" => $request->invoice_id,
                "invoice_number" => $request->invoice_number,
                "product" => $request->product,
                "product" => $request->product,
                "status" => $request->Status,
                "value_status" => 1,
                "Payment_Date" => $request->Payment_Date,
                "note" => $request->note,
                "user" => (Auth::user()->name)
            ]);
        } else {
            $invoices->update([
                "Value_Status" => 3,
                "Status" => $request->Status,
                "Payment_Data" => $request->Payment_date
            ]);

            invoices_details::create([
                "id_invoice" => $request->invoice_id,
                "invoice_number" => $request->invoice_number,
                "product" => $request->product,
                "section" => $request->section,
                "status" => $request->Status,
                "value_status" => 3,
                "Payment_Date" => $request->Payment_Date,
                "note" => $request->note,
                "user" => Auth::user()->name
            ]);

            session()->flash("Status_Update");
            return redirect("invoices");
        }
    }

    public function Invoice_Paid()
    {
        $invoices = invoice::where("Value_Status", 1)->get();
        return view("invoices.invoice_paid", compact("invoices"));
    }
    public function Invoice_UnPaid()
    {
        $invoices = invoice::where("Value_Status", 2)->get();
        return view("invoices.invoice_unpaid", compact("invoices"));
    }
    public function Invoice_Partial()
    {
        $invoices = invoice::where("Value_Status", 3)->get();
        return view("invoices.invoice_partial", compact("invoices"));
    }

    public function Print_invoice($id)
    {
        $invoices = invoice::where("id", $id)->first();
        return view("invoices.Print_invoice", compact("invoices"));
    }
}
