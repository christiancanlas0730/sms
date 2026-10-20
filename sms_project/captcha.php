<?php


$num1 = rand(1,9);
$num2 = rand(1,9);

$_SESSION['captcha_answer'] = $num1 + $num2;

echo "<strong>$num1 + $num2 = ?</strong>";
?>
