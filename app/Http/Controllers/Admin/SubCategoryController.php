<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    public function index(Request $request){
        $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
                                ->orderBy('sub_categories.id')->leftJoin('categories','categories.id','sub_categories.category_id');
        if(!empty($request->get('keyword'))) {
            $subCategories = $subCategories->where('sub_categories.name','like','%'.$request->get('keyword').'%');
            $subCategories = $subCategories->orwhere('categories.name','like','%'.$request->get('keyword').'%');
        }
        $subCategories = $subCategories->paginate(10);
        $data['subCategories'] = $subCategories;
        return view('admin.sub_category.list', $data);
    }

    public function create(){
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        return view('admin.sub_category.create',$data);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required'
        ]);
        if($validator->passes()) {
            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->showHome = $request->showHome;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            session()->flash('success','Sub Category added successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Sub Category added successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function edit($id, Request $request) {
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
            session()->flash('error','Record not found.');
            return redirect()->route('sub-categories.index');
        }
        $categories = Category::orderBy('name','ASC')->get();
        $data['categories'] = $categories;
        $data['subCategory'] = $subCategory;
        return view('admin.sub_category.edit',$data);
    }

    public function update($id, Request $request){
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
        session()->flash('error','Record not found.');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Record not found.'
             ]);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$subCategory->id.',id',
            'category' => 'required',
            'status' => 'required'
        ]);
        if($validator->passes()) {
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->showHome = $request->showHome;
            $subCategory->category_id = $request->category;
            $subCategory->save();

          session()->flash('success','Sub Category updated successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Sub Category updated successfully.'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy ($id, Request $request){
        $subCategory = SubCategory::find($id);
        if(empty($subCategory)){
          session()->flash('error','Record not found.');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Record not found.'
             ]);
            }

            $subCategory->delete();
    session()->flash('success','Sub Category deleted successfully.');
        return response()->json([
            'status' => true,
            'message' => 'Sub Category deleted successfully.'
        ]);
    }

}
