<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>
<div class="container_16">    
<nav>
 <div class="grid_16 menu" >
  <table width="100%" border="0">
    <tr style="border-bottom: red solid 3px">
     <td width="40%" style="text-align:left;">
        <img src="<?php echo base_url(); ?>imagenes/cintillo.png"/>
     </td>
     <td>                        
     <ul>
      <li class="nivel1"><?php echo anchor('acceso/salir', 'Salir', 'title="Salir del Sistema"') ?></li>
      <li class="nivel1"><?php echo anchor($this->uri->uri_string(), 'Reportes', 'title="Reportes"') ?>
       <ul class="nivel2">
          <li><?php echo anchor('en_construccion', 'Reportes Estadísticos', 'title="Reportes Estadísticos"') ?></li>
          <li><?php echo anchor('en_construccion', 'Reportes Generales', 'title="Reportes Generales"') ?></li>         
       </ul>      
      </li>
      <li class="nivel1"><?php echo anchor($this->uri->uri_string(), 'Registrar', 'title="Registro de Correspondencia"') ?>
       <ul class="nivel2">
          <li><?php echo anchor('registrar/correspondencia/entrante', 'Correspondencia Entrante', 'title="Registrar Correspondencia Entrante"') ?></li>
          <li><?php echo anchor('registrar/correspondencia/saliente', 'Correspondencia Saliente', 'title="Registrar Correspondencia Saliente"') ?></li>         
       </ul>            
      </li>      
      <li class="nivel1"><?php echo anchor($this->uri->uri_string(), 'Bandejas', 'title="Bandejas de Correspondencia"') ?>
       <ul class="nivel2">
          <li><?php echo anchor('bandeja/asignadas', 'Asignadas', 'title="Correspondencia Asignada"') ?></li>
          <li><?php echo anchor('bandeja/general', 'General', 'title="Correspondencia General"') ?></li>
          <li><?php echo anchor('bandeja/archivo', 'Archivo', 'title="Correspondencia Archivada"') ?></li> 
       </ul>
      </li>
      <li class="nivel1"><?php echo anchor($this->uri->uri_string(), 'Administración', 'title="Administración"') ?>
        <ul class="nivel2">
           <li><?php echo anchor('acceso/cambiar_clave', 'Cambiar Contraseña', 'title="Cambiar Clave"') ?></li>
          <?php
          
          // PARA ADMINISTRAR USUARIOS, TABLAS DE ORGANISMOS, CLASIFICADORES SE REQUIERE:
          // - QUE EL USUARIO TENGA UN NIVEL IGUAL O SUPERIOR AL DE COORDINADOR (id_nivel=del 1 al 4)
          // - O QUE TENGA ROL DE ADMINISTRADOR
          if ($this->session->userdata('administrador') || intval($this->session->userdata('id_nivel'))<6)
          {
            echo '<li>';
            echo anchor('adm_usuarios', 'Usuarios', 'title="Administrar Usuarios"');
            echo '</li>';
            echo '<li>';
            echo anchor('en_construccion', 'Organismos', 'title="Agregar Organismos"');
            echo '</li>';
            echo '<li>';
            echo anchor('en_construccion', 'Clasificador', 'title="Agregar Clasificador"');            
            echo '</li>'; 
            echo '<li>';
            echo anchor('en_construccion', 'Tipos de Estatus', 'title="Agregar Tipos de Estatus"');            
            echo '</li>'; 
          }
          
          // PARA ENTRAR AL MODULO DE BITÁCROA SE REQUIERE:          
          // - QUE TENGA ROL DE ADMINISTRADOR
          if ($this->session->userdata('administrador'))
          {  
            echo '<li>';
            echo anchor('bitacora', 'Bitácora de Sistema', 'title="Bitácora de Sistema"');
            echo '<li/>'; 
            if (intval($this->session->userdata('id_usuario'))==1)
            {  
              echo '<li>';
              echo anchor('adm_tablas', 'Administración de Tablas', 'title="Administración de Tablas"');
              echo '<li/>'; 
            }
          }
          ?>
        </ul></li>      
    </ul>  
    </td>
  </tr>
  <tr>
      <td>
          <div class="WhereAmI">
            <?php echo $titulo;?>
          </div>          
      </td>
      <td>          
      </td>
  </tr>
  </table>
  </div>
  <div class="clear"></div>
</nav> 
</div>