<?php

function sign_up($firstname, $surname, $gender, $birth_date, $email, $password){
	$notice = 0; //väärtus 0 -> ei ole õnnestunud ja 1 õnnestumine
	$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
	$conn->set_charset("utf8");

    //kontrollin, kas sisestatud emailiga kasutaja on juba olemas
    $stmt = $conn->prepare("SELECT id FROM vr22_users WHERE email = ?");
    echo $conn->error;
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows == 0){
        //lisan uue kasutaja
        $stmt = $conn->prepare("INSERT INTO vr22_users (firstname, lastname, birthdate, gender, email, password) VALUES (?,?,?,?,?,?)");
        echo $conn->error;
        //krüpteerime salasõna
        $options = ["cost"=>12];
        $pwd_hash = password_hash($password, PASSWORD_BCRYPT, $options);
        $stmt->bind_param("sssiss", $firstname, $surname, $birth_date, $gender, $email, $pwd_hash);

        if($stmt->execute()){
            $notice = 1;
        }
        $stmt->close();
        $conn->close();
        echo "Kasutaja registreerimine õnnestus!";
        return $notice;
       
    } else {
        echo "Sellise epostiga kasutaja on juba olemas";

        $stmt->close();
        $conn->close();
        return $notice;
    }

}

function sign_in($email, $password){
    $notice = 0; //väärtus 0 -> ei ole õnnestunud ja 1 õnnestumine
	$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
	$conn->set_charset("utf8");
    $stmt = $conn->prepare("SELECT id, firstname, lastname, password FROM vr22_users WHERE email = ?");
    echo $conn->error;
    $stmt->bind_param("s", $email); 
    //seon loetud andmed muutujatega
    $stmt->bind_result($id_from_db, $firstname_from_db, $lastname_from_db, $password_from_db);
    $stmt->execute();

    if($stmt->fetch()){
        //leiti soovitud meiliaadressiga inimene
        //kontrollime, kas parool on õige
        if(password_verify($password, $password_from_db)){
            //olemegi sees
            $notice = 1;
            $_SESSION["user_id"] = $id_from_db;
            $_SESSION["firstname"] = $firstname_from_db;
            $_SESSION["lastname"] = $lastname_from_db;

            header("Location: home.php");
            $stmt->close();
            $conn->close();
            exit();
        }
    }
    
    $stmt->close();
    $conn->close();
    return $notice;

}