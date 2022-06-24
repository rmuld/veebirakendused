<?php
    //require_once "fncGeneral.php";
    require_once "../../conf.php";
    require_once "fncNews.php";
    require_once "useSession.php";

    $authorName = "Riina Muld";

    if(isset($_POST['delete'])){
        $id = $_POST['delete_post_id'];  
        $query = "DELETE FROM notes WHERE id=$id"; 
        $result = mysql_query($query);
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
            <hr>
        </header>
        <?php
        require_once "pagenav.php";
        ?>
        
        <main>
           <section>
               <h2>Uudised</h2>
               
               <!-- <?php echo all_news(); ?> -->
               <!-- <?php echo first_five_news(); ?> -->
               <?php echo  five_newest_news(); ?>
               
           </section>

        </main>
        
<?php
    require_once "pagefooter.php";
?>