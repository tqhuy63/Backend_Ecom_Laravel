<?php

namespace App\Http\Controllers\admin;

use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
class TempImagesController extends Controller
{
    public function create(Request $request)
    {
        $image = $request->image;
        
        if(!empty($image)) {
            $ext = $image->getClientOriginalExtension();
            $newName = time().".".$ext;

            $TempImage = new TempImage();
            $TempImage->name = $newName;
            $TempImage->save();

            $image->move(public_path().'/temp', $newName);
            
            //Generate thumbnail
            $manager = new ImageManager(new Driver());
            // read image from file system
            $sourcePath = public_path().'/temp/'.$newName;
            $destPath = public_path().'/temp/thumb/'.$newName;
            $image = $manager->read($sourcePath); // mở ảnh từ đường dẫn chính
            $image->scale(300, 275);
            $image->save($destPath);

            return response()->json([
                'status' => true,
                'image_id' => $TempImage->id,
                'ImagePath' => asset('/temp/thumb/'.$newName),
                'message' => 'Image uploaded successfully!'
            ]);
            
        }
 
    }
}
