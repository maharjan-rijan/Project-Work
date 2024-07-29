<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Brand;

class BrandController extends Controller
{

    public function index(Request $request){
        $brands = Brand::orderBy('id','ASC');
        if(!empty($request->get('keyword'))) {
            $brands = $brands->where('name','like','%'.$request->get('keyword').'%');
        }
        $brands = $brands->paginate(10);
        $data['brands'] = $brands;
        return view('admin.brands.list', $data);
    }

   Public function create(){
    return view('admin.brands.create');
   }
   public function store(Request $request){
    $validator = Validator::make($request->all(),[
        'name' => 'required',
        'slug' => 'required|unique:brands',
    ]);

    if($validator->passes()) {
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->status;
        $brand->save();

        session()->flash('success','Brand added successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Brand added successfully.'
            ]);
    } else {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
   }
   public function edit($id, Request $request) {
    $brand = Brand::find($id);
    if(empty($brand)){
        session()->flash('error','Record not found.');
        return redirect()->route('brands.index');
    }
    $data['brand'] = $brand;
    return view('admin.brands.edit',$data);
}

public function update($id, Request $request){

    $brand = Brand::find($id);
    if(empty($brand)){
        session()->flash('error','Record not found.');
        return response()->json([
            'status' => false,
            'notFound' => true,
            'message' => 'Record not found.'
         ]);
    }

    $validator = Validator::make($request->all(),[
        'name' => 'required',
        'slug' => 'required|unique:brands,slug,'.$brand->id.',id',
    ]);

    if($validator->passes()) {
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->status = $request->status;
        $brand->save();

        session()->flash('success','Brand updated successfully.');
            return response()->json([
                'status' => true,
                'message' => 'Brand updated successfully.'
            ]);
    } else {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}
public function destroy ($id, Request $request){
    $brand = Brand::find($id);
    if(empty($brand)){
      session()->flash('error','Record not found.');
        return response()->json([
            'status' => false,
            'notFound' => true,
            'message' => 'Record not found.'
         ]);
        }

        $brand->delete();
   session()->flash('success','Brand deleted successfully.');
    return response()->json([
        'status' => true,
        'message' => 'Brand deleted successfully.'
    ]);
}
}
