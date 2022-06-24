<?php
    require_once "useSession.php";

    $authorName = "Riina Muld";
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
        </header>
        
        <?php
        require_once "pagenav.php";
        ?>
        
        <main>
           <section>
               <h2>Avaleht</h2>
               
           </section>

        </main>
        
<?php
    require_once "pagefooter.php";
?>