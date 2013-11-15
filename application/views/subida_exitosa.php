<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script'); ?>
<div class="container_16 CuerpoPpal">
  <div class="grid_16 AjusteTop"></div>
  <div class="clear"></div>
  <div class="grid_12 alpha omega prefix_2 suffix_2 EntraDatos">  
    <table>
        <thead>
            <tr>
                <th colspan="2">
                 Adjuntar Archivo
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td> 
                    <?php
                    echo img(base_url().'imagenes/scanner.png');
                    ?>                 
                </td>
                <td>
                    <div>
                        <center>Archivo Adjuntado Exitosamente para la Correspondencia:
                           <h3><?php echo $codigo_generado; ?> </h3></center>
                   </div>                    
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                  <div class="BotonIco" onclick="javascript:window.location='<?php echo base_url();?>revisar/correspondencia/<?php echo $id_correspondencia;?>'" title="Cerrar">
                  <?php echo img(base_url().'imagenes/agregado.png');?>                    
                      <a href="#" tabindex="5">&nbsp;Aceptar</a></div>                  
                </td>                
            </tr>
        </tfoot>        
    </table>
    
  </div>
  <div class="grid_16 AjusteBottom"></div>
  <div class="clear"></div>
</div>
<div class="clear"></div>