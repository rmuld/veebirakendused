<?php
    //require_once "fncGeneral.php";
    require_once "../../conf.php";
    require_once "fncNews.php";
    require_once "useSession.php";

    $authorName = "Riina Muld";

    //POST uue uudise lisamine
    //$_POST massiiv
    //$_GET massiiv
    //var_dump($_POST);
    //echo $_POST["news-content"];
    $newsHeader = "";
    $newsInputError = null;
    $newsContent = "";
    $newsExpire = "";
    $notice = null;

    if(isset ($_POST["news-submit"])){
        //kontrollime peakirja
        if(isset ($_POST["news-header"]) and !empty($_POST["news-header"])){
            $newsHeader = $_POST["news-header"];
        } else {
            $newsInputError = "Uudise pealkiri on puudu! ";
        }

        //kontrollime sisu
        if(isset ($_POST["news-content"]) and !empty($_POST["news-content"])){
            $newsContent = $_POST["news-content"];
        } else {
            $newsInputError = "Uudise sisu on puudu! ";
        }

        $newsExpire = $_POST["news-expire"];

        $notice = $newsInputError;

        if(empty($newsInputError)) {
            $notice = save_news($newsHeader, $newsContent, $newsExpire);
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
            <h1>HKI5099.HK Veebirakendused ja nende loomine</h1>
            <hr>
        </header>
        <?php
        require_once "pagenav.php";
        ?>
        
        <main>
           <section>
               <h2>Lisa uudis</h2>
               <form method="POST">
                   <label for="news-header">Uudise pealkiri</label>
                   <input type="text" id="news-header" name="news-header" placeholder="Kirjuta pealkiri..." pattern="[a-zA-Z0-9]*" minlength="4" maxlength="60" required autofocus>
                   <br>
                   <label for="news-content">Uudise tekst</label><br>
                   <textarea id="news-content" name="news-content" cols="60" rows="5" placeholder="Kirjuta sisu..." pattern="[a-zA-Z0-9]*" minlength="4" maxlength="400" required></textarea>
                   <br>
                   <label for="news-expire">Uudise aegmistähtaeg</label>
                    <input type="date" id="news-expire" name="news-expire" required>
                    <br>
                   <input type="submit" id="news-submit" name="news-submit" value="Salvesta uudis">
               </form>
               <?php echo "<p>" .$notice ."</p> \n" ?>
           </section>

        </main>
        
<?php
    require_once "pagefooter.php";
?>