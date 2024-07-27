<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request) {
        $brands = Brand::latest();
        if (!empty($request->get('keyword'))) {
            $brands = $brands->where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        $brands = $brands->paginate(10);
        return view('admin.brands.list', compact('brands'));
    }
    public function create() { 
        $brands = Brand::orderBy('name', 'ASC')->get();
        $data['brands'] = $brands;  
        return view('admin.brands.create', $data);
    }
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required:unique:brands',
        ]);
        if ($validator->passes()) {
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success', 'Brand added successfully!');

            return response()->json([
                'status' => true,
                'message' => 'Brand addedd successfully! '
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
        if (empty($brand)) {
            $request->session()->flash('error', 'Brand not Found.');
            return redirect()->route('brands.index');
        }
        $data['brand'] = $brand;
        return view('admin.brands.edit', $data);
        
    }
    public function update(Request $request, $id) {
        $brand = Brand::find($id);
        if (empty($brand)) {
            $request->session()->flash('errors', 'Record not found');
            return response([
                'status' => false,
                'notFound' => true
            ]);
            // return redirect()->route('sub-categories.index');
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$brand->id.',id',
            'status' => 'required'
        ]);
        if ($validator->passes()) {

            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success', 'Brand updated successfully!');

            return response([
                'status' => true,
                'message' => 'Brand updated successfully!',
                
            ]);
        } else {
            return response([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function destroy($id, Request $request) {
        $brand = Brand::find($id);
        if (empty($brand)) {
            $request->session()->flash('error', 'Record not found');
            return response([
                'status' => false,
                'notFound' => true
            ]);
        }
        $brand->delete();
        $request->session()->flash('success', 'Brand deleted successfully!');
        return response([
            'status' => true,
            'message' => 'Sub Category deleted successfully'
        ]);
    }
}
