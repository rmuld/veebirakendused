<?php
    require_once "useSession.php";	
	require_once "../../conf.php";
    require_once "fncGeneral.php" ;
    require_once "fncPhotoUpload.php" ;
    require_once "classes/Generic.class.php" ;
    require_once "classes/PhotoUpload.class.php" ;

    //testin classi kasutamist
    $genericObject = new Generic(8);
    //väljastan avaliku väärtuse (privaatset väärtust väljastada ei saa)
    echo "Klassi avalik väärtus on: " .$genericObject->justValue;
    $genericObject->reveal();
    //unset käsiga saame muutja ära nullida
    unset($genericObject);

    $photo_error = null;
    $photo_upload_notice = null;

    $altText = null;
    $privacy = 1;
    $fileName = null;
    $fileType = null;
    //muutujad, mis võiks oall conf failis
    $photoUploadSizeLimit = 1024 * 1024 * 1;
    $galleryPhotoOrigFolder = "/home/toivo.parnpuu/public_html/vr/vr-rinde/upload_orig/";
    $galleryPhotoNormalFolder = "/home/toivo.parnpuu/public_html/vr/vr-rinde/upload_normal/";
    $galleryPhotoThumbnailFolder = "/home/toivo.parnpuu/public_html/vr/vr-rinde/upload_thumbnail/";
    $watermark = "assets/vr_watermark.png";
    $photoNamePrefix = "rm_";
    $normalPhotoMaxWidth = 600;
    $normalPhotoMaxHeight = 400;
    $thumbnailWidth = $thumbnailHeight = 100;

    
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if(isset ($_POST["photo_submit"])){
            //vaatame, mis jõuab üles
            //var_dump($_POST);
            //var_dump($_FILES);

            


            //kas on olemas pilt
            if (isset($_FILES["photo_input"]["tmp_name"]) and !empty($_FILES["photo_input"]["tmp_name"])){
                //pildifail on valitud

                //kas on foto
                $imageCheck = getimagesize($_FILES["photo_input"]["tmp_name"]);
                if($imageCheck !== false){
                    if($imageCheck["mime"] == "image/jpeg"){
                        $fileType = "jpg";
                    }
                    if($imageCheck["mime"] == "image/png"){
                        $fileType = "png";
                    }
                    if($imageCheck["mime"] == "image/gif"){
                        $fileType = "gif";
                    }

                    //tuleks kontrollida, kas on lubatud formaat (jpg, png, gif või osa neist)
                } else {
                    $photo_error = "Valitud fail pole foto!";
                }

                //kui on foto, kas on lubatud faili maht
                if($photo_error == null and $_FILES["photo_input"]["size"] > $photoUploadSizeLimit){
                    $photo_error = "Valitud fail on liiga suur!";
                }

                //kas alt tekst on ok
                if(isset($_POST["alt_input"]) and !empty($_POST["alt_input"])){
                    $altText = test_input($_POST["alt_input"]);

                    if(empty($altText)){
                        $photo_error = "Alt text puudu!";
                    }
                } else {                
                    $photo_error = "Sisestasid mingi jama!";
                    $altText = "";
                }

                //TODO: võiks "puhastada" Alt teksti, st pole kohustuslik sisestamine

                //TODO: võiks kontrollida, kas radio button on valitud

                //kui kõik on korras, siis laeme üles
                if($photo_error == null){
                    //võtame kasutusele klassi
                    $upload = new PhotoUpload($_FILES["photo_input"], $fileType);
                    
                    //loon uue failinime
                    $fileName = createFileName($photoNamePrefix, $fileType);

                    //lisan absoluutväärtuse võimaluse: false -> säilitab serva suhte; true -> peab kärpima e õiges proportsioonis tüki lõikama
                    $isAbsolute = false;

                    //suuruse muutmine
                    //$myTempImage = createImage($_FILES["photo_input"]["tmp_name"], $fileType);
                   // $myNormalImage = resizePhoto($myTempImage, $normalPhotoMaxWidth, $normalPhotoMaxHeight);
                    //$myThumbnailImage = resizeToThumb($myTempImage, $thumbnailWidth, $thumbnailHeight);

                    //suuruse muutmine klassiga
                    $upload->resize_photo( $normalPhotoMaxWidth, $normalPhotoMaxHeight);

                    //lisan vesimärgi
                    $upload->addWatermark($watermark);

                    //salvestame muudetud/õige suurusega pildi soovitud kohta
                    //$photo_upload_notice = saveImage($myNormalImage, $galleryPhotoNormalFolder .$fileName, $fileType);
                    $photo_upload_notice = $upload->saveImage($galleryPhotoNormalFolder .$fileName);

                    //salvestame thumbnaili soovitud kohta
                    //$photo_upload_notice = saveImage($myThumbnailImage, $galleryPhotoThumbnailFolder .$fileName, $fileType);
                    $upload->resize_photo($thumbnailWidth, $thumbnailHeight);
                    $photo_upload_notice = $upload->saveImage($galleryPhotoThumbnailFolder .$fileName);

                    //kopeerime originaali soovitud kohta
                    //move_uploaded_file($_FILES["photo_input"]["tmp_name"], $galleryPhotoOrigFolder .$fileName);
                    $photo_upload_notice = $upload->moveOrigPhoto($galleryPhotoOrigFolder .$fileName);

                    //talletame andmebaasi
                    $photo_upload_notice .= storePhotoData($fileName, $_POST["alt_input"], $_POST["privacy_input"]);

                    //tühjendame mälu
                    //imagedestroy($myTempImage);
                    //imagedestroy($myNormalImage);
                    //imagedestroy($myThumbnailImage);

                    //kui kõik on tehtud, siis laseme klassi minna..
                    unset($upload);
                }

            } else {
                $photo_error = "Pildifaili pole valitud!";
            }

            if($photo_upload_notice == null) {
                $photo_upload_notice = $photo_error;
            }
        }
    }
    
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $_SESSION["firstname"]; ?> teeb veebi</title>
	<link rel="stylesheet" type="text/css" href="styles/stiilid.css">
    <script src="JS/fileCheck.js" defer></script> 
</head>
<body>
	<header>
        <img id="banner" src="../../~andrus.rinde/media/pic/rif21_banner.png" alt="RIF21" width="auto" height="auto">
		<h1><?php echo $_SESSION["firstname"] ." " .$_SESSION["lastname"]; ?> arendab veebi</h1>		
	</header>
	
	<?php
    require_once "pagenav.php";
    ?>
	<main>
	<section>
			

		<h2>Foto üleslaadimine</h2>
		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
			<label for="photo_input"> Vali pildifail! </label>
			<input type="file" name="photo_input" id="photo_input">
			<br>
			<label for="alt_input">Alternatiivtekst (alt): </label>
			<input type="text" name="alt_input" id="alt_input" placeholder="alternatiivtekst" value="<?php echo $altText; ?>">
			<br>
			<input type="radio" name="privacy_input" id="privacy_input_1" value="1" <?php if($privacy == 1){echo " checked";} ?>>
			<label for="privacy_input_1">Privaatne (ainult mina näen)</label>
			<br>
			<input type="radio" name="privacy_input" id="privacy_input_2" value="2" <?php if($privacy == 2){echo " checked";} ?>>
			<label for="privacy_input_2">Sisseloginud kasutajatele</label>
			<br>
			<input type="radio" name="privacy_input" id="privacy_input_3" value="3" <?php if($privacy == 3){echo " checked";} ?>>
			<label for="privacy_input_3">Avalik (kõik näevad)</label>
			<br>
			<input type="submit" name="photo_submit" id="photo_submit" value="Lae pilt üles">
		</form>
		<span id="notice"><?php echo $photo_upload_notice; ?></span>
	</section>
	<?php
		require_once "pagefooter.php";
	?>