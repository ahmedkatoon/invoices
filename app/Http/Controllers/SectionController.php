<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = Section::all();
        return view("sections.sections", ["sections" => $sections]);
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
        $validationData = $request->validate(
            [
                "section_name" => "required|unique:sections,section_name",
                "description" => "required|unique:sections,description"
            ],
            [
                "section_name.required" => "يرجي ادخال اسم القسم",
                "section_name.unique" => "اسم القسم مسجل مسبقا",
                "description.required" => "يرجي ادخال البيان"

            ]
        );


        Section::create([
            "section_name" => $request->section_name,
            "description" => $request->description,
            "created_by" => Auth::user()->name
        ]);
        session()->flash("add", "تم اضافه القسم بنجاح");
        return redirect("sections");


        // $data = $request->all();

        // $exist = Section::where("section_name", "=", $data["section_name"])->exists();

        // if ($exist ) {
        //     session()->flash("error", "خطأ القسم مسجل مسبقا");
        //     return redirect("sections");
        // } else {
        //     Section::create([
        //         "section_name" => $request->section_name,
        //         "description" => $request->description,
        //         "created_by" => Auth::user()->name
        //     ]);
        // }
        // session()->flash("add", "تم اضافه القسم بنجاح");
        // return redirect("sections");
    }

    /**
     * Display the specified resource.
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $validationData = $request->validate(
            [
                "section_name" => "required|unique:sections,section_name",
                "description" => "required|unique:sections,description"
            ],

            [
                "section_name.required" => "يرجي ادخال اسم القسم",
                "section_name.unique" => "اسم القسم مسجل مسبقا",
                "description.required" => "يرجي ادخال البيان"

            ]
        );

        $sections = Section::find($id);
        $sections->update($validationData);
        session()->flash('edit','تم تعديل القسم بنجاج');
        return redirect("sections");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        Section::find($id)->delete();
        session()->flash('delete','تم حذف القسم بنجاح');
        return redirect('/sections');
    }

}
