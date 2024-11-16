<?php
$data = [];
for ($i = 0; $i < 30; $i++) {
    $name = $i + 1;
    $data[$i] = [chr($i+65), rand(0, 128), rand(0, 12), rand(-1, 5)];
}
include 'content.php';
?>