<?php

function save_news($title, $news,  $expire) {
    $userid = $_SESSION["user_id"];
    //loon andmebaasi ühenduse
    //kasutan globaalseid muutujaid, need, mis on loodud väljaspool funktsiooni: $GLOBALS["server_host"]
    $connect = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);

    //määran suhtlemise kooditabeli
    $connect->set_charset("utf-8");

    //valmistame ette sql käsu andmete lisamiseks andmetabelisse
    //INSERT INTO VR22_NEWS (title, content, expire, userid) VALUES(?, ?, ?, ?)
    $statement = $connect->prepare("INSERT INTO VR22_news (title, content, expire, userid) VALUES(?, ?, ?, ?)");
    echo $connect->error;

    //andmetüübid: s - string, i - integer, d - decimal
    $statement->bind_param("sssi", $title, $news,  $expire, $userid);

    if($statement->execute()) {
        $notice = "Uudis on salvestatud!";
    } else {
        $notice = "Uudise salvestamise tekkis viga: " . $statement->error;
    }

    //lõpetan käsu
    $statement->close();
    //sulgen ühenduse
    $connect->close();

    return $notice;
};


function all_news(){
    $newsHtml = null;
    $today = date("Y-m-d");
    $photoDir = "../../~toivo.parnpuu/vr/vr-rinde/upload_orig/";

    $connect = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $connect->set_charset("utf-8");

    //SELECT title, content, created FROM VR22_news
    $statement = $connect->prepare("SELECT VR22_news.title, VR22_news.content, VR22_news.created, VR22_news.expire, vr22_photos.filename, vr22_photos.alttext, CONCAT(vr22_users.firstname, ' ', vr22_users.lastname) AS taisNimi FROM VR22_news, vr22_photos, vr22_users
	ORDER BY vr22_photos.id");
    echo $connect->error;
    $statement->bind_result($title_from_db, $content_from_db, $created_from_db, $expire_from_db, $filename_from_db, $alttext_from_db, $taisNimi_from_db);
    $statement->execute();
    
    //küsib DB-st seni, kuni sealt on midagi võtta
    while($statement->fetch()) {
        $newsHtml .= "<h3>" .$title_from_db ."</h3> \n";
        $newsHtml .= "<p>" .$content_from_db ."</p> \n";
        $newsHtml .= '<img src="'.$photoDir . $filename_from_db.'" alt="'. $alttext_from_db .'" style="width: 200px;height:auto" class="thumbs"/>'; 
        $newsHtml .= "<p>Lisatud: " .$created_from_db ."</p> \n";
        $newsHtml .= "<p>Aegub: " .$expire_from_db ."</p> \n";
        $newsHtml .= "<p>Autor: " .$id_from_db ."</p> \n";
    }

    //lõpetan käsu
    $statement->close();
    //sulgen ühenduse
    $connect->close();

    return $newsHtml;
    
};

function first_five_news() {
    $newsHtml = null;
    $today = date("Y-m-d");
    $photoDir = "../../~toivo.parnpuu/vr/vr-rinde/upload_orig/";

    $connect = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $connect->set_charset("utf-8");

    $statement = $connect->prepare("SELECT VR22_news.title, VR22_news.content, VR22_news.created, VR22_news.expire, VR_news.userid vr22_photos.filename, vr22_photos.alttext, CONCAT(vr22_users.firstname, ' ', vr22_users.lastname) AS taisNimi FROM VR22_news, vr22_photos, vr22_users LIMIT 5");
    echo $connect->error;
    $statement->bind_result($title_from_db, $content_from_db, $created_from_db, $expire_from_db, $id_from_db, $filename_from_db, $alttext_from_db, $taisNimi_from_db);
    $statement->execute();

    while($statement->fetch()) {
        $newsHtml .= "<h3>" .$title_from_db ."</h3> \n";
        $newsHtml .= "<p>" .$content_from_db ."</p> \n";
        $newsHtml .= "<p>HELLO</p>";
        $newsHtml .= '<img src="'.$photoDir . $filename_from_db.'" alt="'. $alttext_from_db .'" style="width: 200px;height:auto" class="thumbs"/>'; 
        $newsHtml .= "<p>Lisatud: " .$created_from_db ."</p> \n";
        $newsHtml .= "<p>Aegub: " .$expire_from_db ."</p> \n";
        $newsHtml .= "<p>User ID: " .$id_from_db ."</p> \n";
    }

    //lõpetan käsu
    $statement->close();
    //sulgen ühenduse
    $connect->close();

    return $newsHtml;
}

function five_newest_news() {
    $newsHtml = null;
    $today = date("Y-m-d");
    $photoDir = "../../~toivo.parnpuu/vr/vr-rinde/upload_orig/";

    $connect = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $connect->set_charset("utf-8");

    $statement = $connect->prepare("SELECT VR22_news.title, VR22_news.content, VR22_news.created, VR22_news.expire, VR_news.userid vr22_photos.filename, vr22_photos.alttext, CONCAT(vr22_users.firstname, ' ', vr22_users.lastname) AS taisNimi FROM VR22_news, vr22_photos, vr22_users ORDER BY VR22_news.created DESC LIMIT 5");
    echo $connect->error;
    $statement->bind_result($title_from_db, $content_from_db, $created_from_db, $expire_from_db, $id_from_db, $filename_from_db, $alttext_from_db, $taisNimi_from_db);
    $statement->execute();
    
    
    while($statement->fetch()) {
        $newsHtml .= "<h3>" .$title_from_db ."</h3> \n";
        $newsHtml .= "<p>" .$content_from_db ."</p> \n";
        $newsHtml .= '<img src="'.$photoDir . $filename_from_db.'" alt="'. $alttext_from_db .'" style="width: 200px;height:auto" class="thumbs"/>'; 
        $newsHtml .= "<p>Lisatud: " .$created_from_db ."</p> \n";
        $newsHtml .= "<p>Aegub: " .$expire_from_db ."</p> \n";
        $newsHtml .= "<p>User ID: " .$id_from_db ."</p> \n";
        
    }

    //lõpetan käsu
    $statement->close();
    //sulgen ühenduse
    $connect->close();

    return $newsHtml;
}

?>