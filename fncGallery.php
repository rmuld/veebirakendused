<?php

function all_images(){
    $imagesHtml = null;
    $photoDir = "../../~toivo.parnpuu/vr/vr-rinde/upload_orig/";
    $connect = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $connect->set_charset("utf-8");

    //SELECT title, content, created FROM VR22_news
    $statement = $connect->prepare("SELECT vr22_photos.id, vr22_photos.filename, vr22_photos.alttext, vr22_users.firstname, vr22_users.lastname FROM vr22_photos JOIN vr22_users ON vr22_photos.userid = vr22_users.id WHERE vr22_photos.privacy >= 2 AND vr22_photos.deleted IS NULL GROUP BY vr22_photos.id ORDER BY vr22_photos.id DESC");
    echo $connect->error;
    $statement->bind_result($id_from_db, $filename_from_db, $alttext_from_db, $firstname_from_db, $lastname_from_db);
    $statement->execute();
    
    //küsib DB-st seni, kuni sealt on midagi võtta
    while($statement->fetch()) {
        /* <div class="thumbgallery">
            <img src="gallery_upload_thumb/2424.jpg" alt="mingi pilt" class="thumb" data-id="33" data-filename="kaasik.jpg">
            <p>Riina Muld</p>
            </div>
        */
        $imagesHtml .= '<img src="'.$photoDir . $filename_from_db.'" alt="'. $alttext_from_db .'" data-id="' .$id_from_db .'" data-filename="' .$filename_from_db .'" style="width: 200px;height:auto"/>';
        $imagesHtml .= "<p>" . 'Autor: ' .$firstname_from_db .' ' .$lastname_from_db ."</p> \n";
    }

    $statement->close();
    $connect->close();

    return $imagesHtml;
    
}

function newest_image() {
    $imageHtml = null;
    $photoDir = "../../~toivo.parnpuu/vr/vr-rinde/upload_orig/";
    $connect = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $connect->set_charset("utf-8");

    //SELECT id, filename, alttext, firstname and lastname FROM VR22_photos
    $statement = $connect->prepare("SELECT vr22_photos.id, vr22_photos.filename, vr22_photos.alttext, vr22_users.firstname, vr22_users.lastname FROM vr22_photos JOIN vr22_users ON vr22_photos.userid = vr22_users.id WHERE vr22_photos.privacy = 3 AND vr22_photos.deleted IS NULL GROUP BY vr22_photos.id ORDER BY vr22_photos.id DESC LIMIT 1");
    echo $connect->error;
    $statement->bind_result($id_from_db, $filename_from_db, $alttext_from_db, $firstname_from_db, $lastname_from_db);
    $statement->execute();
    $statement->fetch();

    $imageHtml .= '<img src="'.$photoDir . $filename_from_db.'" alt="'. $alttext_from_db .'" style="width: 200px;height:auto" class="thumbs"/>'; 
    $imageHtml .= "<p>" . 'Piltnik: ' .$firstname_from_db .' ' .$lastname_from_db ."</p> \n";

    $statement->close();
    $connect->close();

    return $imageHtml;
}

function count_photos($privacy){
    $photo_count = 0;
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn->set_charset("utf8");
    $stmt = $conn->prepare("SELECT COUNT(id) FROM vr22_photos WHERE privacy >= ? AND deleted IS NULL");
    echo $conn->error;
    $stmt->bind_param("i", $privacy);
    $stmt->bind_result($count_from_db);
    $stmt->execute();
    if($stmt->fetch()){
        $photo_count = $count_from_db;
    }
    $stmt->close();
    $conn->close();
    return $photo_count;
}

function read_public_photo_thumbs($privacy, $page, $limit){
    $photoDir = "../../~toivo.parnpuu/vr/vr-rinde/upload_thumbnail/";
    $skip = ($page - 1) * $limit;
    $photo_html = null;
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn->set_charset("utf8");
    $stmt = $conn->prepare("SELECT vr22_photos.id, vr22_photos.filename, vr22_photos.alttext, vr22_users.firstname, vr22_users.lastname FROM vr22_photos JOIN vr22_users ON vr22_photos.userid = vr22_users.id WHERE vr22_photos.privacy >= ? AND vr22_photos.deleted IS NULL GROUP BY vr22_photos.id ORDER BY vr22_photos.id DESC LIMIT ?, ?");
    echo $conn->error;
    $stmt->bind_param("iii", $privacy, $skip, $limit);
    $stmt->bind_result($id_from_db, $filename_from_db, $alttext_from_db, $firstname_from_db, $lastname_from_db);
    $stmt->execute();
    while($stmt->fetch()){
        $photo_html .= '<div class="thumbgallery">' ."\n";
        $photo_html .= '<img src="'.$photoDir .$filename_from_db .'" data-id="' .$id_from_db .'" data-filename="' .$filename_from_db .'" alt="';
        if(empty($alttext_from_db)){
            $photo_html .= "Üleslaetud foto";
        } else {
            $photo_html .= $alttext_from_db;
        }
        $photo_html .= '" class="thumbs">' ."\n";
        $photo_html .= "<p>" .$firstname_from_db ." " .$lastname_from_db ."</p> \n";
        $photo_html .= "</div> \n";
    }
    if(empty($photo_html)){
        $photo_html = "<p>Kahjuks pole ühtegi avalikku fotot üles laetud!</p>";
    }
    $stmt->close();
    $conn->close();
    return $photo_html;
}

?>