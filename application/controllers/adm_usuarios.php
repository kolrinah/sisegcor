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
class Adm_usuarios extends CI_Controller {
  function __construct() 
  {
     parent::__construct();
     //$this->load->helper('form');
     //$this->load->library('form_validation');
     $this->load->model('Usuarios');
     $this->load->model('Estructura');
     $this->load->model('Crud');
  }
  
  function index()
  {
    // VERIFICAMOS SI EXISTE SESION ABIERTA    
    if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    
    // VERIFICACIÓN DE PERMISOS NECESARIOS PARA ACCESAR EL CONTROLADOR:
    // * DEBE TENER NIVEL DE USUARIO IGUAL O SUPERIOR A COORDINADOR
    //   (id_nivel>1)
    // * O DEBE TENER ROL DE ADMINISTRADOR       
    if (!($this->session->userdata('administrador') || intval($this->session->userdata('id_nivel'))<6))exit('Sin Acceso al Script');
          
    $data=array();
    $data['titulo']='Administración de Usuarios';
    $data['contenido']='adm_usuarios';    
    $data['script']='<!-- Cargamos CSS de DataTables -->'."\n";    
    $data['script'].="\t".'<link rel="stylesheet" type="text/css" media="all" href="'.base_url().'css/dataTables.css"/>'."\n";
  
    $data['script'].='<!--Incluimos Funciones JS de uso común-->'."\n";
    $data['script'].="\t".'<script type="text/javascript" charset="utf-8" src="'.base_url().'js/comunes.js"></script>'."\n";   
    
    $data['script'].='<!-- Cargamos JS para DataTables -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/jquery.dataTables.js"></script>'."\n";
    $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
    $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/adm_usuarios.js"></script>'."\n";
                
    // VERIFICAMOS SI EL USUARIO ES ADMINISTRADOR PARA ASIGNARLE LA ESTRUCTURA
    $id_estructura=$this->session->userdata('administrador')?1:intval($this->session->userdata('id_estructura'));
           
    $usuarios=$this->Usuarios->listar_usuarios($id_estructura);  
    
    // CONSTRUIMOS LA TABLA CON EL LISTADO DE USUARIOS ARROJADOS DE LA CONSULTA
    $tabla='<table class="TablaNivel1 display" id="usuarios">';
    $tabla.='<thead><tr><th width="26px"></th>';
    $tabla.='<th width="200px">Nombre y Apellido</th>';
    $tabla.='<th>Correo</th>';
    $tabla.='<th>Unidad Administrativa</th>';
    $tabla.='<th width="30px"></th>';
    $tabla.='</tr></thead>';
    $tabla.='<tfoot><tr>';
    $tabla.='<td></td>';
    $tabla.='<td>Nombre y Apellido</td>';
    $tabla.='<td>Correo</th>';
    $tabla.='<td>Unidad Administrativa</td>';
    $tabla.='<td></td>';
    $tabla.='</tr></tfoot>';
    $tabla.='<tbody>';
    
    if (!$usuarios) // SI NO HAY USUARIOS
    {           
      $tabla.='<tr><td colspan="5" title="Para Agregar Usuarios Haga clic en el ícono">';
      $tabla.='<h2><center>La Unidad No Posee Usuarios</center></h2>';
      $tabla.='</td></tr>';
    }   
    else 
    {    
    foreach ($usuarios as $fila)
    {
     $tabla.=(trim($fila['activo'])=='t')?'<tr>':'<tr class="inactivo">';        
     $tabla.='<td>';
     $tabla.='<img src="'.base_url().'imagenes/lupa.png" ';
     $tabla.='onclick="javascript:EditarUsuario('.intval($fila['id_usuario']).');" ';
     $tabla.='class="BotonIco" title="Editar Usuario"/>';
     $tabla.='</td>';
     $tabla.='<td>';
     $tabla.=trim($fila['nombre']).' '.trim($fila['apellido']);
     $tabla.='</td>';
     $tabla.='<td>';
     $tabla.=trim($fila['correo']);
     $tabla.='</td>';
     $tabla.='<td>';
     $tabla.=trim($fila['codigo_estructura']).' '.trim($fila['estructura']);
     $tabla.='</td>';
     $tabla.='<td>';
       $imagen='';
       $imagen=($fila['id_nivel']<5)?'<img src="'.base_url().'imagenes/jefes.png" title="Jefe o Jefa de Unidad" />':$imagen;
       $imagen=($fila['id_nivel']==5)?'<img src="'.base_url().'imagenes/distribuidor.png" title="Distribuidor de Correspondencia" />':$imagen;
       $imagen=($fila['id_nivel']==7)?'<img src="'.base_url().'imagenes/revisor.png" title="Revisor de Correspondencias" />':$imagen;
       $imagen=($fila['administrador']=='t')?'<img src="'.base_url().'imagenes/admin.png" title="Administrador del Sistema" />':$imagen;
       $imagen=($fila['activo']=='f')?'<img src="'.base_url().'imagenes/cancel16.png" title="Usuario Inactivo" />':$imagen;     
     $tabla.=$imagen;
     $tabla.='</td>';
     $tabla.='</tr>';
    }
    }
    $tabla.='</tbody></table>';       
    
    $data['tabla_usuarios']=$tabla;
    
// BOTON DE AGREGAR USUARIO
    $admin=($this->session->userdata('administrador'))?1:0;
    $boton='<center><div class="BotonIco" onclick="javascript:AgregarUsuario('.$admin.')" title="Agregar Usuario">';
    $boton.='<img src="imagenes/add_user.png"/>&nbsp;';   
    $boton.='Agregar';
    $boton.= '</div></center>';
    
    $data['boton_agregar']=$boton;
    
 // CARGAMOS LA VISTA   
    $this->load->view('plantillas/plantilla_general',$data);  
  }

  function buscar_usuario()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    
    $datos=array(
             'cedula' => $this->input->post('patron'),
             'correo' => $this->input->post('patron')
                 );         
    die($this->Crud->buscar_item('usr_usuarios', $datos));    
  }  
  
  function buscarLDAP()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    
    $patron=$this->input->post('patron');           
                 
    die($this->Crud->buscarLDAP($patron));    
  }
  
  function insertar_usuario()
  {   
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
        $usuario=explode("@",$this->input->post('correo'));
        $datos=array(
                'usuario'       => strtolower($usuario[0]),
                'id_estructura' => $this->input->post('id_estructura'),
                'cedula'        => $this->input->post('cedula'),          
                'nombre'        => mb_convert_case($this->input->post('nombre'),MB_CASE_TITLE),
                'apellido'      => mb_convert_case($this->input->post('apellido'),MB_CASE_TITLE),
                'correo'        => strtolower($this->input->post('correo')),
                'id_nivel'      => $this->input->post('id_nivel'),
                'administrador' => $this->input->post('administrador')
                );
        $insertado=$this->Crud->insertar_registro('usr_usuarios', $datos);
        if (!$insertado){die('Error');}
        else
        {
           $registro='id_usuario: '.$this->db->insert_id();
           $registro.='. '.$datos['usuario'];           
           $registro.='. Registrado por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'usr_usuarios',
               'tipo_accion'    =>'INSERT',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora);             
        }    
  }
  
  function editar_usuario()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
    
    $id_usuario=$this->input->post('id_usuario');
    $usuario= $this->Usuarios->obtener_usuario($id_usuario);  
    // CONSTRUIMOS EL FORMULARIO DE EDICION Y CARGAMOS LA INFORMACION DEL USUARIO CONSULTADO
    $form='<div class="EntraDatos">';
    $form.='<table>';
    $form.='<thead>';
    $form.='<tr><th colspan="2">';            
    $form.='Editar Usuario';  
    $form.='</th></tr>';           
    $form.='</thead>';            
    $form.='<tbody>';
    $form.='<tr>';
    $form.='<td width="50%">';              
    $form.='<label>Cédula de Identidad:</label><br/>';    
    $form.='<input type="text" class="Campos" id="CI" title="Número de Cédula" readonly="readonly" ';
    $form.='value="'.$usuario->cedula.'"  />';
    $form.='</td>';
    $form.='<td>';
    $form.='<label>Correo Electrónico:</label><br/>';
    $form.='<input type="text" class="Campos" id="Correo" title="Correo Electrónico" readonly="readonly" ';
    $form.='value="'.$usuario->correo.'" />';
    $form.='</td>';
    $form.='</tr>';
    $form.='<tr>';
    $form.='<td>';
    $form.='<label>Nombre:</label><br/>';
    $form.='<input type="text" class="Campos" id="Nombre" title="Nombre" readonly="readonly" ';
    $form.='value="'.$usuario->nombre.'" />';
    $form.='</td>';
    $form.='<td>';
    $form.='<label>Apellido:</label><br/>';
    $form.='<input type="text" class="Campos" id="Apellido" title="Apellido" readonly="readonly" ';
    $form.='value="'.$usuario->apellido.'" />';
    $form.='</td>';
    $form.='</tr>';  
    $form.='<tr>';
    $form.='<td colspan="2">';
    $form.='<input type="hidden" id="id_unidad" ';
    $form.='value="'.$usuario->id_estructura.'" />';
    $form.='<label>Unidad Administrativa:</label><br/>';
    $form.='<input type="text" class="Campos Editable" id="Unidad" title="Unidad Administrativa" tabindex="10" ';
    $form.='value="'.$usuario->codigo_estructura.' - '.$usuario->estructura.'" />';
    $form.='</td>';  
    $form.='</tr>';
    $form.='<tr>';
    $form.='<td>';
    $form.='<label>Nivel de Usuario:</label><br/>';    
    $form.='<select class="Campos Editable" id="Nivel" title="Nivel de Usuario" title="Seleccione el Nivel del Usuario" tabindex="11">';
     
     // Caja Combo para Niveles de Usuario 
     $nivel=$this->Crud->listar_registros('usr_niveles',array('id_nivel'=>$usuario->id_tipo_estructura));
     $n=($nivel->num_rows>0)?$nivel->row():die('Error');
     
     $a=array($n->id_nivel, 5, 6, 7); $b=array($n->nivel, 'Distribuidor', 'Analista', 'Revisor');
     $opciones=array_combine($a,$b);
     ksort($opciones);
     $opciones=$this->_construye_opciones($opciones, $usuario->id_nivel);
     
    $form.=$opciones;    
    $form.='</select>';
    $form.='</td>';
    $form.='<td>';
    
      if ($usuario->activo=='t')
      {
          $datos=array(
                      'img'  =>base_url()."imagenes/activo16.png",
                      'span' => 'Usuario Activo',
                      'valor'=>'t');
      }
      else
      {
          $datos=array(
                      'img'  =>base_url()."imagenes/cancel16.png",
                      'span' => 'Usuario Inactivo',
                      'valor'=>'f');
      }          
    $form.='<label>Estado de Usuario:</label><br/>';
    $form.='<div class="ToggleBoton" onclick="javascript:ToggleBotonActivo()" title="Haga clic para cambiar">';
    $form.='<img id="imgActivo" src="'.$datos['img'].'"/>';
    $form.='</div>';
    $form.='<span id="spanActivo">&nbsp;'.$datos['span'].'</span>';
    $form.='<input type="hidden" id="hideActivo" value="'.$datos['valor'].'" />';
    
    $form.='</td>';
    $form.='</tr>';
    $form.='<tr>';
    $form.='<td>';
    $form.='</td>';
    $form.='<td>';
      if ($this->session->userdata('administrador'))
      {
        if ($usuario->administrador=='t')
        {
            $datos=array(
                        'img'  =>base_url()."imagenes/admin16.png",
                        'span' => 'Administrador',
                        'valor'=>'t');
        }
        else
        {
            $datos=array(
                        'img'  =>base_url()."imagenes/user16.png",
                        'span' => 'Usuario Normal',
                        'valor'=>'f');
        }          
        $form.='<label>Rol de Usuario:</label><br/>';
        $form.='<div class="ToggleBoton" onclick="javascript:ToggleBotonAdmin()" title="Haga clic para cambiar">';
        $form.='<img id="imgAdmin" src="'.$datos['img'].'"/>';
        $form.='</div>';
        $form.='<span id="spanAdmin">&nbsp;'.$datos['span'].'</span>';
        $form.='<input type="hidden" id="hideAdmin" value="'.$datos['valor'].'" />';
      }
      else
      {
        $form.='<input type="hidden" id="hideAdmin" value="'.$usuario->administrador.'" />';  
      }
    
    $form.='</td>';
    $form.='</tr>';
    $form.='</tbody>';
    
    $form.='<tfoot>';
    $form.='<tr><td colspan="2">';
    $form.='<div class="BotonIco" onclick="javascript:ResetearClave('.$usuario->id_usuario.')" title="Reiniciar contraseña">';
    $form.='<img src="imagenes/reset.png"/>&nbsp;';   
    $form.='Reiniciar';
    $form.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $form.='<div class="BotonIco" onclick="javascript:ActualizarUsuario('.$usuario->id_usuario.')" title="Guardar Cambios">';
    $form.='<img src="imagenes/guardar32.png"/>&nbsp;';   
    $form.='Guardar';
    $form.= '</div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';    
    $form.='<div class="BotonIco" onclick="javascript:CancelarModal()" title="Cancelar">';
    $form.='<img src="imagenes/cancel.png"/>&nbsp;';
    $form.='Cancelar';
    $form.= '</div>';
    $form.='</td></tr>';
    $form.='</tfoot>';
    $form.='</table>';   
    $form.='</div>';
    die($form);
  }
  
  function actualizar_usuario()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      $donde=array(
                'id_usuario'  => intval($this->input->post('id_usuario'))       
                  );
      $datos=array(                
              'id_estructura' => $this->input->post('id_estructura'),
              'id_nivel'      => $this->input->post('id_nivel'),
              'activo'      => $this->input->post('activo'),
              'administrador' => $this->input->post('administrador')                
              );        
      $actualizado=$this->Crud->actualizar_registro('usr_usuarios', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='id_usuario: '.$donde['id_usuario'];         
         $registro.='. Actualizado por: '.$this->session->userdata('usuario');
         $bitacora=array(
             'direccion_ip'   =>$this->session->userdata('ip_address'),
             'navegador'      =>$this->session->userdata('user_agent'),
             'id_usuario'     =>$this->session->userdata('id_usuario'),
             'controlador'    =>$this->uri->uri_string(),
             'tabla_afectada' =>'usr_usuarios',
             'tipo_accion'    =>'UPDATE',
             'registro'       =>$registro
         );
         $this->Crud->insertar_registro('z_bitacora', $bitacora);            
      };
  }
  
  function resetear_clave()
  {
      if (!$this->input->is_ajax_request()) die('Acceso Denegado');
      $donde=array(
                'id_usuario'  => intval($this->input->post('id_usuario'))       
                  );
      $datos=array(                
              'clave' => '202cb962ac59075b964b07152d234b70' // Clave Inicial: "123"             
              );        
      $actualizado=$this->Crud->actualizar_registro('usr_usuarios', $datos, $donde);        
      if (!$actualizado){die('Error');}
      else 
      {           
         $registro='id_usuario: '.$donde['id_usuario'];         
         $registro.='. Reinicialización de Contraseña hecha por: '.$this->session->userdata('usuario');
         $bitacora=array(
             'direccion_ip'   =>$this->session->userdata('ip_address'),
             'navegador'      =>$this->session->userdata('user_agent'),
             'id_usuario'     =>$this->session->userdata('id_usuario'),
             'controlador'    =>$this->uri->uri_string(),
             'tabla_afectada' =>'usr_usuarios',
             'tipo_accion'    =>'UPDATE',
             'registro'       =>$registro
         );
         $this->Crud->insertar_registro('z_bitacora', $bitacora);            
      };
  } 
   
  function listar_unidades()
  {
    if (!$this->input->is_ajax_request()) die('Acceso Denegado');
   
    $frase=$this->input->post('frase');
    $id_estructura=($this->session->userdata('administrador'))?1:$this->session->userdata('id_estructura');
    die(json_encode($this->Estructura->listar_unidades($frase,$id_estructura))); 
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
} ?>