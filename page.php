<?php
    session_start();

    require_once "../../conf.php";
    require_once "fncGeneral.php";
    require_once "fncGallery.php";
    require_once "fncUser.php";

    $notice = null;
    $email = null;
    $email_error = null;
    $password_error = null;

    $authorName = "Riina Muld";

    //piltide kataloog
    $photoDir = "hkphotos/";
    $allFiles = readDirContent($photoDir);
    //var_dump($allFiles);
    $allowedPhotoTypes = ["image/jpeg", "image/png"];
    $photoFiles = checkIfPhoto($allFiles, $photoDir, $allowedPhotoTypes);

    //yhe juhusliku foto kuvamine
    $photoCount = count($photoFiles);
    $randomNum = mt_rand(0, $photoCount-1);
    $photoHtml = "\n" .'<img src="' .$photoDir .$photoFiles[$randomNum] .'" alt="Haapsalu kolledž" class="photoframe">' ."\n";

    //kolme juhusliku foto kuvamine
    $randomKeys = array_rand($allFiles, 3);
    $tripleOnePhotoHtml = "\n" .'<img src="' .$photoDir .$allFiles[$randomKeys[0]] .'" alt="Haapsalu kolledž" class="photoframe">' ."\n";
    $tripleTwoPhotoHtml = "\n" .'<img src="' .$photoDir .$allFiles[$randomKeys[1]] .'" alt="Haapsalu kolledž" class="photoframe">' ."\n";
    $tripleThreePhotoHtml = "\n" .'<img src="' .$photoDir .$allFiles[$randomKeys[2]] .'" alt="Haapsalu kolledž" class="photoframe">' ."\n";
  
    
    //aeg ja selle kuvamine
    $fullTimeNow = date('d.m.Y H:i:s');
    $weekdayNow = date('N');
    //echo $weekdayNow - sama mis JS console.log
    $weekadayNamesEt = ['esmasp2ev', 'teisip2ev', 'kolmap2ev', 'neljap2ev', 'reede', 'laup2ev', 'pyhap2ev'];
    $dayCategory = 'lihtsalt p2ev';
    if($weekdayNow <= 5){
        $dayCategory = 'argip2v';
    } else {
        $dayCategory = 'puhkep2v';
    }

    $timeNow = date('H:i:s');
    $partOfDay = '??';
    if($timeNow >= '6:00:00' && $timeNow < '12:00:00') {
        $partOfDay = 'HOMMIK, tere hommikust!';
    } else if($timeNow >= '12:00:00' && $timeNow < '15:00:00') {
        $partOfDay = 'LÕUNA, maitsvat lõunat!';
    } else if($timeNow >= '15:00:00' && $timeNow < '21:00:00'){
        $partOfDay = 'ÕHTU, head õhtut!';
    } else if($timeNow >= '21:00:00' && $timeNow < '6:00:00'){
        $partOfDay = 'ÖÖ, head ööd!';
    }
    
    
    

    $semesterBegin = new DateTime('2022-1-31');
    $semesterEnd = new DateTime('2022-6-30');
    $semesterDuration = $semesterBegin->diff($semesterEnd);
    $semesterDurationDays = $semesterDuration->format('%r%a');
    $fromSemesterBegin = $semesterBegin->diff(new DateTime("now"));
    $fromSemesterBeginDays = $fromSemesterBegin->format('%r%a');

    if($fromSemesterBeginDays > 0){
        if($fromSemesterBeginDays <= $semesterDurationDays){
            $semesterMeter = "\n" .'<p>Semester edeneb: <meter min="0" max="' .$semesterDurationDays .'" value="' . $fromSemesterBeginDays.'"></meter></p>' ."
            \n";
        }else{
            $semesterMeter = "\n <p>Semester on l6ppenud</p> \n";
        }
    }elseif($fromSemesterBeginDays === 0){
        $semesterMeter = "\n <p>Semester algab t2na</p> \n";
    }else{
        $semesterMeter = "\n <p>Semesteri alguseni on j22nud" . abs($fromSemesterBeginDays) ." p2eva</p> \n";
    }

    //kontrollime sisestust sisse logimiseks
    if($_SERVER["REQUEST_METHOD"] === "POST"){
        if(isset($_POST["login_submit"])){
            //email
            if(isset($_POST["email_input"]) and !empty($_POST["email_input"])){
                $email = test_input(filter_var($_POST["email_input"], FILTER_VALIDATE_EMAIL));
                if(empty($email)){
                    $email_error = "Palun sisesta oma e-posti aadress!";
                }
            } else {
                $email_error = "Palun sisesta oma e-posti aadress!";
            }
            //parool
            if(isset($_POST["password_input"]) and !empty($_POST["password_input"])){
                if(strlen($_POST["password_input"]) < 8){
                    $password_error = "Sisestatud salasõna on liiga lühike!";
                }
            } else {
                $password_error = "Palun sisesta salasõna!";
            }

            if(empty($email_error) and empty($password_error)){
                $notice = sign_in($email, $_POST["password_input"]);
            } else {
                $notice = $email_error ." " . $password_error;
            }
        }
    }
    
?>

<!DOCTYPE html>
<html lang="et">
    <head>
        <title>Veebirakendused | <?php echo $authorName;?></title>
        <meta charset="UTF-8">
        <meta author='<?php echo $authorName;?>' name="RIF21 veebirakendused ja nende loomine" content="Harjutusi PHPga">
        <meta name="viewport" content="width=devce-width, inital-scale=1.0">
        <link rel="stylesheet" href="styles/stiilid.css">   
    </head>
    <body>
        <header id="pais">
            <!-- <img id="banner" src="../../~andrus.rinde/media/pic/rif21_banner.png" alt="RIF21" width="auto" height="auto"> -->
            <h1>HKI5099.HK Veebirakendused ja nende loomine</h1>
            <br>
            <nav>
                <h2>Lingid</h2>
                <ul id="menuu">
                    <li><a href="https://www.tlu.ee/haapsalu" target="_blank" >Tallinna Ülikooli Haapsalu kolledžis</a></li>
                </ul>
            </nav>
        </header>

        <hr>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="email" name="email_input" placeholder="email ehk kasutajatunnus" value="<?php echo $email; ?>">
            <input type="password" name="password_input" placeholder="salasõna">
            <input type="submit" name="login_submit" value="Logi sisse">
            <span><?php echo $notice; ?></span>
        </form>
        <p>Loo omale <a href="addUser.php">kasutajakonto</a></p>
        <hr>
        
        <main>
            <section id="dateandtime">
                <h2>Kuupäev ja kellaaeg</h2>
                <p>Lehe avamise hetk: <?php echo $weekadayNamesEt[$weekdayNow -1] .", " .$fullTimeNow .", on " .$dayCategory;?></p>
                <p>Praegu on: <?php echo $partOfDay ?></p>
            </section>
            
            <section id="progressbar">
                <h2>Kevadsemestri progress</h2>
                 <?php echo $semesterMeter;?>
            </section>

            <section>               
                <h2>Avalik galerii</h2>
                <?php echo newest_image(); ?>
            </section>

            <section>
                <h2>Pildigalerii</h2>
                <?php echo $photoHtml;?>                
            </section>
            <section>
                <h2>Galerii triple</h2>
                <?php echo $tripleOnePhotoHtml;?>                
                <?php echo $tripleTwoPhotoHtml;?>                
                <?php echo $tripleThreePhotoHtml;?>
                <br>                
                <a href="#pais">Üles</a>
            </section>

            

        </main>
        
<?php
    require_once "pagefooter.php";
?>