<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script'); ?>
<div class="container_16 CuerpoPpal">
  <div class="grid_16 AjusteTop"></div>
  <div class="clear"></div>
  <div class="grid_12 alpha omega prefix_2 suffix_2 EntraDatos">  
    <table>
        <thead>
            <tr>
                <th colspan="2">
                    <?php
                    echo $titulo;
                    ?> 
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td> 
                    <?php
                    echo $imagen;
                    ?>                 
                </td>
                <td>
                    <div><strong>Nota:</strong> De manera opcional, puede adjuntar la 
                        copia digitalizada de la correspondencia registrada en cualquiera
                        de los formatos:<br/><center><strong>PDF, JPG, JPEG, PNG, TIF, BMP o GIF</strong></center>
                   </div>
                     <?php
                      echo form_label('Seleccione el Archivo: ');
                      echo form_open_multipart(base_url().'registrar/subir_archivo/'.$id_correspondencia);
                     ?>
                    <div>
                     <input type="file" name="userfile" size="50" />
                    </div>
                    <?php                    
                      echo form_close();  
                    ?>
                    <p><?php echo $error;?></p>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">
                  <div class="BotonIco" onclick="javascript:window.close();" title="Cancelar">
                  <?php echo img(base_url().'imagenes/cancel.png');?>                    
                      <a href="#" tabindex="5">&nbsp;Cancelar</a></div>
                  <?php echo nbs(5);?>
                  <div class="BotonIco" onclick="javascript:$('form').submit()" title="Adjuntar Archivo">
                  <?php echo img(base_url().'imagenes/guardar32.png');?>
                  <a href="#" tabindex="4">&nbsp;Adjuntar</a></div>          
                </td>                
            </tr>
        </tfoot>        
    </table>
    
  </div>
  <div class="grid_16 AjusteBottom"></div>
  <div class="clear"></div>
</div>
<div class="clear"></div>