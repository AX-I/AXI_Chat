<?php
$success = false;

$infile = $_FILES["imgUpload"];
$inname = basename($infile["name"]);
if ($inname == "") $inname = time() . ".img";

$target_file = "UsrImgs/" . $inname;
$outname = $inname;

$fc = 0;
while (file_exists($target_file)) {
    $outname = $fc . $inname;
    $fc ++;
    $target_file = "UsrImgs/" . $outname;
}

$inprop = getimagesize($infile["tmp_name"]);

if (in_array($inprop["mime"], ["image/jpeg", "image/png", "image/gif"])) {
    
    if (max($inprop[0], $inprop[1]) > 5000) exit("Image is too large.<br>If you are using a phone try taking a screenshot of the image.");

    if (move_uploaded_file($infile["tmp_name"], $target_file)) {
    echo "The file [$outname] has been uploaded.<br><a href=\"Image.php\">Upload another image</a><br>";
    $success = true;
    
    } else {
        echo "Error uploading the file $outname.";
    }
}
else {echo "Invalid file<br>File must be of type jp(e)g, png, or gif.";}

if (!$success) exit;

?>
