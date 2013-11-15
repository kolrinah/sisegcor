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
class Revisar extends CI_Controller {

    function __construct() 
    {
      parent::__construct();
      $this->load->helper('form');
      $this->load->model('Usuarios');
      $this->load->model('Organismos');
      $this->load->model('Estructura');
      $this->load->model('Correspondencia');
      $this->load->model('Crud');
      date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora
    }
    
    function index()
    {   
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    }
    
    function correspondencia($id_correspondencia)
    { 
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();} 
      
      $id_estructura=$this->session->userdata('id_estructura');
                  
      $correspondencia=$this->Correspondencia->get_correspondencia($id_correspondencia);
      $movimientos=$this->Correspondencia->get_movimientos($id_correspondencia);
      $ultimo_movimiento=$this->Correspondencia->get_ultimo_movimiento($id_correspondencia);
      $estructuras=$this->Estructura->obtener_estructuras_inferiores($id_estructura);

      if ($estructuras->num_rows()<1) return false;
      if ($correspondencia->num_rows()!=1) die('Correspondencia Nro.'.$id_correspondencia.' No Existe');
      $c=$correspondencia->row();
      $m=($movimientos->num_rows()>0)?$movimientos->result():0;
      $um=($ultimo_movimiento->num_rows()>0)?$ultimo_movimiento->row():exit('Error: No Hay Movimientos');
      $e=$estructuras->result();
      
      // VERIFICAMOS LOS PERMISOS DEL USUARIO PARA VER LA CORRESPONDENCIA
      // EL MISMO DEBE CUMPLIR CON LAS SIGUIENTES CONDICIONES:
      // - Su Session->id_usuario = cor.id_usuario_asignado
      // OR
      // - Su Sesion->administrador=true
      // OR
      // - Su Session->id_estructura = cor.id_estructura_owner
      // OR
      // - cor.id_estructura_owner Pertenecer a estructuras inferiores a su Session->id_estructura
      // OR
      // - mov.id_usuario = Session->id_usuario
      // 
      // POR OTRO LADO, PARA ESCRIBIR EN LA CORRESPONDENCIA ES NECESARIO:
      //- Su Session->id_usuario = cor.id_usuario_asignado
      // OR
      // - Su Sesion->administrador=true
      // OR
      // - UltimoMovimiento.id_usuario = Sesion->id_usuario
      //
      // PERMISO DE BORRAR CORRESPONDENCIA. LAS CONDICIONES SON:
      // - Su Sesion->administrador=true
      // OR (
      // - Sesion->id_usuario = mov[0]->id_usuario
      // AND
      // - Sesion->id_usuario = cor.id_usuario_asignado
      // AND
      // - NumMov < 6 
      //
      
      // INICIALIZAMOS PERMISOS
      $permisoVer=false;  
      $permisoEscribir=false; 
      $permisoBorrar=false;
      
      if ($this->session->userdata('administrador'))
      {
         $permisoVer=true;  
         $permisoEscribir=true; 
         $permisoBorrar=true;
      }
      
      if ($this->session->userdata('id_usuario')==$c->id_usuario_asignado &&
          $this->session->userdata('id_usuario')==$m['0']->id_usuario &&
          $movimientos->num_rows()<6 ) $permisoBorrar=true;
      
      if ($this->session->userdata('id_usuario')==$c->id_usuario_asignado) {$permisoVer=true; $permisoEscribir=true;}
      
      if ($um->id_usuario==$this->session->userdata('id_usuario')) $permisoEscribir=true;
      
      if ($this->session->userdata('id_nivel')<6 &&
          $this->session->userdata('id_estructura')==$c->id_estructura_owner) $permisoEscribir=true;
      
      if ($this->session->userdata('id_nivel')==7) $permisoVer=true;
          
      foreach ($e as $fila) //Verificamos que la correspondencia este entre las estructuras inferiores
      {
          if ($fila->id_estructura==$c->id_estructura_owner) {$permisoVer=true;}
      }      
      unset($fila);
      
      foreach ($m as $fila) //Verificamos que el usuario haya ejecutado algun movimiento en la correspondencia
      {
          if ($this->session->userdata('id_usuario')==$fila->id_usuario) $permisoVer=true;
      }
      unset($fila);
      
      if (!$permisoVer) exit('No Posee los permisos necesarios para poder ver esta correspondencia.');
      
      $escribir= ($permisoEscribir)?'':' disabled="disabled" ';
      
      $data=array();     
      $data['c']=$c;
      $data['permiso_adjuntar']= $this->_permiso_adjuntar($m[0]->id_usuario);
    
      $data['permisoEscribir']=$permisoEscribir;
      $data['permisoBorrar']=$permisoBorrar;
      
      $data['id_correspondencia']= array(
                  'type' => 'hidden',
                  'name' => 'id_correspondencia',
                    'id' => 'id_correspondencia',
                 'value' => $c->id_correspondencia); 
      
      // OBSERVACIONES
      $data['observaciones']= array(
                        'name' => 'observaciones',
                          'id' => 'observaciones',
                        'style'=> 'width:98%; height:70px',
                       'class' => 'CampoFicha',
                     'readonly'=> 'readonly',
                      //'rows' => '3',
                       'value' => trim($c->observaciones),
                       'title' => 'Observaciones y Análisis');      
      
      // Caja Combo para Categorías de Correspondencia
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->Crud->listar_registros('cor_clasificaciones');
      if ($tipos->num_rows()>0)
      {  
         $a=array();$b=array(); 
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_clasificacion);
           array_push($b,$fila->clasificacion);
         }         
         $opciones=array_combine($a,$b);
         asort($opciones);
         $opciones=$this->_construye_opciones($opciones, $c->id_clasificacion);
      }
      
      $data['clasificacion']= '<select '.$escribir.'id="id_clasificacion" class="CampoFicha" title="Clasificación de Correspondencia">';
      $data['clasificacion'].=$opciones;
      $data['clasificacion'].='</select>';
      
      // Caja Combo para Estatus de Correspondencia
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      $tipos=$this->listar_registros('cor_estatus');
      if ($tipos->num_rows()>0)
      {  
         $a=array();$b=array(); 
         foreach ($tipos->result() as $fila)
         {
           array_push($a,$fila->id_estatus);
           array_push($b,$fila->estatus);
         }         
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->_construye_opciones($opciones, $c->id_estatus);
      }
      
      $data['estatus']='<select '.$escribir.'id="id_estatus" class="CampoFicha" title="Estatus de Correspondencia">';
      $data['estatus'].=$opciones;
      $data['estatus'].='</select>';
      
      // LUGAR DE ARCHIVO
      $data['lugar_archivo']= array(
                        'name' => 'lugar_archivo',
                          'id' => 'lugar_archivo',
                       'class' => ($permisoEscribir)?'':'CampoFicha',
                  'max-lenght' => '50',
                        'size' => '25',
                       'value' => trim($c->lugar_archivo),
                       'title' => $c->estructura);
   
      // COMBO MOVIMIENTOS POSIBLES
      $opciones='<option value="0">ERROR AL CARGAR LA DATA</option>';
      
      $data['acciones']= '<select '.$escribir.'id="id_tipo_movimiento" class="CampoFicha" title="Seleccione la Acción a Tomar">';      

      $data['acciones'].= $this->_movimientos_posibles($id_correspondencia, $um->id_tipo_movimiento);
      $data['acciones'].='</select>';      
      
      // CARGAMOS LOS MOVIMIENTOS DE LA CORRESPONDENCIA
      
      $data['movimientos']=$this->_movimientos($id_correspondencia);  
      
      // BOTON DE SALIR
      $salir=array(
               'src' => base_url().'imagenes/exit.png',
               'alt' => 'Salir',
             'title' => 'Cerrar',
             'style' => 'cursor:pointer; max-width:32px; max-height:32px',
           'onclick' => 'javascript:window.close();'
                   );
            
      $data['botonsalir']=img($salir);
      
      // BOTON BORRAR
      $borrar=array(
               'src' => base_url().'imagenes/borrar.png',
               'alt' => 'Borrar',
             'title' => 'Borrar Correspondencia',
             'style' => 'cursor:pointer; max-width:32px; max-height:32px',
           'onclick' => 'javascript:BorrarCorrespondencia('.$c->id_correspondencia.');'
                   );
            
      $data['botonborrar']=img($borrar);
      
      // BOTON EJECUTAR            
      $boton='<center><div class="BotonIco" onclick="javascript:EjecutarMovimiento();" title="Ejecutar Acción">';
      $boton.='<img src="'.base_url().'imagenes/agregado.png"/>&nbsp;&nbsp;';   
      $boton.='Ejecutar';
      $boton.= '</div></center>';
      
      $data['botonejecutar']=$boton;
      
      $data['contenido']='form_revisar';
      $data['titulo']='Revisión de Correspondencia';
      
      $data['script']='<!--Incluimos Funciones JS de uso común-->'."\n";
      $data['script'].="\t".'<script type="text/javascript" charset="utf-8" src="'.base_url().'js/comunes.js"></script>'."\n";
      
      $data['script'].='<!-- Cargamos Nuestro JS -->'."\n";
      $data['script'].="\t".'<script type="text/javascript" src="'.base_url().'js/revisar.js"></script>'."\n";
            
      $this->load->view('plantillas/plantilla_ingreso',$data);
    }    
   
    // OBTIENE LOS MOVIMIENTOS POSIBLES SEGUN EL ULTIMO MOVIMIENTO
    function _movimientos_posibles($id_correspondencia, $ultimo_movimiento)
    {         
      $correspondencia=$this->Correspondencia->get_correspondencia($id_correspondencia);
      if ($correspondencia->num_rows()!=1) die('Error');
      $c=$correspondencia->row();        
      $c1= $this->session->userdata('id_usuario')==$c->id_usuario_asignado; // La Cor está asignada al usuario
      $c2= $this->session->userdata('id_nivel')<6 || // El nivel del usuario es Distribuidos, jefe o Administrador
           $this->session->userdata('administrador');
      $c3= $this->session->userdata('id_estructura')==$c->id_estructura_owner; // La Cor perteneca a la Estructura
      $entrante= ($c->entrante=='t')?true:false;
      
      // Caja Combo para Movimientos Posibles  
      $opciones='<option value="0">Sin Movimientos Posibles</option>';                  
      $moves=$this->Correspondencia->get_movimientos_posibles($ultimo_movimiento, $c1, $c2, $c3, $entrante);
      if ($moves->num_rows()>0)
      {  
         $a=array('0');$b=array('[Seleccione]'); 
         foreach ($moves->result() as $fila)
         {
           array_push($a,$fila->id_movimiento_posible);
           array_push($b,$fila->tipo_movimiento);           
         } 
         unset($fila);
         $opciones=array_combine($a,$b);
         ksort($opciones);
         $opciones=$this->_construye_opciones($opciones);
      } 
      return $opciones;
    }
    
    function _movimientos($id_correspondencia)
    {/*
      if ($this->input->is_ajax_request()) // Si la peticion vino por AJAX
      {
        $id_correspondencia=$this->input->post('id_correspondencia'); 
      } */     
      date_default_timezone_set('America/Caracas');
      
      $movimientos=$this->Correspondencia->get_movimientos($id_correspondencia);
      $m=($movimientos->num_rows()>0)?$movimientos->result():0;
      if ($m==0) $tabla='<center><h3>No Posee Movimientos</h3></center>';
      else
      {
        $tabla='<table class="Movimientos">';
        $tabla.='<thead>';
        $tabla.='<tr>';
        $tabla.='<th width="40px">';        
        $tabla.='</th>';
        $tabla.='<th style="text-align:center; width:160px;">';
        $tabla.='<strong>Fecha - Hora</strong>';
        $tabla.='</th>';
        $tabla.='<th>';
        $tabla.='<strong>Acciones</strong>';
        $tabla.='</th>';        
        $tabla.='<th style="text-align:left; width:160px; padding-left:10px">';
        $tabla.='<strong>Ejecutante</strong>';
        $tabla.='</th>';        
        $tabla.='</tr>'; 
        $tabla.='</thead>';
        
        foreach ($m as $r)
        {
           $tabla.='<tr>';        
           $tabla.='<td style="text-align:center;">';           
           $tabla.='<img src="'.base_url().$r->icon_mov.'" title="'.$r->tipo_movimiento.'"/>';           
           $tabla.='</td>';
           $tabla.='<td style="text-align:center;">';             
           $tabla.=date("d/m/Y - h:ia",strtotime($r->fecha_movimiento));
           $tabla.='</td>';        
           $tabla.='<td>';
           $tabla.=$r->movimiento;
           $tabla.='</td>';
           $tabla.='<td>';
           $tabla.=mb_convert_case($r->nombre.' '.$r->apellido,MB_CASE_TITLE);
           $tabla.='</td>';        
           $tabla.='</tr>';            
        }
        unset($r);
        $tabla.='</table>';
      }            
     // if ($this->input->is_ajax_request()) die($tabla); // Si la peticion vino por AJAX
     // else
      return $tabla;
    }
    
    function cambiar_estatus()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $id_estatus=intval($this->input->post('id_estatus'));
              
       $donde=array(
                 'id_correspondencia' => $id_correspondencia
                   );
       $datos=array(                
               'id_estatus' => $id_estatus
               );
       $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);        
       if (!$actualizado)die('Error');
       
       // OBTENEMOS DATOS DEL ESTATUS NUEVO
       $donde=array('id_estatus'=>$id_estatus);
       $estatus=$this->Crud->listar_registros('cor_estatus',$donde);
       if ($estatus->num_rows()<1)die('Error');
       $e=$estatus->row();
       
       // REGISTRAMOS EL MOVIMIENTO COMO TIPO 11
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 11,
                    'movimiento'         => 'Cambio de Estatus a: "'.$e->estatus.'"'
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
       
       $salida=array('estatus'     => $e->estatus,
                     'id_estatus'  => $e->id_estatus,
                     'movimientos' => $this->_movimientos($id_correspondencia)
                    );
             
       die(json_encode($salida));
    }
    
    function cambiar_clasificacion()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $id_clasificacion=intval($this->input->post('id_clasificacion'));
              
       $donde=array(
                 'id_correspondencia' => $id_correspondencia
                   );
       $datos=array(                
                   'id_clasificacion' => $id_clasificacion
               );
       $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);        
       if (!$actualizado)die('Error');
       
       // OBTENEMOS LA NUEVA CLASIFICACION
       $donde=array('id_clasificacion'=>$id_clasificacion);
       $clasificacion=$this->Crud->listar_registros('cor_clasificaciones',$donde);
       if ($clasificacion->num_rows()<1)die('Error');
       $e=$clasificacion->row();
       
       // REGISTRAMOS EL MOVIMIENTO COMO TIPO 11
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 11,
                    'movimiento'         => 'Cambio de clasificacion a: "'.$e->clasificacion.'"'
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
       
       $salida=array('clasificacion'     => $e->clasificacion,
                     'id_clasificacion'  => $e->id_clasificacion,
                     'movimientos'       => $this->_movimientos($id_correspondencia)
                    );
             
       die(json_encode($salida));
    } 
    
    function cambiar_ubicacion()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $lugar_archivo=  mb_convert_case($this->input->post('lugar_archivo'),MB_CASE_TITLE);
              
       $donde=array(
                 'id_correspondencia' => $id_correspondencia
                   );
       $datos=array(                
                   'lugar_archivo' => $lugar_archivo
               );
       $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);        
       if (!$actualizado)die('Error');       
              
       // REGISTRAMOS EL MOVIMIENTO COMO TIPO 11
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 11,
                    'movimiento'         => 'Cambio de Ubicación Física a: "'.$lugar_archivo.'"'
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
       
       $salida=array('lugar_archivo'     => $lugar_archivo,                     
                     'movimientos'       => $this->_movimientos($id_correspondencia)
                    );
             
       die(json_encode($salida));
    }       

    function movimiento_tipo_2() // DISTRIBUCIÓN A UNIDAD
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $id_estructura_owner=intval($this->input->post('id_estructura_owner'));

       // VERIFICAMOS SI LA CORRESPONDENCIA ES SALIENTE
       $corr=$this->Crud->listar_registros('cor_correspondencias',array('id_correspondencia'=>$id_correspondencia));
       $c=($corr->num_rows>0)?$corr->row():die('Error');
       if ($c->entrante=='f')
       {
          $donde=array(
                   'id_correspondencia' => $id_correspondencia
                      );
          $datos=array(                
                   'id_estructura_owner' => $id_estructura_owner,
                   'entrante' => 't'
                     );
         $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);         
       }
       else
       {
         $donde=array(
                   'id_correspondencia' => $id_correspondencia
                     );
         $datos=array(                
                     'id_estructura_owner' => $id_estructura_owner
                     );
         $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);
       }
       if (!$actualizado)die('Error');
       
       $estructura=$this->Crud->listar_registros('e_estructura',array('id_estructura'=>$id_estructura_owner));
       $e=($estructura->num_rows>0)?$estructura->row():die('Error');
                     
       // REGISTRAMOS EL MOVIMIENTO COMO TIPO 2
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 2,
                    'movimiento'         => 'Distribución de Correspondencia a la Unidad: '.
                            $e->codigo_estructura.' - '.mb_convert_case($e->estructura,MB_CASE_TITLE)
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
              
       // Actualizar el Combo de Movimientos Posibles     
       $salida=array(    
                        'movimientos' => $this->_movimientos($id_correspondencia),
               'movimientos_posibles' => $this->_movimientos_posibles($id_correspondencia, 2)
                    );
             
       die(json_encode($salida));
    }   
    
    function movimiento_tipo_3() // RECEPCIÓN EN UNIDAD ADMINISTRATIVA
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $id_usuario=$this->session->userdata('id_usuario');
              
       $donde=array(
                 'id_correspondencia' => $id_correspondencia
                   );
       $datos=array(                
                   'id_usuario_asignado' => $id_usuario
               );
       $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);        
       if (!$actualizado)die('Error');
       
       $usuario=$this->Crud->listar_registros('usr_usuarios',array('id_usuario'=>$id_usuario));
       $u=($usuario->num_rows>0)?$usuario->row():die('Error');
       
       $u=mb_convert_case(($u->nombre.' '.$u->apellido),MB_CASE_TITLE);
              
       // REGISTRAMOS EL MOVIMIENTO COMO TIPO 3
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 3,
                    'movimiento'         => 'Correspondencia Recibida en la Unidad'
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
              
       // Actualizar el Combo de Movimientos Posibles     
       $salida=array(
                            'usuario' => $u,
                        'movimientos' => $this->_movimientos($id_correspondencia),
               'movimientos_posibles' => $this->_movimientos_posibles($id_correspondencia, 3)
                    );
             
       die(json_encode($salida));
    }
    
    function movimiento_tipo_4() // ASIGNACION DE USUARIO
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $id_usuario_asignado=intval($this->input->post('id_usuario_asignado'));
                     
       $donde=array(
                 'id_correspondencia' => $id_correspondencia
                   );
       $datos=array(                
                   'id_usuario_asignado' => $id_usuario_asignado
                   );
       $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);        
       if (!$actualizado)die('Error');  
       
       $usuario=$this->Crud->listar_registros('usr_usuarios',array('id_usuario'=>$id_usuario_asignado));
       $u=($usuario->num_rows>0)?$usuario->row():die('Error');
       
       $u=mb_convert_case(($u->nombre.' '.$u->apellido),MB_CASE_TITLE);
              
       // REGISTRAMOS EL MOVIMIENTO COMO TIPO 4
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 4,
                    'movimiento'         => 'Asignación de Correspondencia a: '.$u                                    
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
              
       // Actualizar el Combo de Movimientos Posibles     
       $salida=array(    
                            'usuario' => $u,
                        'movimientos' => $this->_movimientos($id_correspondencia),
               'movimientos_posibles' => $this->_movimientos_posibles($id_correspondencia, 4)
                    );
             
       die(json_encode($salida));
    }     
    
    function movimiento_tipo_5() // RECEPCIÓN POR USUARIO
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $id_usuario=$this->session->userdata('id_usuario');
              
       $donde=array(
                 'id_correspondencia' => $id_correspondencia
                   );
       $datos=array(                
                   'id_usuario_asignado' => $id_usuario
               );
       $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);        
       if (!$actualizado)die('Error');       
              
       // REGISTRAMOS EL MOVIMIENTO COMO TIPO 5
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 5,
                    'movimiento'         => 'Correspondencia Recibida por el Usuario Asignado'
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
              
       // Actualizar el Combo de Movimientos Posibles     
       $salida=array(                
                        'movimientos' => $this->_movimientos($id_correspondencia),
               'movimientos_posibles' => $this->_movimientos_posibles($id_correspondencia, 5)
                    );
             
       die(json_encode($salida));
    }    
    
    function movimiento_tipo_6() // DEVOLUCIÓN DE CORRESPONDENCIA
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $id_estructura=$this->session->userdata('id_estructura');
       $nombre=$this->session->userdata('cod_estruct').' - '.
                mb_convert_case($this->session->userdata('nombre_estruct'),MB_CASE_TITLE);
       
       // DETERMINAMOS LA ESTRUCTURA DEL PRIMER MOVIMIENTO
       // PARA QUE SEA LA ESTRUCTURA TOPE DE DEVOLUCIÓN
       $movimientos=$this->Correspondencia->get_movimientos($id_correspondencia);
       $m=($movimientos->num_rows()>0)?$movimientos->result():die('Error');
       
       $usuario=$this->Usuarios->obtener_usuario($m[0]->id_usuario);
       $id_estructura_tope=$usuario->id_estructura;
       
       if($id_estructura==$id_estructura_tope && 
                          $this->session->userdata('id_nivel')!=6) die('Error');
       
       // SI EL USUARIO ES FUNCIONARIO, PODRA DEVOLVER LA CORRESPONDENCIA A SU PROPIA UNIDAD       
       // SI EL USUARIO ES DISTRIBUIDOR (NIVEL<6) PODRA DEVOLVER A LA UNIDAD SUPERIOR       
       if ($this->session->userdata('id_nivel')!=6)
       { // OBTENEMOS LA ESTRUCTURA SUPERIOR
         $estructura=$this->Crud->listar_registros('e_estructura',array('id_estructura'=>$id_estructura));
         $e=($estructura->num_rows>0)?$estructura->row():die('Error');
         $id_estructura=$e->id_superior;
         
         $estructura=$this->Crud->listar_registros('e_estructura',array('id_estructura'=>$id_estructura));
         $e=($estructura->num_rows>0)?$estructura->row():die('Error');
         $nombre=$e->codigo_estructura.' - '.mb_convert_case($e->estructura,MB_CASE_TITLE);           
       }
       
       $donde=array(
                 'id_correspondencia' => $id_correspondencia
                   );
       $datos=array(                
                   'id_estructura_owner' => $id_estructura
                   );
       $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);        
       if (!$actualizado)die('Error');  
                            
       // REGISTRAMOS EL MOVIMIENTO COMO TIPO 6
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 6,
                    'movimiento'         => 'Correspondencia Devuelta a Unidad Superior: '.
                                             $nombre
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
              
       // Actualizar el Combo de Movimientos Posibles     
       $salida=array(    
                        'movimientos' => $this->_movimientos($id_correspondencia),
               'movimientos_posibles' => $this->_movimientos_posibles($id_correspondencia, 6)
                    );
             
       die(json_encode($salida));
    } 
    
    function movimiento_tipo_7() // ENVÍO A SUPERIOR
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $id_estructura=$this->session->userdata('id_estructura');
          
       // OBTENEMOS LA ESTRUCTURA SUPERIOR
       $estructura=$this->Crud->listar_registros('e_estructura',array('id_estructura'=>$id_estructura));
       $e=($estructura->num_rows>0)?$estructura->row():die('Error');
              
       $donde=array(
                 'id_correspondencia' => $id_correspondencia
                   );
       $datos=array(                
                   'id_estructura_owner' => $e->id_superior
                   );
       $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);        
       if (!$actualizado)die('Error');  
       
       $estructura=$this->Crud->listar_registros('e_estructura',array('id_estructura'=>$e->id_superior));
       $e=($estructura->num_rows>0)?$estructura->row():die('Error');
                     
       // REGISTRAMOS EL MOVIMIENTO COMO TIPO 7
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 7,
                    'movimiento'         => 'Correspondencia Enviada a Unidad Superior: '.
                            $e->codigo_estructura.' - '.mb_convert_case($e->estructura,MB_CASE_TITLE)
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
              
       // Actualizar el Combo de Movimientos Posibles     
       $salida=array(    
                        'movimientos' => $this->_movimientos($id_correspondencia),
               'movimientos_posibles' => $this->_movimientos_posibles($id_correspondencia, 7)
                    );
             
       die(json_encode($salida));
    }
        
    function movimiento_tipo_8() // RECUPERAR CORRESPONDENCIA
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $id_usuario=$this->session->userdata('id_usuario');
       $id_estructura=$this->session->userdata('id_estructura');
              
       $donde=array(
                 'id_correspondencia' => $id_correspondencia
                   );
       $datos=array(                
                   'id_usuario_asignado' => $id_usuario,
                   'id_estructura_owner' => $id_estructura 
               );
       $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);        
       if (!$actualizado)die('Error');
       
       $usuario=$this->Crud->listar_registros('usr_usuarios',array('id_usuario'=>$id_usuario));
       $u=($usuario->num_rows>0)?$usuario->row():die('Error');
       
       $u=mb_convert_case(($u->nombre.' '.$u->apellido),MB_CASE_TITLE);
              
       // REGISTRAMOS EL MOVIMIENTO COMO TIPO 8
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 8,
                    'movimiento'         => 'Correspondencia Recuperada por el Usuario'
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
              
       // Actualizar el Combo de Movimientos Posibles     
       $salida=array(
                            'usuario' => $u,
                        'movimientos' => $this->_movimientos($id_correspondencia),
               'movimientos_posibles' => $this->_movimientos_posibles($id_correspondencia, 3)
                    );
             
       die(json_encode($salida));
    }    

    function movimiento_tipo_9() // EDICIÓN DE OBSERVACIONES
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $observaciones=  trim($this->input->post('observaciones'));
       
       $donde=array(
                 'id_correspondencia' => $id_correspondencia
                   );
       $datos=array(                
                   'observaciones' => $observaciones
                   );
       $actualizado=$this->Crud->actualizar_registro('cor_correspondencias', $datos, $donde);        
       if (!$actualizado)die('Error');  
       
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 9,
                    'movimiento'         => 'Edición de Observaciones'
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
       
       $salida=array(                
                     'movimientos' => $this->_movimientos($id_correspondencia)
                    );
             
       die(json_encode($salida));
    }    
    
    function movimiento_tipo_11() // OTRAS ACCIONES
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       // VERIFICAMOS SI EXISTE SESION ABIERTA    
       if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); die('Error');}
       
       $id_correspondencia=intval($this->input->post('id_correspondencia'));
       $accion=  trim($this->input->post('accion'));
       
       $movimiento=array(
                    'id_correspondencia' => $id_correspondencia,
                    'id_usuario'         => $this->session->userdata('id_usuario'),
                    'id_tipo_movimiento' => 11,
                    'movimiento'         => $accion
                        );
       $this->Crud->insertar_registro('cor_movimientos', $movimiento);              
       
       $salida=array(                
                     'movimientos' => $this->_movimientos($id_correspondencia)
                    );
             
       die(json_encode($salida));
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
       
       if ($this->session->userdata('administrador')) $permiso=true;
       
       if ($this->session->userdata('id_nivel')<6) $permiso=true;
      
       if ($this->session->userdata('id_usuario')==$id_usuario) $permiso=true;
      
       return $permiso;       
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
    function _construye_opciones($opciones=0, $seleccionada=0)  
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
    
    // BUSQUEDA DE UNIDADES INFERIORES INMEDIATAS y Paralelas
    function listar_unidades_distribucion()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       
        $frase= $this->input->post('frase');
        $id_estructura= $this->session->userdata('id_estructura');
        
        $e=$this->Estructura->listar_unidades_distribucion($id_estructura,$frase);
        
        if($e->num_rows()>0)
        {
           die(json_encode($e->result_array()));
        }
        else {die(json_encode(array('No hubo coincidencias')));}                
    }
    
    // LISTAR ANALISTAS
    function listar_analistas()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado');
       
        $frase= $this->input->post('frase');
        $id_estructura= $this->session->userdata('id_estructura');
        $id_usuario= $this->session->userdata('id_usuario');
        
        die(json_encode($this->Usuarios->listar_analistas($frase,$id_estructura,$id_usuario)));       
    }
    
    function borrar_correspondencia()
    {
       if (!$this->input->is_ajax_request()) die('Acceso Denegado'); // Si la peticion no vino por AJAX
       
       $id_correspondencia= intval($this->input->post('id_correspondencia'));
       
       // BUSCAMOS LA CORRESPONDENCIA
       $correspondencia=$this->Correspondencia->get_correspondencia($id_correspondencia);
       if ($correspondencia->num_rows()!=1)die('error');
       $c=$correspondencia->row();
       
       // PROCEDEMOS A VERIFICAR SI POSEE ADJUNTOS
       if (trim($c->ruta_archivo)!='') $this->_borrar_si_existe($c->ruta_archivo);
       
       // ELIMINAMOS EL REGISTRO Y REPORTAMOS EN BITACORA
        $corr=array(
            'id_correspondencia' => $id_correspondencia
                       );        
        
        $borrado=$this->Crud->eliminar_registro('cor_correspondencias', $corr);
        if (!$borrado){die('Error');}
        else
        {
           $registro='Correspondencia con id_correspondencia: '.$id_correspondencia;           
           $registro.='. Eliminada por: '.$this->session->userdata('usuario');
           $bitacora=array(
               'direccion_ip'   =>$this->session->userdata('ip_address'),
               'navegador'      =>$this->session->userdata('user_agent'),
               'id_usuario'     =>$this->session->userdata('id_usuario'),
               'controlador'    =>$this->uri->uri_string(),
               'tabla_afectada' =>'cor_correspondencias',
               'tipo_accion'    =>'DELETE',
               'registro'       =>$registro
           );
           $this->Crud->insertar_registro('z_bitacora', $bitacora); 
        }              
       die(json_encode(array('Correspondencia Eliminada')));
    }
 
    function _borrar_si_existe($ruta)
    {
       if (file_exists($ruta)) @unlink($ruta);
       return true;
    }
}
?>