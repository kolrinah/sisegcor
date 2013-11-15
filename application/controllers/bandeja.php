<?php if (!defined('BASEPATH')) exit('Sin Acceso Directo al Script');
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *  SISTEMA DE SEGUIMIENTO Y CONTROL DE CORRESPONDENCIA              *
 *  DESARROLLADO POR: ING.REIZA GARCÍA                               *
 *                    ING.HÉCTOR MARTÍNEZ                            *
 *  PARA:  MINISTERIO DEL PODER POPULAR PARA RELACIONES EXTERIORES   *
 *  FECHA: ENERO DE 2013                                             *
 *  FRAMEWORK PHP UTILIZADO: CodeIgniter Version 2.1.3               *
 *                           http://ellislab.com/codeigniter         *
 *  TELEFONOS PARA SOPORTE: 0416-9052533 / 0212-5153033              *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
class Bandeja extends CI_Controller {

    function __construct() 
    {
      parent::__construct();      
      $this->load->helper('form');
      $this->load->model('Correspondencia');     
      $this->load->model('Crud');
      $this->load->model('Estructura');
    }
    
    function index()
    { 
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}  
   
    }
    
    function asignadas()
    {
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}  
            
      $data=$this->_campos_fijos();
      
      $data['bandeja']= array(
                        'type'=>'hidden',
                        'name' => 'bandeja',
                        'id' => 'bandeja',                        
                        'value' => '0');  // 0 -> ASIGNADAS, 1 -> GENERAL, 2 -> ARCHIVO 
      
      $data['titulo']='Correspondencias Asignadas';      
      $data['tabla']=$this->_cargar_registros(0);
      
      $this->load->view('plantillas/plantilla_general',$data); 
    }
    
    function general()
    {
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}  
            
      $data=$this->_campos_fijos();
      
      $data['bandeja']= array(
                        'type'=>'hidden',
                        'name' => 'bandeja',
                        'id' => 'bandeja',                        
                        'value' => '1');  // 0 -> ASIGNADAS, 1 -> GENERAL, 2 -> ARCHIVO 
      
      $data['titulo']='Correspondencia General';
      $data['tabla']=$this->_cargar_registros(1);
      
      $this->load->view('plantillas/plantilla_general',$data);
    }
    
    function archivo()
    {
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}  
         
      $data=$this->_campos_fijos();
      
      $data['bandeja']= array(
                        'type'=>'hidden',
                        'name' => 'bandeja',
                        'id' => 'bandeja',                        
                        'value' => '2');  // 0 -> ASIGNADAS, 1 -> GENERAL, 2 -> ARCHIVO 
      
      $data['titulo']='Correspondencia Archivo';
      $data['tabla']=$this->_cargar_registros(2);
      
      $this->load->view('plantillas/plantilla_general',$data);
    }        

    function repetidas($id_organismo, $nro_comunicado, $fecha_emision, $entrante)
    {
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}  
                   
      $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
      $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    
      $data['script'].='<!--Incluimos Funciones JS de uso común-->'."\n";
      $data['script'].="\t".'<script type="text/javascript" charset="utf-8" src="'.base_url().'js/comunes.js"></script>'."\n";     
      
      $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
      $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
      $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
      $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/bandeja.js"></script>'."\n";
              
      // BOTON DE SALIR
      $salir=array(
               'src' => base_url().'imagenes/exit.png',
               'alt' => 'Salir',
             'title' => 'Cerrar',
             'style' => 'cursor:pointer; max-width:32px; max-height:32px',
           'onclick' => 'javascript:window.close();'
                   );
            
      $data['botonsalir']=img($salir);
      $data['contenido']='repetidas';
      
      $data['bandeja']= array(
                        'type'=>'hidden',
                        'name' => 'bandeja',
                        'id' => 'bandeja',                        
                        'value' => '0');  // 0 -> ASIGNADAS, 1 -> GENERAL, 2 -> ARCHIVO 
      
      $data['titulo']='Correspondencias Similares';      
      $data['tabla']=$this->_cargar_repetidas($id_organismo, $nro_comunicado, $fecha_emision, $entrante);
      
      $this->load->view('plantillas/plantilla_ingreso',$data); 
    }
        
    function _campos_fijos()
    {
      $entrante=$this->session->userdata('entrante');
      $fecha_ini=$this->session->userdata('fecha_inicial');
      $fecha_fin=$this->session->userdata('fecha_final');
      
      $data=array();
      
      $data['fecha_ini']= array(                        
                        'id' => 'FechaIni',
                        'class'=>'Fechas FondoBlanco',
                        'value' => $fecha_ini,
                        'maxlength' => '10',
                        'title'=>'Fecha Inicial',
                        'readonly'=>'readonly',
                        'size' => 10);    
      
      $data['fecha_fin']= array(                        
                        'id' => 'FechaFin',
                        'class'=>'Fechas FondoBlanco',
                        'value' => $fecha_fin,
                        'maxlength' => '10',
                        'title'=>'Fecha Final',
                        'readonly'=>'readonly',                        
                        'size' => 10);
      
      // Caja Combo para Tipo de Correspondencia
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->listar_registros('cor_tipos');
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array('- Todos los Tipos -');
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_tipo);
           array_push($b,$fila->tipo);
         }
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->_construye_opciones($opciones);
      }
      
      $data['tipo']= '<select id="id_tipo" onchange="javascript:Actualiza();" ';
      $data['tipo'].=' class="Campos FondoBlanco" title="Tipo de Correspondencia">';
      $data['tipo'].=$opciones;
      $data['tipo'].='</select>';
      
      // Caja Combo para Categorías de Correspondencia
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->listar_registros('cor_clasificaciones');
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array('- Todas las Categorías -');
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_clasificacion);
           array_push($b,$fila->clasificacion);
         }         
         $opciones=array_combine($a,$b);
         asort($opciones);
         $opciones=$this->_construye_opciones($opciones);
      }
      
      $data['clasificacion']= '<select id="id_clasificacion" onchange="javascript:Actualiza();" ';
      $data['clasificacion'].=' class="Campos FondoBlanco" title="Clasificación de Correspondencia">';
      $data['clasificacion'].=$opciones;
      $data['clasificacion'].='</select>';

      // BOTON Entrante / Saliente
      
      $dat=array(
             'img'   => base_url()."imagenes/".(($entrante)?"entrante.png":"saliente.png"),
             'title' => 'Ver Correspondencia '.(($entrante)?"Saliente":"Entrante"),
             'valor' => ($entrante)?"t":"f",
             'span'  => "Correspondencia ".(($entrante)?"Entrante":"Saliente"));
      
      $boton='<div class="ToggleBoton" onclick="javascript:ToggleBotonEntrante();Actualiza();">';
      $boton.='<img id="imgEntrante" src="'.$dat['img'].'" title="'.$dat['title'].'"/>';
      $boton.='</div>';
      $boton.='<span id="spanEntrante">&nbsp;'.$dat['span'].'</span>';
      $boton.='<input type="hidden" id="hideEntrante" value="'.$dat['valor'].'" />';
      // FIN BOTON ENTRANTE / SALIENTE
      
      $data['boton_entrante']=$boton;
      
      $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
      $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
    
      $data['script'].='<!--Incluimos Funciones JS de uso común-->'."\n";
      $data['script'].="\t".'<script type="text/javascript" charset="utf-8" src="'.base_url().'js/comunes.js"></script>'."\n";     
      
      $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
      $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
      $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
      $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/bandeja.js"></script>'."\n";
              
      $data['contenido']='bandeja';
      
      return $data;
    }
        
    function _cargar_registros($bandeja, $id_tipo=0, $id_clasificacion=0)
    {      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
      
      $entrante=$this->session->userdata('entrante');
      $fecha_ini=$this->session->userdata('fecha_inicial');
      $fecha_fin=$this->session->userdata('fecha_final');
      $id_usuario=$this->session->userdata('id_usuario');
      
      $id_nivel=$this->session->userdata('id_nivel');
      $admin=$this->session->userdata('administrador');
      
      // VERIFICAMOS SI EL USUARIO ES DISTRIBUIDOR, JEFE O ADMIN
      $id_estructura=($id_nivel<=5 || $id_nivel==7 || $admin)?$this->session->userdata('id_estructura'):'0';
      $estructura='0';
      switch ($bandeja)
      {
         case 0:// CORRESPONDENCIA ASIGNADA
                if ($id_nivel==7)$id_estructura='0';
                $registros= $this->Correspondencia->correspondencia_asignada($entrante,$fecha_ini, 
                             $fecha_fin, $id_usuario, $id_estructura, $id_tipo, $id_clasificacion );
                break;
            
         case 1: // CORRESPONDENCIA GENERAL                 
                if ($id_estructura!='0')
                {
                   $estructura=$this->Estructura->obtener_estructuras_inferiores($id_estructura);
                   $e=$estructura->result();
                   $estructura='';
                   foreach ($e as $f)  
                   {
                     $estructura.=" or id_estructura_owner='".$f->id_estructura."'";
                   }
                   $estructura= substr($estructura, 3);
                }                
                $registros= $this->Correspondencia->correspondencia_general($entrante,$fecha_ini, 
                             $fecha_fin, $id_usuario, $estructura, $id_tipo, $id_clasificacion );
                break;
                
        default: // CORRESPONDENCIA ARCHIVO
                if ($id_estructura!='0')
                {
                   $estructura=$this->Estructura->obtener_estructuras_inferiores($id_estructura);
                   $e=$estructura->result();
                   $estructura='';
                   foreach ($e as $f)  
                   {
                     $estructura.=" or id_estructura_owner='".$f->id_estructura."'";
                   }                   
                }                
                $registros= $this->Correspondencia->correspondencia_archivo($entrante,$fecha_ini, 
                             $fecha_fin, $id_usuario, $estructura, $id_tipo, $id_clasificacion );
                break;
      }

      if ($registros->num_rows()<1) 
          return '<br/><br/><table class="TablaNivel1">
              <tr><td><center><h2>No se encontraron Registros</h2></center></td></tr></table>';
      
      // CONSTRUIMOS LA TABLA CON LOS REGISTROS ARROJADOS DE LA CONSULTA
      $tabla='<table class="TablaNivel1 display Correspondencias" id="registros">';
      $tabla.='<thead><tr>';
      $tabla.='<th style="max-width:130px;">IDENTIFICADOR</th>';
      $tabla.='<th width="80px">FECHA</th>';
      $tabla.='<th width="50px">TIPO</th>';
      $tabla.='<th width="270px">REMITENTE</th>';
      $tabla.='<th>ASUNTO</th>';
      $tabla.='<th width="95px">ANALISTA</th>';
      $tabla.='<th width="95px">ESTATUS</th>';
      $tabla.='</tr></thead>';
      $tabla.='<tfoot><tr>';
      $tabla.='<td>IDENTIFICADOR</td>';
      $tabla.='<td>FECHA</td>';
      $tabla.='<td>TIPO</td>';
      $tabla.='<td>REMITENTE</td>';
      $tabla.='<td>ASUNTO</td>';
      $tabla.='<td>ANALISTA</td>';
      $tabla.='<td>ESTATUS</td>';
      $tabla.='</tr></tfoot>';
      $tabla.='<tbody>';
      
      foreach ($registros->result() as $c)
      {
          $tabla.='<tr class="Resaltado" onclick="javascript:VerDetalles('.$c->id_correspondencia.');">';
          $tabla.='<td>';
          $tabla.='<div title="Generado por el Sistema">'.$c->codigo_generado.'</div>';
          $tabla.=($c->nro_comunicado!='')?
                   '<div title="Número de Comunicado">N°Com: '.$c->nro_comunicado.'</div>':'';
          $tabla.=($c->codigo_interno!='')?
                   '<div title="Recepción de Vicepresidencia">'.$c->codigo_interno.'</div>':'';
          $tabla.='</td>';
          $tabla.='<td style="text-align:center">';
          $tabla.='<div class="Oculto">'.Date("Y/m/d",strtotime($c->fecha_recepcion)).'</div>';
          $tabla.='<div title="Fecha de '.(($entrante)?'Recepción':'Envío').'">'.
                   Date("d/m/Y",strtotime($c->fecha_recepcion)).'</div>';
          $tabla.='<div title="Fecha de Emisión">'.Date("d/m/Y",strtotime($c->fecha_emision)).'</div>';
          $tabla.='</td>';
          $tabla.='<td style="text-align:center">';
          $tabla.='<img src="'.base_url().$c->icon_tipo.'" title="'.$c->tipo.'"/>';
          $tabla.='<div class="Oculto">'.$c->tipo.'</div>';
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='<div title="Ente u Organismo">'.trim($c->organismo).'</div>';
          $tabla.='<div title="Dependencia" style="font-size:.9em">'.
                  ((trim($c->pertenece_a)!='')?trim($c->pertenece_a):'').'</div>';
          $tabla.='<div title="Persona que '.(($entrante)?'Envía':'Recibe').'">'.
                  (($entrante)?'Envía: ':'Recibe: ').'<strong>'.$c->remitente_receptor.'</strong></div>';
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='<div title="Categoría">'.$c->clasificacion.'</div>';
          $tabla.='<div title="Asunto">'.$c->asunto.'</div>';
          $tabla.='<div class="Oculto">'.$c->observaciones.'</div>';
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='<div title="Analista Asignado">'.$c->nombre.' '.$c->apellido.'</div>';
          $tabla.='</td>';      
          $tabla.='<td>';
          $tabla.='<div title="Estatus">'.$c->estatus.'</div>';
          $tabla.='<div class="Oculto">'.$c->movimiento.'</div>';
          $tabla.='<div style="text-align:center;">';          
          $tabla.=($c->caso_cerrado=='t')?'<img src="'.base_url().'imagenes/mov12.png'.'" title="'.$c->estatus.'"/>':
                                          '<img src="'.base_url().$c->icon_mov.'" title="'.$c->movimiento.'"/>';
          $tabla.='</div>';
          $tabla.='</td>';  
          $tabla.='</tr>';
      }   
            
      $tabla.='</tbody>';
      $tabla.='</table>';
      
      return $tabla;
    }

    function _cargar_repetidas($id_organismo, $nro_comunicado, $fecha_emision, $entrante)
    {      
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
      
      $registros=$this->Correspondencia->buscar_repetidas($id_organismo, $nro_comunicado,
                                                           $fecha_emision, $entrante);              
      if ($registros->num_rows()<1) 
          return '<br/><br/><table class="TablaNivel1">
              <tr><td><center><h2>No se encontraron Registros</h2></center></td></tr></table>';
      
      // CONSTRUIMOS LA TABLA CON LOS REGISTROS ARROJADOS DE LA CONSULTA
      $tabla='<table class="TablaNivel1 display Correspondencias" id="registros">';
      $tabla.='<thead><tr>';
      $tabla.='<th width="120px">IDENTIFICADOR</th>';
      $tabla.='<th width="80px">FECHA</th>';
      $tabla.='<th width="50px">TIPO</th>';
      $tabla.='<th width="270px">REMITENTE</th>';
      $tabla.='<th>ASUNTO</th>';
      $tabla.='<th width="95px">ANALISTA</th>';
      $tabla.='<th width="95px">ESTATUS</th>';
      $tabla.='</tr></thead>';
      $tabla.='<tfoot><tr>';
      $tabla.='<td>IDENTIFICADOR</td>';
      $tabla.='<td>FECHA</td>';
      $tabla.='<td>TIPO</td>';
      $tabla.='<td>REMITENTE</td>';
      $tabla.='<td>ASUNTO</td>';
      $tabla.='<td>ANALISTA</td>';
      $tabla.='<td>ESTATUS</td>';
      $tabla.='</tr></tfoot>';
      $tabla.='<tbody>';
      
      foreach ($registros->result() as $c)
      {
          $tabla.='<tr class="Resaltado" onclick="javascript:VerDetalles('.$c->id_correspondencia.');">';
          $tabla.='<td>';
          $tabla.='<div title="Generado por el Sistema">'.$c->codigo_generado.'</div>';
          $tabla.=($c->nro_comunicado!='')?
                   '<div title="Número de Comunicado">N°Com: '.$c->nro_comunicado.'</div>':'';
          $tabla.=($c->codigo_interno!='')?
                   '<div title="Recepción de Vicepresidencia">'.$c->codigo_interno.'</div>':'';
          $tabla.='</td>';
          $tabla.='<td style="text-align:center">';
          $tabla.='<div class="Oculto">'.Date("Y/m/d",strtotime($c->fecha_recepcion)).'</div>';
          $tabla.='<div title="Fecha de '.(($entrante)?'Recepción':'Envío').'">'.
                   Date("d/m/Y",strtotime($c->fecha_recepcion)).'</div>';
          $tabla.='<div title="Fecha de Emisión">'.Date("d/m/Y",strtotime($c->fecha_emision)).'</div>';
          $tabla.='</td>';
          $tabla.='<td style="text-align:center">';
          $tabla.='<img src="'.base_url().$c->icon_tipo.'" title="'.$c->tipo.'"/>';
          $tabla.='<div class="Oculto">'.$c->tipo.'</div>';
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='<div title="Ente u Organismo">'.trim($c->organismo).'</div>';
          $tabla.='<div title="Dependencia" style="font-size:.9em">'.
                  ((trim($c->pertenece_a)!='')?trim($c->pertenece_a):'').'</div>';
          $tabla.='<div title="Persona que '.(($entrante)?'Envía':'Recibe').'">'.
                  (($entrante)?'Envía: ':'Recibe: ').'<strong>'.$c->remitente_receptor.'</strong></div>';
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='<div title="Categoría">'.$c->clasificacion.'</div>';
          $tabla.='<div title="Asunto">'.$c->asunto.'</div>';
          $tabla.='<div class="Oculto">'.$c->observaciones.'</div>';
          $tabla.='</td>';
          $tabla.='<td>';
          $tabla.='<div title="Analista Asignado">'.$c->nombre.' '.$c->apellido.'</div>';
          $tabla.='</td>';      
          $tabla.='<td>';
          $tabla.='<div title="Estatus">'.$c->estatus.'</div>';
          $tabla.='<div class="Oculto">'.$c->movimiento.'</div>';
          $tabla.='<div style="text-align:center;">';          
          $tabla.=($c->caso_cerrado=='t')?'<img src="'.base_url().'imagenes/mov10.png'.'" title="'.$c->estatus.'"/>':
                                          '<img src="'.base_url().$c->icon_mov.'" title="'.$c->movimiento.'"/>';
          $tabla.='</div>';
          $tabla.='</td>';  
          $tabla.='</tr>';
      }   
            
      $tabla.='</tbody>';
      $tabla.='</table>';
      
      return $tabla;
    }
    
    function listar_registros($tabla=0)
    {   
      // Si la peticion vino por AJAX   
      if ($this->input->is_ajax_request()){$tabla=$this->input->post('tabla');}
      
      $registros=$this->Crud->listar_registros($tabla); 
      
      if ($this->input->is_ajax_request()) die($registros); // Si la peticion vino por AJAX
      else return $registros;
    }

    // Construye las opciones de Combo-Select a partir de una matriz
    function _construye_opciones($opciones, $seleccionada=0)  
    {      
      $combo='';
      foreach ($opciones as $value => $text)
      {
        if ($value == $seleccionada)
        {
          $combo.='<option value="'.$value.'" selected="selected">'.$text.'</option>';
        }
        else
        {
          $combo.='<option value="'.$value.'">'.$text.'</option>';
        }
      }
      return $combo;
    }
    
    function actualiza_sesion()
    {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
      // CONVERSION NECESARIA PARA COMPATIBILIDAD DE VARIABLES BOOL ENTRE AJAX y PHP
      $entrante=($this->input->post('entrante')=='true')?true:false;
      $fecha_ini=$this->input->post('fecha_inicial');
      $fecha_fin=$this->input->post('fecha_final');
      $id_tipo=  intval($this->input->post('id_tipo'));
      $id_clasificacion=intval($this->input->post('id_clasificacion'));
      $bandeja=  intval($this->input->post('bandeja'));
      
      $this->session->set_userdata('entrante',$entrante);
      $this->session->set_userdata('fecha_inicial',$fecha_ini);
      $this->session->set_userdata('fecha_final',$fecha_fin);
            
      $tabla=$this->_cargar_registros($bandeja, $id_tipo, $id_clasificacion);      
      die($tabla);
    }
}
?>