<?php
class Resize
{
    public function image_resize($image, $w_o = false, $h_o = false) {
        echo $image;
        if (($w_o < 0) || ($h_o < 0)) {
            echo "Некорректные входные параметры";
            return false;
        }
        list($w_i, $h_i, $type) = getimagesize($image); // Получаем размеры и тип изображения (число)
        $types = array("", "gif", "jpeg", "png"); // Массив с типами изображений
        $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа
        if ($ext) {
            $func = 'imagecreatefrom'.$ext; // Получаем название функции, соответствующую типу, для создания изображения
            $img_i = $func($image); // Создаём дескриптор для работы с исходным изображением
        } else {
            echo 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый
            return false;
        }
        /* Если указать только 1 параметр, то второй подстроится пропорционально */
        if (!$h_o) $h_o = $w_o / ($w_i / $h_i);
        if (!$w_o) $w_o = $h_o / ($h_i / $w_i);
        $img_o = imagecreatetruecolor($w_o, $h_o); // Создаём дескриптор для выходного изображения
        imagecopyresampled($img_o, $img_i, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i); // Переносим изображение из исходного в выходное, масштабируя его
        $func = 'image'.$ext; // Получаем функция для сохранения результата
        return $func($img_o, $image); // Сохраняем изображение в тот же файл, что и исходное, возвращая результат этой операции
    }

    public function conversion_to_two_colors($image, $img_width = 10, $img_height = 10, $get_image_name){
        $img_height = (int)$img_height;
        $img_width = (int)$img_width;
        $im = imagecreatefrompng($image);

        for($i = 0; $i < $img_height; $i ++){
            for($j = 0; $j < $img_width; $j ++){

                $rgb = imagecolorat($im, $j, $i);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $black = imagecolorallocate($im, 0, 0, 0);
                $white = imagecolorallocate($im, 255, 255, 255);

                if($r > 128 OR $g > 128 OR $b > 128){
                    imagesetpixel($im, $j, $i, $white);
                }else{
                    imagesetpixel($im, $j, $i, $black);
                }
            }
        }
        imagepng($im, $get_image_name);
    }

    public function create_matrix($image, $width, $height, $title){
        $img_height = (int)$height;
        $img_width = (int)$width;
        $im = imagecreatefrompng($image);

        $fp = fopen($title.".ch", "w");

        for($i = 0; $i < $img_height; $i ++) {
            for ($j = 0; $j < $img_width; $j++) {
                $rgb = imagecolorat($im, $j, $i);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                if($r > 128 OR $g > 128 OR $b > 128){
                    fwrite($fp, 0);
                }else{
                    fwrite($fp, 'X');
                }

            }
            fwrite($fp, "\n");
        }
        fclose($fp);

    }

    public function compare_images($img, $pattern, $width, $height){
        $img_height = (int)$height;
        $img_width = (int)$width;
        $im = imagecreatefrompng($img);
        $pat = imagecreatefrompng($pattern);
        $diff = 0;

        for($i = 0; $i < $img_height; $i ++) {
            for ($j = 0; $j < $img_width; $j++) {
                $img_rgb = imagecolorat($im, $j, $i);
                $img_r = ($img_rgb >> 16) & 0xFF;
                $img_g = ($img_rgb >> 8) & 0xFF;
                $img_b = $img_rgb & 0xFF;
                $img_summ = $img_r + $img_g + $img_b;

                $pat_rgb = imagecolorat($pat, $j, $i);
                $pat_r = ($pat_rgb >> 16) & 0xFF;
                $pat_g = ($pat_rgb >> 8) & 0xFF;
                $pat_b = $pat_rgb & 0xFF;
                $pat_summ = $pat_r + $pat_g + $pat_b;
                
                if($pat_summ != $img_summ){
                    $diff ++;
                }
            }
        }
        return $diff;
    }

    public function resize_character($img, $width, $height, $result_image_name){//изображение должно быть бинаризировано
        $img_height = (int)$height;
        $img_width = (int)$width;
        $im = imagecreatefrompng($img);
        $new_im = imagecreate($width, $height);

        //зададим фон
        $background = imagecolorallocate($new_im, 255, 255, 255);

        //другие цвета
        $black = imagecolorallocate($new_im, 0, 0, 0);
        $white = imagecolorallocate($new_im, 255, 255, 255);

        //находим координаты прямоугольника с буквой
        $w_begin = 999999; $h_begin = 999999; $w_end = 0; $h_end = 999999;

        for($i = 0; $i < $img_height; $i ++) {
            for ($j = 0; $j < $img_width; $j++) {
                $img_rgb = imagecolorat($im, $j, $i);
                $img_r = ($img_rgb >> 16) & 0xFF;
                $img_g = ($img_rgb >> 8) & 0xFF;
                $img_b = $img_rgb & 0xFF;
                $color_summ = $img_r + $img_g + $img_b;

                if($color_summ == 0 && $h_begin > $i){//верхняя граница
                    $h_begin = $i;
                }

                if($color_summ == 0 && $h_end >= $h_begin){//нижняя граница
                    $h_end = $i;
                }

                if($color_summ == 0 && $w_begin > $j){//левая граница
                    $w_begin = $j;
                }

                if($color_summ == 0 && $w_end < $j){//правая граница
                    $w_end = $j;
                }
            }
        }
        $w_begin -= 1; $h_begin -= 1; $w_end += 4; $h_end += 1;
        echo "imagecopyresized ( $new_im , $im ,5 ,5 ,$w_begin ,$h_begin ,55 , 77 ,$w_end - $w_begin ,$h_end - $h_begin );";
        imagecopyresampled( $new_im , $im ,7 ,5 ,$w_begin ,$h_begin ,50 , 77 ,$w_end - $w_begin ,$h_end - $h_begin );
        imagepng($new_im, $result_image_name);
    }

}