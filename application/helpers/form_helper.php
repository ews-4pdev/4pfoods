<?php

function text($name, $prefix, $placeholder) {

?>
  <div class="form-group">
      <label for="<?= $name; ?>"></label> <input class="form-control" id="<?= $prefix.$name; ?>" name="<?= $name; ?>" placeholder="<?= $placeholder; ?>" type="text">

      <div class="error-notify"></div>
  </div><!-- End group -->
<?php

}


?>
