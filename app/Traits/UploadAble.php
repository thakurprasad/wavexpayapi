<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

/**
 * Trait UploadAble
 * @package App\Traits
 */
trait UploadAble
{
    /**
     * @param UploadedFile $file
     * @param null $folder
     * @param string $disk
     * @param null $filename
     * @return false|string
     */
    public function uploadOne(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        // Default Extension
        $ext = 'png';

		$name = !is_null($filename) ? $filename : Str::random(25) . "." . $ext;
		//$name = $name . "." . $file->getClientOriginalExtension();

		$actualImage = $folder.'/'.$name;

		// Resizing and stroing in resize by size folder in disk, ex <<SCHOOL_CODE>>/userProfile/60x60/
		$this->imageResizeAndSaveB2B($file, $folder, $name, 60, 60, $disk);
		// Resizing and stroing in resize by size folder in disk, ex <<SCHOOL_CODE>>/userProfile/350x350/
		$this->imageResizeAndSaveB2B($file, $folder, $name, 350, 350, $disk);

		// Now saving the original image
		$image = Image::make($file)->stream();
		Storage::disk($disk)->put($actualImage, $image->__toString(), 'public');

		// Now retruning the original image url
		return Storage::disk($disk)->url($actualImage);
    }

	/**
     * @param uploadRawOne base64 $file
     * @param null $folder
     * @param string $disk
     * @param null $filename
     * @return false|string
     */
    //$path_return = 0 => Full Path, 1 => File Name
    public function uploadRawOne($file, $folder = null, $disk = 'public', $filename = null, $path_return = 0)
    {
		// Default Extension
        $ext = 'png';

		// Naming if required
		$name = !is_null($filename) ? $filename : Str::random(25);
		$name = $name . "." . $ext;

		// Name with folder
		$actualImage = $folder.'/'.$name;

		// Resizing and stroing in resize by size folder in disk, ex <<SCHOOL_CODE>>/userProfile/60x60/
		$this->imageResizeAndSaveB2B($file, $folder, $name, 60, 60, $disk);
		// Resizing and stroing in resize by size folder in disk, ex <<SCHOOL_CODE>>/userProfile/350x350/
		$this->imageResizeAndSaveB2B($file, $folder, $name, 350, 350, $disk);

		// Now saving the original image
		$image = Image::make($file)->stream();
		Storage::disk($disk)->put($actualImage, $image->__toString(), 'public');

		// Now retruning the original image url
        Storage::disk($disk)->url($actualImage);

        if($path_return)
            return $name;
        else
		    return $actualImage;

    }

    public function uploadMoveFile($data,$filename,$destination)
    {
        if ($data === false) {
            return false;
        }
        Storage::disk('s3')->put($destination."/".$filename, $data, 'public');
        return $filename;
    }

	public function uploadRawPDFOne($file, $folder = null, $disk = 'public', $filename = null, $path_return = 0)
    {

		$name = !is_null($filename) ? $filename : Str::random(25);

		$ext = 'pdf';

		$name = $name . "." . $ext;

		$actualImage = $folder.'/'.$name;

		//$storage_path = Storage::disk($disk)->path('public/'.$actualImage);
		//Storage::disk($disk)->put('public/'.$actualImage, base64_decode($file));

        Storage::disk($disk)->put($actualImage, base64_decode($file));

		$full_path =  Storage::disk($disk)->url($actualImage);

        //return $actualImage;
        if($path_return)
            return $name;
        else
		    return $full_path;
    }

	public function uploadByMove($file, $folder = null, $disk = 'public', $filename)
    {
		$name = $filename;
		$actualImage = $folder.'/'.$name;
		$text = $folder.'/'.'sss.txt';
        //$file->move(storage_path('app/public/'.$folder), $name);
		//Storage::disk($disk)->put($actualImage, $file);
		//$storage_path     = Storage::disk($disk)->path('public/'.$actualImage);
		//Storage::disk($disk)->put('public/'.$actualImage, base64_decode($file));
        //File::move($file, $storage_path);
		//echo $file;echo $storage_path;
		//rename($file, $storage_path);
		
		//Storage::disk($disk)->put($actualImage, $file, 'public');
		Storage::disk($disk)->put($actualImage, file_get_contents($file), 'public');
		
		//Storage::disk($disk)->move($img, $moveTo);
		
        return $name;
    }

   public  function uploadPDF(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {

         $name = !is_null($filename) ? $filename : Str::random(25);

        $actualPDF = $file->storeAs(
            $folder,
            $name . "." . $file->getClientOriginalExtension(),
            $disk
        );


        return $actualPDF;


    }
	
	public  function uploadZIP(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
	{

		$name = !is_null($filename) ? $filename : Str::random(25);

		$actualZIP = $file->storeAs(
		$folder,
		$name,
		$disk
		);


		return $actualZIP;


	}

    /**
     * @param null $path
     * @param string $disk
     */
    public function deleteOne($path = null, $disk = 'public', $remove_thumbnail=false)
    {
        Storage::disk($disk)->delete($path);

        $foldername = substr($path,0,12);
        $filename = substr($path,13);

        if($remove_thumbnail){

            $patharr = explode("/", $path);
            $foldername = isset($patharr[0]) ? $patharr[0] : false;
            $filename = isset($patharr[1]) ? $patharr[1] : false;

            if($foldername && $filename){
                Storage::disk($disk)->delete($foldername.'/350x350/'.$filename);
                Storage::disk($disk)->delete($foldername.'/60x60/'.$filename);
            }


        } else {
            Storage::disk($disk)->delete($foldername.'/350x350/'.$filename);
            Storage::disk($disk)->delete($foldername.'/60x60/'.$filename);
        }


    }

    /**
     * @param UploadedFile $file
     * @param null $folder
     * @param string $disk
     * @param null $filename
     * @return false|string
     */
    public function uploadCSV(UploadedFile $file, $folder = null, $disk = 'public', $filename = null)
    {
        $name = !is_null($filename) ? $filename : str_random(25);

        $actualFile = $file->storeAs(
            $folder,
            $name . "." . $file->getClientOriginalExtension(),
            $disk
        );

        return $actualFile;
    }

    public function downloadOne($file){

        $pathToFile = storage_path('app/public/'.$file);
        return response()->download($pathToFile);


    }

	// Resizing Raw base64 Image
	public function imageResizeAndSaveB2B($file, $folder, $name, $width, $height, $disk = 'public')
    {

        if (!empty($file)) {

			$filePath = $folder.'/'.$width.'x'.$height.'/'.$name;
			$image = Image::make($file);
			$image->height() > $image->width() ? $width=null : $height=null;
			$image = Image::make($file)->resize($width, $height, function ($const) {
			$const->aspectRatio();
			})->stream();
			Storage::disk($disk)->put($filePath, $image->__toString(), 'public');

            return true;
        } else { return false; }
    }

}