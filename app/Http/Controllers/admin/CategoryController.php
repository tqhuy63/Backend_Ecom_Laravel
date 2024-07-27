<?php

namespace App\Http\Controllers\admin;
require '../vendor/autoload.php';
use App\Models\Category;
use App\Models\TempImage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Laravel\Facades\Image;

class CategoryController extends Controller
{
    /**
 * Display a ing of the resource.
     */
    public function index(Request $request)
    {
        // search
        $categories = Category::latest();
        if (!empty($request->get('keyword'))) {
            $categories = $categories->where('name', 'like', '%' . $request->get('keyword') . '%');
        }
        $categories = $categories->paginate(10);
        return view('admin.category.list', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);
        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status ?? 1;
            $category->save();

            // save image here
                if (!empty($request->image_id)) {
                    $tempImage = TempImage::find($request->image_id);
                    $extArray = explode('.', $tempImage->name);
                    $ext = last($extArray);

                    $newImageName = $category->id.'.'.$ext; // thêm tên mới cho Image bằng cách gộp Id category và phần mở rộng của image
                    $sPath = public_path(). '/temp/'.$tempImage->name; // lưu trữ tạm
                    $dPath = public_path().'/uploads/category/'.$newImageName; 
                    File::copy($sPath, $dPath); // coppy từ đường dẫn tạm thời sang đường dẫn chính

                    // Generate Image Thumbnail
                    $manager = new ImageManager(new Driver());
                    // read image from file system
                    $img = $manager->read($sPath); // mở ảnh từ đường dẫn chính
                    $dPath = public_path().'/uploads/category/thumb/'.$newImageName; // đặt đường dẫn chính
                    $img->cover(450, 600); // resize
                    $img->save($dPath); // lưu 
                    // Image Crop// Lưu hình ảnh đã resize vào thư mục thumb
                    $category->image = $newImageName;
                    $category->save();
                }

            $request->session()->flash('success', 'Category added successfully!');
            return response()->json([
                'status' => true,
                'message' => 'Category added successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $Category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($categoryId, Request $request)
    {   
        $category = Category::find($categoryId);
        if (empty($category)) {
            return redirect()->route('categories.index');
        }
        return view('admin.category.edit', compact('category'));
        // return redirect()->route('categories.edit', ['category' => $category->id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $categoryId)
    {   
        $category = Category::find($categoryId);
        if (empty($category)) {
            $request->session()->flash('error', 'Category not found!');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->id.',id',
        ]);
        if ($validator->passes()) {

            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status ?? 1;
            $category->save();

            $oldImage = $category->image;

            // save image here
                if (!empty($request->image_id)) {
                    $tempImage = TempImage::find($request->image_id);
                    $extArray = explode('.', $tempImage->name);
                    $ext = last($extArray);

                    $newImageName = $category->id.'-'.time().'.'.$ext; // thêm tên mới cho Image bằng cách gộp Id category và phần mở rộng của image
                    $sPath = public_path(). '/temp/'.$tempImage->name; // lưu trữ tạm
                    $dPath = public_path().'/uploads/category/'.$newImageName; 
                    File::copy($sPath, $dPath); // coppy từ đường dẫn tạm thời sang đường dẫn chính

                    // Generate Image Thumbnail
                    $manager = new ImageManager(new Driver());
                    // read image from file system
                    $img = $manager->read($sPath); // mở ảnh từ đường dẫn chính
                    $dPath = public_path().'/uploads/category/thumb/'.$newImageName; // đặt đường dẫn chính
                    $img->cover(450, 600); // resize
                    $img->save($dPath); // lưu 
                    // Image Crop// Lưu hình ảnh đã resize vào thư mục thumb
                    $category->image = $newImageName;
                    $category->save();

                    // delete old images 
                    File::delete(public_path().'/uploads/category/thumb/'.$oldImage);
                    File::delete(public_path().'/uploads/category/'.$oldImage);
                }

            $request->session()->flash('success', 'Category updated successfully!');
            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($categoryId, Request $request)
    {   
        $category = Category::find($categoryId);
        if (empty($category)) {
            $request->session()->flash('error', 'Category not found!');
            return response()->json([
                'status' => true,
                'message' => 'Category not found!'
            ]);
            // return redirect()->route('categories.index');
        }
         
        File::delete(public_path().'/uploads/category/thumb/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image);
        $category->delete();
        $request->session()->flash('success', 'Category deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]); 
    }
}
