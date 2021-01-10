<?php

$data = array_replace([0, 0], explode('.', phpversion()));

echo $data[0], '.', $data[1];
