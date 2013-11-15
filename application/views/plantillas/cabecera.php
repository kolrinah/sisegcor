<!--------------------------------------------------------------------->
<!--                                                                 -->
<!-- SISTEMA DE SEGUIMIENTO Y CONTROL DE CORRESPONDENCIA             -->
<!-- DESARROLLADO POR: ING.REIZA GARCÍA                              -->
<!--                   ING.HÉCTOR MARTÍNEZ                           -->
<!-- PARA EL MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES -->
<!-- FECHA: ENERO DE 2013                                            -->
<!-- TELEFONOS DE CONTACTO PARA SOPORTE: 0416-9052533 / 0212-5153033 -->
<!--                                                                 -->
<!--------------------------------------------------------------------->
<?php if( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela
      session_start();      
      if (!isset($_SESSION['aprobado']) && $this->uri->uri_string()!='acceso')
      {
        $this->session->sess_destroy(); 
	session_destroy();
        redirect('acceso','refresh');	
	exit();
      }
   
      if ($this->uri->uri_string()=='acceso' && $this->session->userdata('aprobado'))
      {redirect('principal','refresh');
      exit();
      }
      ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $titulo?></title>
	<link rel="icon" type="image/png" href="<?php echo base_url();?>imagenes/bandera.ico" />
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url();?>imagenes/bandera.ico" />
<!--Cargamos el Framework CSS 960gs (Grid System)-->
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url();?>css/reset.css" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url();?>css/text.css" />
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url();?>css/960.css" />
<!--Cargamos el Framework CSS jQueryUI -->        
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url();?>css/jquery-ui.css" />        
<!--Cargamos CSS propio-->
        <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url();?>css/estilos.css">
<!--Incluimos la liberia jQuery-->
	<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>js/jquery.js"></script>
<!--Incluimos el API jQueryUI-->
	<script type="text/javascript" charset="utf-8" src="<?php echo base_url();?>js/jquery-ui.js"></script>
<?php if (isset($script)){echo $script;}?>
    </head>
    <body>