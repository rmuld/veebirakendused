<?php

function save_news($title, $news,  $expire) {
    $userid = $_SESSION["user_id"];
    //loon andmebaasi ühenduse
    //kasutan globaalseid muutujaid, need, mis on loodud väljaspool funktsiooni: $GLOBALS["server_host"]
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);

    //määran suhtlemise kooditabeli
    $conn->set_charset("utf-8");

    //valmistame ette sql käsu andmete lisamiseks andmetabelisse
    //INSERT INTO VR22_NEWS (title, content, expire, userid) VALUES(?, ?, ?, ?)
    $stmt = $conn->prepare("INSERT INTO VR22_news (title, content, expire, userid) VALUES(?, ?, ?, ?)");
    echo $conn->error;

    //andmetüübid: s - string, i - integer, d - decimal
    $stmt->bind_param("sssi", $title, $news,  $expire, $userid);

    if($stmt->execute()) {
        $notice = "Uudis on salvestatud!";
    } else {
        $notice = "Uudise salvestamise tekkis viga: " . $stmt->error;
    }

    //lõpetan käsu
    $stmt->close();
    //sulgen ühenduse
    $conn->close();

    return $notice;
};


function all_news(){
    $newsHtml = null;
    $today = date("Y-m-d");
    $photoDir = "../../~toivo.parnpuu/vr/vr-rinde/upload_orig/";

    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn->set_charset("utf-8");

    //SELECT title, content, created FROM VR22_news
    $stmt = $conn->prepare("SELECT VR22_news.title, VR22_news.content, VR22_news.created, VR22_news.expire, vr22_photos.filename, vr22_photos.alttext, CONCAT(vr22_users.firstname, ' ', vr22_users.lastname) AS taisNimi FROM VR22_news, vr22_photos, vr22_users
	ORDER BY vr22_photos.id");
    echo $conn->error;
    $stmt->bind_result($title_from_db, $content_from_db, $created_from_db, $expire_from_db, $filename_from_db, $alttext_from_db, $taisNimi_from_db);
    $stmt->execute();
    
    //küsib DB-st seni, kuni sealt on midagi võtta
    while($stmt->fetch()) {
        $newsHtml .= "<h3>" .$title_from_db ."</h3> \n";
        $newsHtml .= "<p>" .$content_from_db ."</p> \n";
        $newsHtml .= '<img src="'.$photoDir . $filename_from_db.'" alt="'. $alttext_from_db .'" style="width: 200px;height:auto" class="thumbs"/>'; 
        $newsHtml .= "<p>Lisatud: " .$created_from_db ."</p> \n";
        $newsHtml .= "<p>Aegub: " .$expire_from_db ."</p> \n";
        $newsHtml .= "<p>Autor: " .$id_from_db ."</p> \n";
    }

    //lõpetan käsu
    $stmt->close();
    //sulgen ühenduse
    $conn->close();

    return $newsHtml;
    
};

function first_five_news() {
    $newsHtml = null;
    $today = date("Y-m-d");
    $photoDir = "../../~toivo.parnpuu/vr/vr-rinde/upload_orig/";

    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn->set_charset("utf-8");

    $stmt = $conn->prepare("SELECT VR22_news.title, VR22_news.content, VR22_news.created, VR22_news.expire, VR_news.userid vr22_photos.filename, vr22_photos.alttext, CONCAT(vr22_users.firstname, ' ', vr22_users.lastname) AS taisNimi FROM VR22_news, vr22_photos, vr22_users LIMIT 5");
    echo $conn->error;
    $stmt->bind_result($title_from_db, $content_from_db, $created_from_db, $expire_from_db, $id_from_db, $filename_from_db, $alttext_from_db, $taisNimi_from_db);
    $stmt->execute();

    while($stmt->fetch()) {
        $newsHtml .= "<h3>" .$title_from_db ."</h3> \n";
        $newsHtml .= "<p>" .$content_from_db ."</p> \n";
        $newsHtml .= "<p>HELLO</p>";
        $newsHtml .= '<img src="'.$photoDir . $filename_from_db.'" alt="'. $alttext_from_db .'" style="width: 200px;height:auto" class="thumbs"/>'; 
        $newsHtml .= "<p>Lisatud: " .$created_from_db ."</p> \n";
        $newsHtml .= "<p>Aegub: " .$expire_from_db ."</p> \n";
        $newsHtml .= "<p>User ID: " .$id_from_db ."</p> \n";
    }

    //lõpetan käsu
    $stmt->close();
    //sulgen ühenduse
    $conn->close();

    return $newsHtml;
}

function read_news($amount) {
    $newsHtml = null;
    $today = date("Y-m-d");

    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn->set_charset("utf-8");

    $stmt = $conn->prepare("SELECT title, content, added, firstname, lastname FROM VR22_news JOIN vr22_users on VR22_news.userid = vr22_users.id WHERE expire >= ? ORDER BY VR22_news.id DESC LIMIT ?");
    echo $conn->error;
    $stmt->bind_param("si", $today, $amount);
    $stmt->bind_result($title_from_db, $content_from_db, $added_from_db, $firstname_from_db, $lastname_from_db);
    $stmt->execute();
    
    
    while($stmt->fetch()) {
        $newsHtml .= "<h3>" .$title_from_db ."</h3> \n";
        $newsHtml .= "<p>" .$content_from_db ."</p> \n";
        $newsHtml .= "<p>Lisas: " .$firstname_from_db ." " .$lastname_from_db .", " .$added_from_db ."</p> \n";
        
    }

    //lõpetan käsu
    $stmt->close();
    //sulgen ühenduse
    $conn->close();

    return $newsHtml;
}

?>