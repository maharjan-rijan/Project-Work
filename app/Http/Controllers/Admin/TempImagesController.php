<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


class TempImagesController extends Controller
{
    public function create(Request $request){
        $image = $request->image;
        if(!empty($image)){
            $ext = $image->getClientOriginalExtension();

            $tempImage = new TempImage();
            $tempImage->name = 'TEST';
            $tempImage->save();

            $newName = $tempImage->id.'-'.time().'.'.$ext;
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/temp',$newName);

            //Generate Thumbnail
            $manager = new ImageManager(new Driver());
            $sPath = public_path().'/temp/'.$newName;
            $dPath = public_path().'/temp/thumb/'.$newName;
            $image = $manager->read($sPath);
            $image->cover(300,275);
            $image->save($dPath);
            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'ImagePath' => asset('/temp/thumb/'.$newName),
                'message' => 'Image uploaded successfully.'
            ]);
        }
    }
}
