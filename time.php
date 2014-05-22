<form><input type="text" name="date"><input type="submit"></form>
<br />
<br />
<?php 

$date = preg_replace("[^A-Za-z0-9:. ]"," ",$_GET['date']);
if(strlen($date)==0) $date = "Now";
print $date." is ".strtotime($date);

?>