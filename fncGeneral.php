<?php
    function readDirContent($dir) {
        return $readAllFiles = array_slice(scandir($dir), 2); 
    }

    function checkIfPhoto($allFiles, $photoDir, $allowedTypes) {
        $photoFiles = [];
        foreach ($allFiles as $file) {
            $fileInfo = getimagesize($photoDir .$file);
            if(isset($fileInfo["mime"])){
                if(in_array($fileInfo["mime"], $allowedTypes)) {
                    array_push($photoFiles, $file);
                }
            }
        }
        return $photoFiles;
    }

    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data  = strip_tags($data);
        $data = htmlspecialchars($data);
        return $data;
    }