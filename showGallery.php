<?php
    require_once "../../conf.php";
    require_once "fncGallery.php";
    require_once "useSession.php";

    $privacy = 2;
    
    $page = 1;
    $limit = 10;
    $photo_count = count_photos($privacy);
    //kontrollime, mis lehel oleme ja kas selline leht on võimalik
    if(!isset($_GET["page"]) or $_GET["page"] < 1){
        $page = 1;
    } elseif(round($_GET["page"] - 1) * $limit >= $photo_count){
        $page = ceil($photo_count / $limit);
    } else {
        $page = $_GET["page"];
    }

?>

<!DOCTYPE html>
<html lang="et">
    <head>
        <title>Veebirakendused | <?php echo $_SESSION["firstname"] ." " .$_SESSION["lastname"];?></title>
        <meta charset="UTF-8">
        <meta author='<?php echo $_SESSION["firstname"];?>' name="RIF21 veebirakendused ja nende loomine" content="Harjutusi PHPga">
        <meta name="viewport" content="width=devce-width, inital-scale=1.0">
        <link rel="stylesheet" href="styles/stiilid.css">
        <link rel="stylesheet" href="styles/modal.css">
        <script src="JS/modal.js" defer></script>   
    </head>
    <body>
        <header id="pais">
            <!-- <img id="banner" src="../../~andrus.rinde/media/pic/rif21_banner.png" alt="RIF21" width="auto" height="auto"> -->
            <h1><?php echo $_SESSION["firstname"] ." " .$_SESSION["lastname"]; ?> arendab veebi</h1>
            <br>
            
        </header>
        <?php
        require_once "pagenav.php";
        ?>
        
        <main>
           <section>
               <!-- modaalaken fotode näitamiseks -->
                <dialog id="modal" class="modalarea">
                    <span id="modalclose" class="modalclose">&times;</span>
                    <div class="modalhorizontal">
                        <div class="modalvertical">
                            <p id="modalcaption"></p>
                            <img id="modalimage" src="empty.png" alt="Galeriipilt">
                            <br>
                            <input id="rate1" name="rating" type="radio" value="1"><label for="rate1">1</label>
                            <input id="rate2" name="rating" type="radio" value="2"><label for="rate2">2</label>
                            <input id="rate3" name="rating" type="radio" value="3"><label for="rate3">3</label>
                            <input id="rate4" name="rating" type="radio" value="4"><label for="rate4">4</label>
                            <input id="rate5" name="rating" type="radio" value="5"><label for="rate5">5</label>
                            <button id="storeRating" type="button">Salvesta hinne</button>
                            <br>
                            <p id="avgrating"></p>
                        </div>
                    </div>
                </dialog>


               <h2>Fotode kuvamine</h2>
               <!-- <div class="thumbgallery">
                   <?php echo all_images(); ?>                     
                </div> -->
                <p>
			<?php
				if($page > 1){
					echo '<span><a href="?page=' .($page - 1) .'">Eelmine leht</a></span>';
				} else {
					echo "<span>Eelmine leht</span>";
				}
				echo " | ";
				if($page * $limit < $photo_count){
					echo '<span><a href="?page=' .($page + 1) .'">Järgmine leht</a></span>';
				} else {
					echo "<span>Järgmine leht</span>";
				}
			?>
		</p>
                <div class="gallery">
			
                    <?php echo read_public_photo_thumbs($privacy, $page, $limit); ?>
                </div>               
           </section>

        </main>
        
<?php
    require_once "pagefooter.php";
?>