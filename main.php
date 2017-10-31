<?php
include("Resize.php");
$r = new Resize();

$r->image_resize("test.png", 63, 100);
$r->conversion_to_two_colors("test.png", 63, 100, "test.png");
$r->resize_character("test.png", 63, 100, "test.png");
$r->image_resize("test.png", 63, 100);
$r->conversion_to_two_colors("test.png", 63, 100, "test.png");

$arr_diff = array();
$min_diff = 9999;
$let_number = 0;

for($i = 1; $i < 33; $i ++){
    //$arr_diff[$i - 1] = $r->compare_images("test.png", "cut_images/".$i.".png", 63, 100);
    $temp_diff = $r->compare_images("test.png", "cut_images/".$i.".png", 63, 100);
    $arr_diff[$i - 1] = $temp_diff;
    if($temp_diff < $min_diff){
        $min_diff = $temp_diff;
        $let_number = $i;
    }
}

echo "<img src='test.png'><img src='cut_images/".$let_number.".png'><h1>".$min_diff."</h1><br>";
print_r($arr_diff);
/*$min_diff = 999999;
$let_number = 0;
for($i = 1; $i < 32; $i ++){
    //$arr_diff[$i - 1] = $r->compare_images("test.png", "cut_images/".$i.".png", 63, 100);
    $temp_diff = $r->compare_images("test.png", "cut_images_bold/".$i.".png", 63, 100);
    $arr_diff[$i - 1] = $temp_diff;
    if($temp_diff < $min_diff){
        $min_diff = $temp_diff;
        $let_number = $i;
    }
}
echo "<img src='test.png'><img src='cut_images_bold/".$let_number.".png'><h1>".$min_diff."</h1><br>";
print_r($arr_diff);*/

/*$r->image_resize("cut_images_bold/".$i.".png", 63, 100);
    $r->conversion_to_two_colors("cut_images_bold/".$i.".png", 63, 100, "cut_images_bold/".$i.".png");
    $r->resize_character("test.png", 63, 100, "test.png");
    $r->image_resize("test.png", 63, 100);
    $r->conversion_to_two_colors("test.png", 63, 100, "test.png");*/