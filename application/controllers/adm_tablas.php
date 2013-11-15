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
class Adm_tablas extends CI_Controller {
  function __construct() 
  {
     parent::__construct();
     $this->load->model('Crud');
  }
  
  function index()
  {
    // VERIFICAMOS SI EXISTE SESION ABIERTA    
    if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    
    // VERIFICACIÓN DE PERMISOS NECESARIOS PARA ACCESAR EL CONTROLADOR:    
    // * DEBE TENER ROL DE ADMINISTRADOR    
    if (!($this->session->userdata('administrador')))exit('Sin Acceso al Script');
          
    $data=array();
    $data['titulo']='Administrador de Tablas';
    $data['contenido']='adm_tablas';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";

    $data['script'].='<!--Incluimos Funciones JS de uso común-->'."\n";
    $data['script'].="\t".'<script type="text/javascript" charset="utf-8" src="'.base_url().'js/comunes.js"></script>'."\n";

    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/adm_tablas.js"></script>'."\n";
                
               
    $tablas=$this->Crud->listar_tablas();  
    asort($tablas);
    
    // CONSTRUIMOS LA TABLA A PARTIR DE LA CONSULTA
    $tabla='<table class="TablaNivel1 Zebrado">';
    $tabla.='<thead><tr><th width="150px">Nombre de la Tabla</th>';  
    $tabla.='<th width="40px"></th>';
    $tabla.='<th width="40px"></th>';
    $tabla.='<th>Campos</th>';
    $tabla.='</tr></thead>';
    $tabla.='<tfoot><tr>';
    $tabla.='<td>Nombre de la Tabla</td>';
    $tabla.='<td></td>';
    $tabla.='<td></td>';
    $tabla.='<td>Campos</td>';    
    $tabla.='</tr></tfoot>';
    $tabla.='<tbody>';
    
    if (!$tablas) // SI NO HAY TABLAS
    {           
      $tabla.='<tr><td colspan="4" title="Sistema Original">';
      $tabla.='<h2><center>No hay Tablas en el Sistema</center></h2>';
      $tabla.='</td></tr>';
    }   
    else 
    {    
    foreach ($tablas as $fila)
    {
     $tabla.='<tr>';
     $tabla.='<td style="text-align:left!important">';
     $tabla.=trim($fila);
     $tabla.='</td>';
     $tabla.='<td>';
     $tabla.='<img src="'.base_url().'imagenes/agregar16.png" class="BotonIco" ';
     $tabla.='onclick="javascript:agregar_campo(\''.trim($fila).'\')"/>';
     $tabla.='</td>';
     $tabla.='<td>';
     $tabla.='<img src="'.base_url().'imagenes/lupa.png" class="BotonIco" ';
     $tabla.='onclick="javascript:listar_campos(\''.trim($fila).'\')"/>';
     $tabla.='</td>';
     $tabla.='<td>';
     $tabla.='<div id="'.trim($fila).'" class="TablaNivel2"></div>';
     $tabla.='</td>';
     $tabla.='</tr>';
    }
    }
    $tabla.='</tbody></table>';       
    
   $data['tablas']=$tabla;    
   
   // BOTON CORRER SQL
    $boton='<center><div class="BotonIco" onclick="javascript:CorrerSQL();" title="Correr Sentencia SQL">';
    $boton.='<img src="imagenes/sql.png"/>&nbsp;';   
    $boton.='SQL';
    $boton.= '</div></center>';
    // FIN BOTON SQL
    
    // ENTRADA TEXTO SQL
    $sql='<textarea class="Editable" style="width:100%" id="sql" rows="2" title="Escriba la Sentencia SQL">';
    $sql.='</textarea>';
    // FIN ENTRADA TEXTO    
    
    $data['boton']=$boton;
    $data['sql']=$sql;
   
 // CARGAMOS LA VISTA   
    $this->load->view('plantillas/plantilla_general',$data);  
  }
  
  function listar_campos()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // SI LA PETICION NO ES DE AJAX ABORTA
    
    $tabla=  $this->input->post('tabla'); 
    $campos=$this->Crud->listar_campos($tabla);       
    // CONSTRUYO MI TABLA CON LOS CAMPOS
    $tcampos='<table>';
    $tcampos.='<thead>';
    $tcampos.='<tr>';
    $tcampos.='<th>';
    $tcampos.='Nombre';    
    $tcampos.='</th>';
    $tcampos.='<th width="100px">';
    $tcampos.='Tipo';    
    $tcampos.='</th>';
    $tcampos.='<th width="100px">';
    $tcampos.='Longitud';    
    $tcampos.='</th>';
    $tcampos.='<th width="100px" >';
    $tcampos.='</th>';
    $tcampos.='</tr>';
    $tcampos.='</thead>';
    $tcampos.='<tfoot>';
    $tcampos.='<tr>';
    $tcampos.='<td colspan="4">';
    $tcampos.='</td>';
    $tcampos.='</tr>';
    $tcampos.='</tfoot>';
    
    $tcampos.='<tbody>';
    foreach ($campos as $campo)
    {
      $tcampos.='<tr>';
      $tcampos.='<td>';
      $tcampos.=$campo->name;
      $tcampos.='</td>';
      $tcampos.='<td style="text-align:center">';
      $tcampos.=$campo->type;
      $tcampos.='</td>';
      $tcampos.='<td style="text-align:center">';
      $tcampos.=$campo->max_length;      
      $tcampos.='</td>';
      $tcampos.='<td>';
      $tcampos.='<img src="'.base_url().'imagenes/close.png" class="BotonIco" title="Borrar Campo"';
      $tcampos.='onclick="javascript:BorrarCampo(\''.$tabla.'\',\''.$campo->name.'\')"/>';
      $tcampos.='</td>';
      $tcampos.='</tr>';
    }    
    $tcampos.='</tbody>';
    $tcampos.='</table>';
    die($tcampos);
  }
  
  function guardar_campo()
  {      
     if (!$this->input->is_ajax_request()) die('Acceso Denegado');
     
     $tabla=$this->input->post('tabla');
     $nombre=$this->input->post('nombre');
     $tipo=$this->input->post('tipo');
     $longitud=$this->input->post('longitud');
     $nulo=$this->input->post('nulo');
     $omision=$this->input->post('omision');
     
     // ARMAMOS EL COMANDO ALTER
     $comando=$nombre.' '.$tipo;
     $comando.=($tipo=='character varying' || $tipo=='character')?'('.$longitud.') ':' ';
     $comando.=$nulo;
     $comando.=($omision!='')?" DEFAULT ".$omision:"";
     
     $insertado=$this->Crud->agregar_campo($tabla, $comando);
     if (!$insertado){die('Error');}
     else 
     {
        $registro='Agregado el campo: "'.$nombre;
        $registro.='" en la tabla: "'.$tabla;           
        $registro.='". Registrado por: '.$this->session->userdata('usuario');
        $bitacora=array(
            'direccion_ip'   =>$this->session->userdata('ip_address'),
            'navegador'      =>$this->session->userdata('user_agent'),
            'id_usuario'     =>$this->session->userdata('id_usuario'),
            'controlador'    =>$this->uri->uri_string(),
            'tabla_afectada' =>$tabla,
            'tipo_accion'    =>'ALTER TABLE',
            'registro'       =>$registro
        );
        $this->Crud->insertar_registro('z_bitacora', $bitacora);
     }
  }
  
  function borrar_campo()
  {      
     if (!$this->input->is_ajax_request()) die('Acceso Denegado');
     $tabla=$this->input->post('tabla');
     $campo=$this->input->post('campo');

     $borrado=$this->Crud->borrar_campo($tabla, $campo);
     if (!$borrado){die('Error');}
     else 
     {
        $registro='Borrado del campo: "'.$campo;
        $registro.='" de la tabla: "'.$tabla;           
        $registro.='". Registrado por: '.$this->session->userdata('usuario');
        $bitacora=array(
            'direccion_ip'   =>$this->session->userdata('ip_address'),
            'navegador'      =>$this->session->userdata('user_agent'),
            'id_usuario'     =>$this->session->userdata('id_usuario'),
            'controlador'    =>$this->uri->uri_string(),
            'tabla_afectada' =>$tabla,
            'tipo_accion'    =>'ALTER TABLE',
            'registro'       =>$registro
        );
        $this->Crud->insertar_registro('z_bitacora', $bitacora);
     }
  }
  
  function correr_sql()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    
    $sql=$this->input->post('sql');
    
    $resultado=$this->Crud->ventana_sql($sql);
    if (!$resultado)die('<br/><h2><center>-> SIN RESULTADOS <-</center></h2>');
    
    if (is_bool($resultado)===true)die('<br/><h2><center>-> ¡OPERACION EXITOSA! <-</center></h2>');
    
    // CONSTRUIMOS LA TABLA A PARTIR DE LA CONSULTA
    $tabla='<table style="max-width:100%" class="TablaNivel1 Zebrado" id="Tabla">';
    
    // CONSTRUIMOS LA CABECERA
    $tabla.='<thead>';
    $i=0;
    $sizecol=count($resultado[0])>0? 100/count($resultado[0]):0;
    foreach ($resultado[0] as $campo=>$valor)
    {
        $tabla.='<th style="max-width:'.$sizecol.'%">';
        $tabla.=$i;
        $tabla.='</th>';
        $i++;
    }
    $tabla.='</thead>';
    
    // CONSTRUIMOS EL PIE
    $tabla.='<tfoot>';
    $i=0;
    foreach ($resultado[0] as $campo=>$valor)
    {
        $tabla.='<td>';
        $tabla.=$i;
        $tabla.='</td>';
        $i++;
    }
    $tabla.='</tfoot>';
    
    foreach ($resultado as $fila)
    {
      $tabla.='<tr>';
      foreach ($fila as $campo=>$valor)
      {
         $tabla.='<td style="text-align:left" title="'.$campo.'">'; 
         $tabla.=$valor;
         $tabla.='</td>';
      }      
      $tabla.='</tr>';
    }
    $tabla.='</table>';
    
    die($tabla);
  }
      
}?>