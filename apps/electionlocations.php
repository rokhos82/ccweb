<?php

$application['out'] = return_page('http://pollinglocation.googleapis.com/?q='.cleanpath($_POST['q']));

?>