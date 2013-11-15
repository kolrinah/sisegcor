<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16 CuerpoPpal">
  <div class="grid_16 AjusteTop"></div>
  <div class="clear"></div>
  <div class="grid_16">
    <h3>¡Su Archivo Ha Sido Adjuntado Correctamente!</h3>
    <ul>
        <?php foreach ($upload_data as $item => $value):?>
        <li><?php echo $item;?>: <?php echo $value;?></li>
        <?php endforeach; ?>
    </ul>
  </div>
  <div class="clear"></div>
  <div class="grid_16 AjusteBottom"></div>
  <div class="clear"></div>
</div>
<div class="clear"></div>