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
class Registrar extends CI_Controller {

    function __construct() 
    {
      parent::__construct();
      $this->load->helper('form');
      $this->load->model('Usuarios');
      $this->load->model('Organismos');
      $this->load->model('Correspondencia');
      $this->load->model('Crud');
    }
    
    function index()
    {   
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
      $this->correspondencia();
    }
    
    function correspondencia($entrante='entrante')
    { 
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora              
      $tiempo=  getdate(time()); // hace 30 días
      $fecha= $tiempo['mday']<10? '0'.$tiempo['mday']:$tiempo['mday'];
      $fecha.='/';
      $fecha.=$tiempo['mon']<10?'0'.$tiempo['mon']:$tiempo['mon'];
      $fecha.='/';
      $fecha.=$tiempo['year'];
      $data=array();
      
      $data['titulo']='Registro de Correspondencia ';
      
      $data['titulo_formulario']='Formulario de Registro de Correspondencia ';
      $data['titulo_formulario'].=$entrante;
      
      $data['e_s']=($entrante=='entrante')?'Envía':'Recibe';  
      
      $data['leyenda']=($entrante=='entrante')?'Procedencia:':'Destino:';   
      // Caja Combo para Tipos de Organismos
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->listar_registros('dir_tipo_organismos');
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array('[Seleccione]');
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_tipo_organismo);
           array_push($b,$fila->organismo);
         }         
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->construye_opciones($opciones);
      }
      
      $data['tipo_organismo']='<select id="tipo_organismo" tabindex="100" class="Editable Campos" title="Tipo de Ente que Emite la Correspondencia">';
      $data['tipo_organismo'].=$opciones;
      $data['tipo_organismo'].='</select>';
      
      $data['ente']= array(
                        'label'=>($entrante=='entrante')?
                                    'Ente Emisor de la Correspondencia:':
                                    'Ente Receptor de la Correspondencia:',
                         'e_s' => $data['e_s'],
                        'name' => 'ente',
                          'id' => 'ente',
                    'tabindex' => 101,
                       'class' => 'Editable CampoFicha',
                       'value' => '   -- Seleccione Primero el tipo de Organismo que '.
                                    $data['e_s'].' la Correspondencia --',
                       'title' => 'Ente u Organismo que '.$data['e_s'].':',          
                    'disabled' => 'disabled'); 
      
      $data['id_organismo']= array(
                        'type'=>'hidden',
                        'name' => 'id_organismo',
                          'id' => 'id_organismo',                        
                       'value' => '0'); 
      
      $data['remitente']= array(
                        'label'=>($entrante=='entrante')? 'Remitente:':'Receptor:',
                        'name' => 'remitente_receptor',
                          'id' => 'remitente_receptor',
                    'tabindex' => 102,
                'autocomplete' => 'on',
                       'class' => 'Editable CampoFicha',
                     //  'value' => '',
                   'maxlength' => '350',
                       'title' => 'Persona que '.$data['e_s'].' la Correspondencia');
      
      $data['nro_comunicado']= array(                        
                              'id' => 'nro_comunicado',
                            'name' => 'nro_comunicado',
                        'tabindex' => 103,
                    'autocomplete' => 'on',
                           'class' => 'Editable Campos',
                      //     'value' => '',
                       'maxlength' => '50',
                            'title'=> 'Número de Comunicado');
      
      $data['codigo_interno']= array(                        
                              'id' => 'codigo_interno',
                            'name' => 'codigo_interno',
                        'tabindex' => 104,
                           'class' => 'Editable Campos',
                     //      'value' => '',
                       'maxlength' => '50',
                           'title' => 'Código Interno de Recepción de Vicepresidencia');

      $data['boton_registrar'] ='<div class="BotonIco" title="Registrar Correspondencia" ';
      $data['boton_registrar'].='onclick="javascript:BuscarCorrespondencia(\'';
      $data['boton_registrar'].=$entrante;      
      $data['boton_registrar'].='\');">';
      $data['boton_registrar'].='<img src="'.base_url().'imagenes/guardar32.png"/>&nbsp;&nbsp;&nbsp;';
      $data['boton_registrar'].='<a href="#" tabindex="111">';
      $data['boton_registrar'].='Registrar';
      $data['boton_registrar'].='</a>';
      $data['boton_registrar'].='</div>';      
      
      // BOTON DE SALIR
      $salir=array(
               'src' => base_url().'imagenes/exit.png',
               'alt' => 'Salir',
             'title' => 'Cerrar',
             'style' => 'cursor:pointer; max-width:32px; max-height:32px',
           'onclick' => "javascript:window.location='".base_url()."bandeja/asignadas'"
                   );      
      
      $data['botonsalir']=img($salir);
      
      // Caja Combo para Tipo de Correspondencia
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->listar_registros('cor_tipos');
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array('[Seleccione]');         
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_tipo);
           array_push($b,$fila->tipo);
         }         
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->construye_opciones($opciones);
      }
      
      $data['tipo']= '<select id="id_tipo" tabindex="105" class="Editable Campos" title="Tipo de Correspondencia">';
      $data['tipo'].=$opciones;
      $data['tipo'].='</select>';
      
      // Caja Combo para Categorías de Correspondencia
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->Crud->listar_registros('cor_clasificaciones');
      if ($tipos->num_rows()>0)
      {
         $a=array('0'); $b=array(' [Seleccione]');
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_clasificacion);
           array_push($b,$fila->clasificacion);
         }         
         $opciones=array_combine($a,$b);
         asort($opciones);
         $opciones=$this->construye_opciones($opciones);
      }
      
      $data['clasificacion']= '<select id="id_clasificacion" tabindex="106" class="Editable Campos" title="Clasificación de Correspondencia">';
      $data['clasificacion'].=$opciones;
      $data['clasificacion'].='</select>';
            
      $data['fecha_recepcion']= array(
                         'label' => ($entrante)?'Fecha de Recepción:':'Fecha de Envío:',
                          'name' => 'fecha_recepcion',              
                            'id' => 'fecha_recepcion',
                      'tabindex' => 107,
                         'class' => 'Fechas Editable',
                         'value' => $fecha,
                     'maxlength' => '10',
                         'title' =>($entrante)?
                                    'Fecha de Recepción de la Correspondencia:':
                                    'Fecha de Envío de la Correspondencia:',
                      'readonly' => 'readonly',                        
                          'size' => 10);
      
      $data['fecha_emision']= array( 
                          'name' => 'fecha_emision',
                            'id' => 'fecha_emision',
                         'class' => 'Fechas Editable',
                         'value' => $fecha,
                     'maxlength' => '10',
                      'tabindex' => 108,
                         'title' => 'Fecha de Elaboración de la Correspondencia',
                      'readonly' => 'readonly',
                          'size' => 10);   
      
      $data['asunto']= array(
                          'name' => 'asunto',
                            'id' => 'asunto',
                      'tabindex' => 109,
                  'autocomplete' => 'on',
                         'class' => 'Editable CampoFicha',
                  //       'value' => '',
                         'title' => 'Asunto');
      
      $data['observaciones']= array(  
                          'name' => 'observaciones',
                            'id' => 'observaciones',
                      'tabindex' => 110,
                         'class' => 'Editable CampoFicha',
                       //  'value' => '',                        
                         'title' => 'Observaciones',
                          'rows' => '2'); 
      
      $data['analista']= mb_convert_case($this->session->userdata('usuario'), MB_CASE_TITLE);                        
            
      $data['contenido']='form_registrar';
      
      $data['script']='<!--Incluimos Funciones JS de uso común-->'."\n";
      $data['script'].="\t".'<script type="text/javascript" charset="utf-8" src="'.base_url().'js/comunes.js"></script>'."\n";
      
      $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
      $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/registrar.js"></script>'."\n";
       
      $this->load->view('plantillas/plantilla_general',$data);
    }
    
    function registrar_correspondencia()
    {
      // if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
       
       // COMPROBAMOS QUE LA CORRESPONDENCIA NO ESTE REPETIDA; TOMANDO EN CUENTA LOS CAMPOS:
       // id_organismo, nro_comunicado y fecha_emision
              
       $datos=array(
                 // Campos que vienen por POST    
                 'id_organismo'=>$this->input->post('id_organismo'),
                 'remitente_receptor'=>ucfirst($this->input->post('remitente_receptor')),
                 'nro_comunicado'=>mb_convert_case($this->input->post('nro_comunicado'),MB_CASE_UPPER),
                 'codigo_interno'=>mb_convert_case($this->input->post('codigo_interno'),MB_CASE_UPPER),
                 'id_tipo'=>$this->input->post('id_tipo'),
                 'id_clasificacion'=>$this->input->post('id_clasificacion'),
                 'fecha_recepcion'=>$this->input->post('fecha_recepcion'),
                 'fecha_emision'=>$this->input->post('fecha_emision'),
                 'asunto'=>ucfirst($this->input->post('asunto')),
                 'observaciones'=>ucfirst($this->input->post('observaciones')),
                 'id_usuario_asignado'=>$this->session->userdata('id_usuario'),
                 'entrante'=>$this->input->post('entrante'),
                 
                 // Campos Automáticos
                 'id_estructura_owner' => $this->session->userdata('id_estructura'),
                 'codigo_generado'=>$this->_generar_codigo($this->input->post('entrante'),
                                                           $this->input->post('fecha_recepcion'))
                 );
         
       // PROCEDEMOS A INSERTAR LA CORRESPONDENCIA
       $insertado=$this->Crud->insertar_registro('cor_correspondencias', $datos);
       if (!$insertado){die('Error');}
       
       // PREPARAMOS PARA INSTERTAR EL MOVIMIENTO INICIAL
       $id_correspondencia=$this->db->insert_id(); // OBTENEMOS LA id_correspondencia INSERTADA
       
       date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
       $fecha=getdate(time());	
       $fecha['mday']=($fecha['mday']<10)?"0".$fecha['mday'] :$fecha['mday'];
       $fecha['mon']=($fecha['mon']<10)?"0".$fecha['mon'] :$fecha['mon'];
       $Hoy=$fecha['mday']."/".$fecha['mon']."/".$fecha['year'];
       
       $accion= "Registro de Correspondencia el día: ";
       $accion.= $this->_diasemana($fecha).", ".$Hoy." a las: ".Date('h:iA');
       
       $movimiento=array(
                         'id_correspondencia' => $id_correspondencia,
                         'id_usuario' => $datos['id_usuario_asignado'],
                         'id_tipo_movimiento' => 1,
                         'movimiento'  =>$accion
                        );
       
       // PROCEDEMOS A INSTERTAR EL MOVIMIENTO INICIAL
       $insertado=$this->Crud->insertar_registro('cor_movimientos', $movimiento);
       if (!$insertado){die('Error');}
       
       // REGISTRAMOS EN BITACORA  
       $registro='id_correspondencia: '.$id_correspondencia;
       $registro.=', correspondencia: '.$datos['codigo_generado'];
       $registro.='. Registrado por: '.$this->session->userdata('usuario');
       $bitacora=array(
           'direccion_ip'   =>$this->session->userdata('ip_address'),
           'navegador'      =>$this->session->userdata('user_agent'),
           'id_usuario'     =>$this->session->userdata('id_usuario'),
           'controlador'    =>$this->uri->uri_string(),
           'tabla_afectada' =>'cor_correspondencia',
           'tipo_accion'    =>'INSERT',
           'registro'       =>$registro
       );
       $this->Crud->insertar_registro('z_bitacora', $bitacora); 
       
       $salida=array('codigo_generado'=>$datos['codigo_generado'],
                     'id_correspondencia'=>$id_correspondencia
                    );
//       $salida=array('codigo_generado'=>'S11-1201-0001',
//                     'id_correspondencia'=>$id_correspondencia
//                    );
       die(json_encode($salida));
    }
    
    // COMPROBAMOS QUE LA CORRESPONDENCIA NO ESTE REPETIDA; TOMANDO EN CUENTA LOS CAMPOS:
    // id_organismo, nro_comunicado y fecha_emision
    function buscar_correspondencia()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
           
       $id_organismo=$this->input->post('id_organismo');
       $nro_comunicado=mb_convert_case($this->input->post('nro_comunicado'),MB_CASE_UPPER);
       $fecha_emision=$this->input->post('fecha_emision');
       $entrante=$this->input->post('entrante');
       
       $consulta=$this->Correspondencia->buscar_repetidas($id_organismo, $nro_comunicado,
                                                          $fecha_emision, $entrante);
       if (!$consulta){die('Error');}
       
       $salida=array(    'cantidad' => $consulta->num_rows(),
                     'id_organismo' => $id_organismo,
                   'nro_comunicado' => $nro_comunicado,
                    'fecha_emision' => str_replace('/', '-', $fecha_emision),
                         'entrante' => ($entrante=='t')?'entrante':'saliente'
                    );
       die(json_encode($salida));
    }
    
    function adjuntar($id_correspondencia, $error='')
    {              
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
       // CONSULTAMOS SI EXISTE LA CORRESPONDENCIA
       $correspondencia=  $this->Correspondencia->get_correspondencia($id_correspondencia);
       if ($correspondencia->num_rows()!=1) exit('Error');
       $c=$correspondencia->row();
       
       $movimientos=$this->Correspondencia->get_movimientos($id_correspondencia);
       $m=($movimientos->num_rows()>0)?$movimientos->result():exit('Error: No Hay Movimientos');       
       
       // VERIFICAMOS PERMISO PARA ADJUNTAR
       if (!$this->_permiso_adjuntar($m[0]->id_usuario)) exit('Sin Acceso Directo al Script');
       
       $data=array();
       $data['contenido']='form_adjuntar';
       if (trim($c->ruta_archivo)=='')
       {          
           $data['imagen']=img(base_url().'imagenes/scanner.png');
           $data['titulo']='Adjuntar Archivo';          
           $data['error']=$error;
           $data['id_correspondencia'] = $id_correspondencia;
           $data['codigo_generado'] = $c->codigo_generado;                 
       }
       else
       {   
           $imagen=array(
                        'src' => (substr($c->ruta_archivo, -3)=='pdf')?
                                     (base_url().'imagenes/pdf.png"'):
                                     (base_url().substr($c->ruta_archivo, 2)),
                        'alt' => $c->codigo_generado,
                      'title' => $c->codigo_generado,
                      'style' => 'max-width:150px; max-height:150px',
           );
           
           $data['imagen']=img($imagen);
           $data['titulo']='Reemplazar Archivo';          
           $data['error']=$error;
           $data['id_correspondencia'] = $id_correspondencia;
           $data['codigo_generado'] = $c->codigo_generado;
       }
       $this->load->view('plantillas/plantilla_ingreso',$data);          
    }
    
    function subir_archivo($id_correspondencia)
    {
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
       // CONSULTAMOS SI EXISTE LA CORRESPONDENCIA
       $correspondencia=  $this->Correspondencia->get_correspondencia($id_correspondencia);
       if ($correspondencia->num_rows()!=1) exit('Error: No Hay Movimientos');
       $c=$correspondencia->row(); 

       $movimientos=$this->Correspondencia->get_movimientos($id_correspondencia);
       $m=($movimientos->num_rows()>0)?$movimientos->result():exit('Error');       
       
       // VERIFICAMOS PERMISO PARA ADJUNTAR
       if (!$this->_permiso_adjuntar($m[0]->id_usuario)) exit('Sin Acceso Directo al Script');       
       
       $codigo=  explode('-', $c->codigo_generado);        
       
       $year='20'.substr($codigo[1],0,2);
       $mes=substr($codigo[1],-2);
       
       $carpeta='./adjuntos/'.$year.'/'.$mes; // EJEMPLO './adjuntos/2013/01/
       
       $this->_crear_si_no_existe($carpeta);
       
       $config['upload_path'] = $carpeta;
       $config['allowed_types'] = 'pdf|jpg|jpeg|png|tif|bmp|gif';        
       // CREAMOS UN NOMBRE ALEATORIO PARA EL ADJUNTO
       $archivo_raw='';
       while ($archivo_raw=='')
       {
          $archivo_raw=random_string('alnum', 8); 
          if($this->_existe($carpeta, $archivo_raw, explode('|',$config['allowed_types'])))
          {
              $archivo_raw='';
          }
       }        
       $config['file_name']= $archivo_raw;
       $config['overwrite']= TRUE;
       $config['max_size']	= '0';
       $config['max_width']  = '0';
       $config['max_height']  = '0';
       
       $archivo=$config['upload_path'].$config['file_name'].'.*';        
       
       $this->load->library('upload', $config);

       if ( ! $this->upload->do_upload())  // AL HABER ERROR SUBIENDO EL ARCHIVO
       {
          $error = $this->_traducir($this->upload->display_errors());
          $this->adjuntar($id_correspondencia, $error);
       }
       else
       {
          // BORRAMOS EL ARCHIVO ANTERIOR          
          if (trim($c->ruta_archivo)!='') @unlink($c->ruta_archivo);
          
          // CODIGO PARA GUARDAR EL CAMPO DEL NOMBRE DEL ARCHIVO EN BASE DE DATOS
          $subido = $this->upload->data();
          $donde=array('id_correspondencia' => $id_correspondencia);
          $datos=array('ruta_archivo' => $carpeta.'/'.$subido['file_name']);
          $this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);
          
          $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 10,
                    'movimiento'         => 'Cambio de Archivo Adjunto'
                        );
          $this->Crud->insertar_registro('cor_movimientos', $movimiento);    
          
          if ($subido['image_width']>900) // VERIFICAMOS TAMAÑO MÁXIMO DE ANCHO
          {  // Ajuste de tamaño al archivo subido
             unset($config);
             $config['image_library'] = 'gd2';
             $config['source_image'] = $carpeta.'/'.$subido['file_name'];
             $config['create_thumb'] = FALSE;
             $config['maintain_ratio'] = TRUE;
             $config['width'] = 900;  
             $config['height'] = 6000; 
             $config['master_dim'] = 'width'; 
             $this->load->library('image_lib', $config);
             $this->image_lib->resize();   
          }           
          
          $data=array();           

          $data['contenido']='subida_exitosa';           
          $data['id_correspondencia'] = $id_correspondencia;
          $data['codigo_generado'] = $c->codigo_generado;
     
          $data['titulo']='Adjuntar Archivo';
          $this->load->view('plantillas/plantilla_ingreso',$data);           
       }
    }
        
    // BUSCA UN DIRECTORIO Y LO CREA SI NO EXISTE
    function _crear_si_no_existe($carpeta)
    {
       if (!file_exists($carpeta))
       {
          @mkdir($carpeta, 0777, true);
       }      
    }
    
    // DEVUELVE TRUE SI YA EXISTE EL NOMBRE DEL ARCHIVO, FALSE SI NO EXISTE
    function _existe($ruta, $nombre, $extension)
    {
        foreach ($extension as $valor)
        {
           if (file_exists($ruta.'/'.$nombre.'.'.$valor))
           {
              return true;
           }
        }
        return false;
    }
    
    // BUSCA UN ARCHIVO REPETIDO Y LO BORRA
    function _borrar_si_existe($ruta, $nombre, $extension)
    {
        foreach ($extension as $valor)
        {
           if (file_exists($ruta.'/'.$nombre.'.'.$valor))
           {
              // echo $ruta.$nombre.'.'.$valor.'<br/>';
               @unlink($ruta.'/'.$nombre.'.'.$valor);
           }
        }
    }
    
    function _permiso_adjuntar($id_usuario)
    {
       // VERIFICAMOS LOS PERMISOS DEL USUARIO PARA ADJUNTAR ARCHIVOS A LA CORRESPONDENCIA
       // EL MISMO DEBE CUMPLIR CON LAS SIGUIENTES CONDICIONES:
       // - Su Sesion->administrador=true
       // OR
       // - Su Sesion->id_nivel<6
       // OR
       // HABER CREADO LA CORRESPONDENCIA (Movimiento Registrador de la Correspondencia)
       // - Su Session->id_usuario = mov[0].id_usuario 
                    
       $permiso=false;  // Inicializamos la variable
       
       if ($this->session->userdata('administrador')) $permiso=true or $permiso;
       
       if ($this->session->userdata('id_nivel')<6) $permiso=true or $permiso;
      
       if ($this->session->userdata('id_usuario')==$id_usuario) $permiso=true or $permiso;
      
       return $permiso;       
    }
    
    // CAMBIAR LOS MENSAJES DE ERROR
    function _traducir($mensaje)
    {
        switch ($mensaje)
        {
            case "<p>The filetype you are attempting to upload is not allowed.</p>":
                $mensaje="<p>El tipo de Archivo que intenta adjuntar no posee un formato válido.</p>";
                break;
            case "<p>You did not select a file to upload.</p>":
                $mensaje="<p>No ha seleccionado ningún archivo.</p>";
                break;
            case "<p>The uploaded file exceeds the maximum allowed size in your PHP configuration file.</p>":
                $mensaje="<p>El archivo que intenta subir supera el límite de tamaño permitido.</p>";
                break;
            case "<p>The upload path does not appear to be valid.</p>":
                $mensaje="<p>La ruta del archivo no parece ser válida.</p>";
                break;
            default: $mensaje='<p>Error desconocido</p>';              
        }
        return $mensaje;
    }

    // GENERADOR DEL CODIGO DE LA CORRESPONDENCIA
    function _generar_codigo($entrante,$fecha_recepcion)
    {        
        // TRANSFORMAMOS LA FECHA DE RECEPCION
        $fecha['mday']= substr($fecha_recepcion,0,2);
        $fecha['mon']= substr($fecha_recepcion,3,2);
        $fecha['anho']= substr($fecha_recepcion,8,2);
        $fecha['year']= substr($fecha_recepcion,6,4);
        
        $dir=trim($this->session->userdata('cod_estruct'));
        $codigo_generado=(($entrante=='t')? 'E': 'S').$dir.'-';

        $cantidad= $this->Correspondencia->cantidad_registros($codigo_generado, $fecha['mon'] ,$fecha['year'], $this->session->userdata('id_estructura'));
      
        $cantidad++;
        if ($cantidad<10){$cantidad="000".$cantidad;}
        elseif ($cantidad>=10 && $cantidad<100){$cantidad="00".$cantidad;}
        elseif ($cantidad>=100 && $cantidad<1000){$cantidad="0".$cantidad;}
        
        $codigo_generado=(($entrante=='t')? 'E': 'S').$dir.'-'.$fecha['anho'].$fecha['mon'].'-'.$cantidad;
        
        return $codigo_generado;
    }
    
    function _diasemana($fecha) // FUNCION APROBADA Y VERIFICADA
    {   
	switch ($fecha['weekday'])
	{
		case "Sunday": $nombre_dia="Domingo"; break;
		case "Monday": $nombre_dia="Lunes"; break;
		case "Tuesday": $nombre_dia="Martes"; break;
		case "Wednesday": $nombre_dia="Mi&eacute;rcoles"; break;
		case "Thursday": $nombre_dia="Jueves"; break;
		case "Friday": $nombre_dia="Viernes"; break;
		case "Saturday": $nombre_dia="S&aacute;bado"; break;
	}
	return $nombre_dia;
    }
    
    function listar_registros($tabla)
    {   
      // Si la peticion vino por AJAX   
      if ($this->input->is_ajax_request()){$tabla=$this->input->post('tabla');}
      
      $registros=$this->Crud->listar_registros($tabla); 
      
      if ($this->input->is_ajax_request()) die($registros); // Si la peticion vino por AJAX
      else return $registros;
    }

    // Construye las opciones de Combo-Select a partir de una matriz
    function construye_opciones($opciones, $seleccionada=0)  
    {
      if ($this->input->is_ajax_request()) // Si la peticion vino por AJAX
      {
        $opciones=$this->input->post('opciones');
        $seleccionada=$this->input->post('seleccionada');
      }
      if (!isset($seleccionada)){$seleccionada=0;}
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
      
      if ($this->input->is_ajax_request()) die($combo); // Si la peticion vino por AJAX
      else return $combo;
    }
    
    function listar_organismos()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       
        $frase= $this->input->post('frase');
        $tipo_organismo= $this->input->post('tipo_organismo');
        
        die(json_encode($this->Organismos->listar_organismos($frase,$tipo_organismo)));       
    }
    
    function cuantos_organismos()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       
       $id_tipo_organismo= intval($this->input->post('id_tipo_organismo'));
       
       $entes=$this->Organismos->cuantos_organismos($id_tipo_organismo);
       $o=$entes->row();
       
       if ($entes->num_rows()==1)
       {         
         $salida= array('id_organismo'=> $o->id_organismo,
                        'organismo'   => $o->organismo);
       }
       else
       {
         $salida= array('id_organismo'=> 0,'organismo'=>'');
       }       
       die(json_encode($salida));
    }
}
?>