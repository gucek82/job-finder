<?php

use Framework\Session;
?>

<?php
$successMessage = Session::getFlashMessage('succces_message');
if($successMessage !== null) {?>
<div class="message bg-green-100 p-3 my-3">
    <?= $successMessage; ?>
</div>
<?php }

$errorMessage = Session::getFlashMessage('error_message');
if($errorMessage !== null) {?>
<div class="message bg-red-100 p-3 my-3">
    <?= $errorMessage; ?>
</div>
<?php }
?>