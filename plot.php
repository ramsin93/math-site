<?php
header('Content-type: image/png');

session_start();
if (session_id() === 'dbefb3bb6a20767d5f1d') {
	if (isset($_SESSION['error'])) {
		session_unset(); 
		session_destroy(); 
		exit(1);
	}
	
	define('EQUATION', $_SESSION['equation']);
	define('WIDTH', $_SESSION['width']);
	define('HEIGHT', $_SESSION['height']);
	define('SCALE', $_SESSION['scale']);
	define('INCREMENT', 20);
	
	// Draws the background with the coordinate system
	function setup() {
		$img = @imagecreatetruecolor(WIDTH, HEIGHT) or die('Cannot Initialize new image stream');
		$bkgnd = imagecolorallocate($img, 255, 255, 255);
		imagefilledrectangle($img, 0, 0 , WIDTH, HEIGHT, $bkgnd);
		
		$marker_color = imagecolorallocate($img, 0, 0, 0);
		imageline($img, WIDTH/2, 0, WIDTH/2, HEIGHT, $marker_color);
		imageline($img, 0, HEIGHT/2, WIDTH, HEIGHT/2, $marker_color);
		
		draw_markers($img, $marker_color);
		
		return $img;
	}
	
	// Draws the axis markers
	function draw_markers($img, $marker_color) {
		$marker_length = 10/SCALE;
		for ($i = 0; $i < WIDTH/2; $i+=INCREMENT/SCALE) {
			$x = $i + WIDTH/2;
			$y = HEIGHT/2;
			
			imageline($img, $x, $y - $marker_length, $x, $y + $marker_length, $marker_color);
			imageline($img, $x-2*$i, $y - $marker_length, $x-2*$i, $y + $marker_length, $marker_color);
		}
		
		for ($i = 0; $i < HEIGHT/2; $i+=INCREMENT/SCALE) {
			$x = WIDTH/2;
			$y = $i + HEIGHT/2;
			
			imageline($img, $x - $marker_length, $y, $x + $marker_length, $y, $marker_color);
			imageline($img, $x - $marker_length, $y-2*$i, $x + $marker_length, $y-2*$i, $marker_color);
		}
	}
	
	// Plots the function on $img
	function plot($img, $equation) {
		$color = imagecolorallocate($img, 0, 0, 0);
		
		for ($i = 0; $i < WIDTH/2; $i+=0.01) {
			$x0 = $i/(INCREMENT/SCALE);
			@eval('$y = ' . str_replace('X', '$x0', $equation) . ';');
			if ($y !== false) {
				$x0 = $i + WIDTH/2;
				$y = HEIGHT/2 - $y*(INCREMENT/SCALE);
				imagesetpixel($img, $x0, $y, $color);
			}
			
			$x1 = -$i/(INCREMENT/SCALE);
			@eval('$y = ' . str_replace('X', '$x1', $equation) . ';');
			if ($y !== false) {
				$x1 = -$i + WIDTH/2;
				$y = HEIGHT/2 - $y*(INCREMENT/SCALE);
				imagesetpixel($img, $x1, $y, $color);
			}
		}
	}
	
	$img = setup();
	plot($img, EQUATION);
	imagepng($img);
	imagedestroy($img);
}

session_unset(); 
session_destroy(); 

?>