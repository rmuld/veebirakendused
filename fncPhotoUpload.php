<?php

function createFileName($photoNamePrefix, $fileType){
    $timestamp = microtime(1) * 10000;
    return $photoNamePrefix .$timestamp ."." .$fileType;
}

function createImage($file, $fileType) {
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

function resizePhoto($src, $width, $height){
    //originaalsuurus
    $imageWidth = imagesx($src);
    $imageHeight = imagesy($src);
    $newHeight = $imageHeight;
    $newWidth = $imageWidth;

    //kas laiuse või kõrguse järgi
    if($imageWidth/$width > $imageHeight/$height){
        $newHeight = round($imageHeight/($imageWidth / $width));

    } else {
        $newWidth = round($imageWidth/($imageHeight / $height));
    }

    //uue suurusega image
    $tempImage = imagecreatetruecolor($newWidth, $newHeight);

    //säilitame png läbipaistvuse
    // imagesavealpha($tempImage, true);
    // $transColor = imagecolorallocateaplha($tempImage, 0, 0, 0, 127);
    // imagefill($tempImage, 0, 0, $transColor);

    //kuhu imagesse, kust imagest, kuhu x, kuhu y, kust x, kust y, kuhu kui laialt, kuhu kui kõrgelt, kust kui laialt, kust kui kõrgelt
    imagecopyresampled($tempImage, $src, 0, 0, 0, 0, $newWidth, $newHeight, $imageWidth, $imageHeight);
    return $tempImage;
}

function resizeToThumb($src, $width, $height){
    //originaalsuurus
    $imageWidth = imagesx($src);
    $imageHeight = imagesy($src);
    $newHeight = $imageHeight;
    $newWidth = $imageWidth;

    //x ja y thumbnaili jaoks
    $destY = intval(($height - $newHeight) / 2);
    $destX = intval(($width - $newWidth) / 2);

    //uue suurusega image
    $tempImage = imagecreatetruecolor($newWidth, $newHeight);
    //kuhu imagesse, kust imagest, kuhu x, kuhu y, kust x, kust y, kuhu kui laialt, kuhu kui kõrgelt, kust kui laialt, kust kui kõrgelt
    imagecopyresampled($tempImage, $src, $destX, $destY, 0, 0, $newWidth, $newHeight, $imageWidth, $imageHeight);
    return $tempImage;

    echo "Thumbnail resized!";
    return;
};

function saveImage($image, $target, $fileType){
    $notice = null; 

    if($fileType == "jpg"){
        if(imagejpeg($image, $target, 95)){
            $notice = "Salvestamine õnnestus!";
        } else {
            $notice = "Salvestamisel tekkis tõrge!";
        }
    }

    if($fileType == "png"){
        if(imagepng($image, $target, 6)){
            $notice = "Salvestamine õnnestus!";
        } else {
            $notice = "Salvestamisel tekkis tõrge!";
        }
    }

    if($fileType == "gif"){
        if(imagegif($image, $target)){
            $notice = "Salvestamine õnnestus!";
        } else {
            $notice = "Salvestamisel tekkis tõrge!";
        }
    }
    return $notice;
}

function saveThumbnail(){
    echo "Thumbnail saved!";
    return;
};

function storePhotoData($name, $alt, $privacy){
    $notice = null;
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn->set_charset("utf8");
    $stmt = $conn->prepare("INSERT INTO vr22_photos (userid, filename, alttext, privacy) VALUES (?, ?, ?, ?)");
    echo $conn->error;
    $stmt->bind_param("issi", $_SESSION["user_id"], $name, $alt, $privacy);
    if($stmt->execute()){
        $notice = "Foto lisati andmebaasi!";
    } else {
        $notice = "Foto lisamisel andmebaasi tekkis tõrge: " .$stmt->error;
    }
    
    $stmt->close();
    $conn->close();
    return $notice;
}