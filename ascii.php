<?php

# https://gist.github.com/donatj/1353237

function image2ascii(){
  $arr = [];
  $path = 'img/';
  $part = [
    'head','torso',
    'arm','hand','arm','hand',
    'leg','foot','leg','foot'
  ];

  foreach($part as $p) {
    $folder = $path . $p . '/*.jpg';
    $possible_parts = glob($folder, GLOB_BRACE);
    $part = array_rand( $possible_parts ,1);
    $url = $possible_parts[$part];

    //*
    $wbias = $_POST['width_bias'];
    $hbias = $_POST['height_bias'];
    $wchar = $_POST['char_width'];
    $hchar = $_POST['char_height'];
    $wscreen = $_POST['screen_width'];
    $hscreen = $_POST['screen_height'];

    $img = imagecreatefromstring(file_get_contents($url));
    list($wimage, $himage) = getimagesize($url);

    if($wimage/$himage >= $wscreen/$hscreen){
      //set image width to screen width and proportion height
      $wscale = $wimage/$wbias;
      $hscale = $wscale * ($hchar/$wchar);
    } else {
      //set image height to screen height and proportion width
      $hscale = $himage/$hbias;
      $wscale = $hscale * ($wchar/$hchar);
    }

    $chars = array(
      '.', '\'', ':', '!', '|',
      '}', 'I',  'S', '0', '#', '@',
    );
    $chars = array_reverse($chars);
    $c_count = count($chars);

    $line = '';
    for($y = 0; $y <= $himage-1; $y += $hscale) { //rows (incremented by a factor of scaled)
      for($x = 0; $x <= $wimage-1; $x += $wscale) { //cols
        $rgb = imagecolorat($img, $x, $y);
        $r = (($rgb >> 16) & 0xFF);
        $g = (($rgb >> 8) & 0xFF);
        $b = ($rgb & 0xFF);
        $sat = ($r + $g + $b) / (255 * 3); //gets "grayscale" value as number between 0,1

        $line .= $chars[ (int)( $sat * ($c_count - 1) ) ]; //gets integer value of grayscale within array
      }
      $line .= '<br>';
    }
    array_push($arr, $line);
  }
  return $arr;
};

function getWord($difficulty){
  $files = [
    'hard' => 'words3-5',
    'medium' => 'words6-10',
    'easy' => 'words11+'
  ];
  if ($difficulty == 'random'){
    $file = $files[array_rand($files, 1)];
  } else {
    $file = $files[$difficulty];
  }

  $max = filesize($file);
  $seek = rand(0,$max);
  for ($i = 0; $i < 2; $i++){
    $fp = fopen($file,'r');
    fseek($fp, $seek);
    $data = fgets($fp);
    if ($data == PHP_EOL || feof($fp)) {
      $i = 0;
      $seek = 0;
    } else {
      $seek += strlen($data);
    }
    fclose($fp);
  }
  return trim($data);
}
//
$output = array(
  "ascii" => image2ascii(),
  "word" => getWord($_POST['difficulty']),
);

print json_encode($output);
