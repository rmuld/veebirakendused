<?php
class PhotoUpload {
    private $photoToUpload;
    private $fileType; //alguses saadame parameetrina, hiljem teeb klass selle ise kindlaks
    private $tempImage;
    private $newTempImage;
    public $error;
    public $fileName;

    function __construct($photo){
        $this->photoToUpload = $photo;
        $this->error = null;
        $this->checkImage();
        if(empty($this->error)){
            $this->tempImage = $this->createImageFromFile($this->photoToUpload["tmp_name"], $this->fileType);
        }             
    }

    function __destruct(){
        if(isset($this->tempImage)){
            imagedestroy($this->tempImage);
        }
        if(isset($this->newTempImage)){
            @imagedestroy($this->newTempImage);
        }
    }

    private function checkImage(){
        $error = null;
        $imageCheck = getimagesize($this->photoToUpload["tmp_name"]);
        if($imageCheck !== false){
            if($imageCheck["mime"] == "image/jpeg"){
                $this->fileType = "jpg";
            }
            if($imageCheck["mime"] == "image/png"){
                $this->fileType = "png";
            }
            if($imageCheck["mime"] == "image/gif"){
                $this->fileType = "gif";
            }
            
        } else {
            $error = "Valitud fail ei ole pilt!";
            $this->error = $error;
        }
    }

    public function checkSize($limit){
        $error = null;
        if($this->photoToUpload["size"] > $limit){
            $error = "Valitud fail on liiga suur!";
            $this->error = $error;
        }
    }

    public function checkAllowedType($allowedTypes){
        $error = null;
        $fileInfo = getimagesize($this->photoToUpload["tmp_name"]);
        if(isset($fileInfo["mime"])){
            if(!in_array($fileInfo["mime"], $allowedTypes)) {
                $error = "Valitud foto fail pole lubatud tüüpi!";
                $this->error = $error;
            } else {
                $error = "Valitud faili tüüpi ei õnnestu kontrollida!";
                $this->error = $error;
            }
        }
    }

    public function createFileName($prefix){
        $timeStamp = microtime(1) * 10000;
        $this->fileName = $prefix .$timeStamp ."." .$this->fileType;
    }

    private function createImageFromFile($file, $fileType){
        $tempImage = null;

        if($fileType == "jpg"){
            $tempImage = imagecreatefromjpeg($file);
        }
        if($fileType == "png"){
            $tempImage = imagecreatefrompng($file);
        }
        if($fileType == "gif"){
            $tempImage = imagecreatefromgif($file);
        }
        return $tempImage;
    }

    private function resizePhoto($width, $height){
        //originaalsuurus
        $imageWidth = imagesx($this->tempImage);
        $imageHeight = imagesy($this->tempImage);
        $newHeight = $imageHeight;
        $newWidth = $imageWidth;

        //kas laiuse või kõrguse järgi
        if($imageWidth/$width > $imageHeight/$height){
            $newHeight = round($imageHeight/($imageWidth / $width));

        } else {
            $newWidth = round($imageWidth/($imageHeight / $height));
        }

        //uue suurusega image
        $this->newTempImage = imagecreatetruecolor($newWidth, $newHeight);

        //säilitame png läbipaistvuse
        // imagesavealpha($tempImage, true);
        // $transColor = imagecolorallocateaplha($tempImage, 0, 0, 0, 127);
        // imagefill($tempImage, 0, 0, $transColor);

        //kuhu imagesse, kust imagest, kuhu x, kuhu y, kust x, kust y, kuhu kui laialt, kuhu kui kõrgelt, kust kui laialt, kust kui kõrgelt
        imagecopyresampled($this->newTempImage, $this->tempImage, 0, 0, 0, 0, $newWidth, $newHeight, $imageWidth, $imageHeight);
        //return $tempImage;
    }

    function resize_photo($w, $h, $keep_orig_proportion = true){
		$image_w = imagesx($this->tempImage);
		$image_h = imagesy($this->tempImage);
		$new_w = $w;
		$new_h = $h;
		$cut_x = 0;
		$cut_y = 0;
		$cut_size_w = $image_w;
		$cut_size_h = $image_h;
		
		if($w == $h){
			if($image_w > $image_h){
				$cut_size_w = $image_h;
				$cut_x = round(($image_w - $cut_size_w) / 2);
			} else {
				$cut_size_h = $image_w;
				$cut_y = round(($image_h - $cut_size_h) / 2);
			}	
		} elseif($keep_orig_proportion){//kui tuleb originaaproportsioone säilitada
			if($image_w / $w > $image_h / $h){
				$new_h = round($image_h / ($image_w / $w));
			} else {
				$new_w = round($image_w / ($image_h / $h));
			}
		} else { //kui on vaja kindlasti etteantud suurust, ehk pisut ka kärpida
			if($image_w / $w < $image_h / $h){
				$cut_size_h = round($image_w / $w * $h);
				$cut_y = round(($image_h - $cut_size_h) / 2);
			} else {
				$cut_size_w = round($image_h / $h * $w);
				$cut_x = round(($image_w - $cut_size_w) / 2);
			}
		}
			
		//loome uue ajutise pildiobjekti
		$this->newTempImage = imagecreatetruecolor($new_w, $new_h);
        //säilitame vajadusel läbipaistvuse (png ja gif piltide jaoks
        imagesavealpha($this->newTempImage, true);
        $trans_color = imagecolorallocatealpha($this->newTempImage, 0, 0, 0, 127);
        imagefill($this->newTempImage, 0, 0, $trans_color);
        
		imagecopyresampled($this->newTempImage, $this->tempImage, 0, 0, $cut_x, $cut_y, $new_w, $new_h, $cut_size_w, $cut_size_h);
		//class function - returni ei ole vaja
        //return $temp_image;
	}

    public function addWatermark($watermark){
        $watermarkFileType = strtolower(pathinfo($watermark, PATHINFO_EXTENSION));
        $watermarkImage = $this->createImageFromFile($watermark, $watermarkFileType);
        $watermarkW = imagesx($watermarkImage);
        $watermarkH = imagesy($watermarkImage);
        $watermarkX = imagesx($this->newTempImage) - $watermarkW - 10;
        $watermarkY = imagesy($this->newTempImage) - $watermarkH - 10;
        imagecopy($this->newTempImage, $watermarkImage, $watermarkX, $watermarkY, 0, 0, $watermarkW, $watermarkH);
        imagedestroy($watermarkImage);
    }

    public function moveOrigPhoto($target){
        $notice = null;
        if(move_uploaded_file($this->photoToUpload["tmp_name"], $target)){
            $notice .= " Originaalfoto laeti üles!";
        } else {
            $notice .= " Originaalfoto üleslaadimine ei õnnestunud!";
        }
        return $notice;
    }

    function saveImage($target){
        $notice = null; 
    
        if($this->fileType == "jpg"){
            if(imagejpeg($this->newTempImage, $target, 95)){
                $notice = "Salvestamine õnnestus!";
            } else {
                $notice = "Salvestamisel tekkis tõrge!";
            }
        }
    
        if($this->fileType == "png"){
            if(imagepng($this->newTempImage, $target, 6)){
                $notice = "Salvestamine õnnestus!";
            } else {
                $notice = "Salvestamisel tekkis tõrge!";
            }
        }
    
        if($this->fileType == "gif"){
            if(imagegif($this->newTempImage, $target)){
                $notice = "Salvestamine õnnestus!";
            } else {
                $notice = "Salvestamisel tekkis tõrge!";
            }
        }

        imagedestroy($this->newTempImage);

        return $notice;
    }
}//class lõpp