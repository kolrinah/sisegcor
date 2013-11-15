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
class Acceso extends CI_Controller {

    function __construct() 
    {
        parent::__construct();       
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->model('Usuarios');
    }

    function index()    
    {
        if ($this->session->userdata('aprobado')){redirect('bandeja/asignadas','refresh');exit(0);}
        $data=array();        
        $data['titulo']='Acceso a Sisegcor';
        $data['contenido']='ingreso';
        $data['usuario']= array(
                                'name' => 'usuario',
                                'id' => 'usuario',
                                'value' => set_value('usuario'),
                                'maxlength' => '150',
                                'tabindex' => '1',
                                'title'=>'Introduzca su Usuario',
                                'size' => '40');
        $data['clave'] = array(
                                'name' => 'clave',
                                'id' => 'clave',
                                'maxlength' => '150',
                                'tabindex' => '2',
                                'title'=>'Introduzca su Clave',
                                'size' => '40');
        $data['script']='<script type="text/javascript">';
        $data['script'].='window.onload=function(){$("#usuario").focus(); ';
        $data['script'].="$('#clave').bind('keyup', function (e) {
                            var key = e.keyCode || e.which;
                           if (key === 13) {
                           $('form').submit(); };
                           }); } ";
        $data['script'].='</script>';
        
        $this->load->view('plantillas/plantilla_ingreso', $data); 
    }
    
    function verificar_ingreso()
    {   
        $this->form_validation->set_rules('usuario', 'Usuario', 'trim|required|strtolower|xss_clean|htmlspecialchars');
        $this->form_validation->set_rules('clave', 'Contraseña', 'required|md5');
        $this->form_validation->set_message('required', 'Campo <i>"%s"</i> Inválido');
        if ($this->form_validation->run() == FALSE)
	{                               
           $this->session->set_userdata('errores','Introduzca los Datos correctamente');
           redirect('acceso','refresh');
           exit(0);
	}else
	{
            $usuario=trim($this->input->post('usuario'));
            $clave=$this->input->post('clave');
            $consulta=$this->Usuarios->verificar_acceso(htmlspecialchars($usuario, ENT_QUOTES),$clave);           
            if ($consulta)
            { 
              // DEFINIMOS EL RANGO DE FECHAS INICIAL PARA FILTROS DE BÚSQUEDA
               date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora              
               $tiempo=  getdate(time()-2592000); // hace 30 días
               $fecha_inicial= $tiempo['mday']<10? '0'.$tiempo['mday']:$tiempo['mday'];
               $fecha_inicial.='/';
               $fecha_inicial.=$tiempo['mon']<10?'0'.$tiempo['mon']:$tiempo['mon'];
               $fecha_inicial.='/';
               $fecha_inicial.=$tiempo['year'];  
               $tiempo=  getdate(time()); // Fecha del día de hoy
               $fecha_final= $tiempo['mday']<10? '0'.$tiempo['mday']:$tiempo['mday'];
               $fecha_final.='/';
               $fecha_final.=$tiempo['mon']<10?'0'.$tiempo['mon']:$tiempo['mon'];
               $fecha_final.='/';
               $fecha_final.=$tiempo['year'];  
               
              // EN CASO DE USAR ARRAY A TRAVES DEL METODO row_array()                    
              $sesion = array(
                    'aprobado'      => TRUE,      
                    'id_usuario'    => $consulta->id_usuario,
                    'id_estructura' => $consulta->id_estructura,
               'id_tipo_estructura' => $consulta->id_tipo_estructura,
                    'id_nivel'      => $consulta->id_nivel,
                    'correo'        => $consulta->correo,
                    'usuario'       => $consulta->nombre." ".$consulta->apellido,
                    'administrador' => ($consulta->administrador=='t')?TRUE:FALSE,
                    'cod_estruct'   => ($consulta->codigo_estructura),
                    'nombre_estruct'=> ($consulta->estructura),
                    'entrante'      => TRUE, // Indica que la correspondencia es Entrante,
                    'fecha_inicial' => $fecha_inicial,
                    'fecha_final'   => $fecha_final,
                    'errores'       => ''
                   );
              
              $this->session->set_userdata($sesion);
              session_start();
              foreach ($this->session->all_userdata() as $key=>$valor)
              {
                $_SESSION[$key]=$valor;
              }             
              // VERFICA SI LA CLAVE ES LA ORIGINAL PARA OBLIGAR A CAMBIARLA
              if ($clave=='202cb962ac59075b964b07152d234b70') // Clave Inicial: "123"
              {
                  redirect('acceso/cambiar_clave','refresh');  
                  exit(0);
              }
              redirect('bandeja/asignadas','refresh');  
              exit(0);
            }
            else
            {
              $this->session->set_userdata('errores','Usuario o Contraseña Incorrectos');
              redirect('acceso','refresh');
              exit(0);
            }
        }       
    }    

    function salir()
    {
       session_start();
       if(isset($_SESSION['aprobado'])){session_destroy();}
       
       $this->session->sess_destroy();
       redirect('acceso','refresh');
       exit(0);
    }
    
    function cambiar_clave()
    {        
        $data=array();        
        $data['titulo']='Cambiar clave personal';
        $data['contenido']='cambiar_clave';

        $data['actual'] = array(
                                'name' => 'actual',
                                'id' => 'actual',
                                'maxlength' => '150',
                                'tabindex' => '1',
                                'title'=>'Introduzca su Contraseña Actual',
                                'size' => '40');
        $data['nueva'] = array(
                                'name' => 'nueva',
                                'id' => 'nueva',
                                'maxlength' => '150',
                                'tabindex' => '2',
                                'title'=>'Introduzca su Contraseña Nueva',
                                'size' => '40');
        $data['nueva2'] = array(
                                'name' => 'nueva2',
                                'id' => 'nueva2',
                                'maxlength' => '150',
                                'tabindex' => '3',
                                'title'=>'Repita su Contraseña Nueva',
                                'size' => '40');
        $data['script']='<script type="text/javascript">';
        $data['script'].='window.onload=function(){$("#actual").focus(); ';
        $data['script'].="$('#nueva2').bind('keyup', function (e) {
                            var key = e.keyCode || e.which;
                           if (key === 13) {
                           $('form').submit(); };
                           }); } ";
        $data['script'].='</script>';
                
        $this->load->view('plantillas/plantilla_ingreso', $data); 
    }
    
    function verificar_clave()
    {        
        $this->form_validation->set_rules('actual', 'Contraseña Actual', 'required|md5');
        $this->form_validation->set_rules('nueva', 'Contraseña Nueva', 'required|matches[nueva2]|md5');
        $this->form_validation->set_rules('nueva2', 'Confirmar Contraseña', 'required|md5');
        $this->form_validation->set_message('required', 'Campo <i>"%s"</i> Inválido');
        $this->form_validation->set_message('matches', 'Ambas contraseñas deben coincidir');
        
        if ($this->form_validation->run() == FALSE)
	{             
           $this->session->set_userdata('errores','Introduzca los Datos correctamente');
           redirect('acceso/cambiar_clave','refresh');
           exit(0);
	}else
	{
            $id_user=$this->session->userdata('id_usuario');
            $actual=$this->input->post('actual');
            $nueva=$this->input->post('nueva');
            $consulta=$this->Usuarios->cambiar_clave($id_user, $actual, $nueva);
            if ($consulta)
            { 
               $this->session->set_userdata('errores','La Contraseña ha sido cambiada correctamente');
               redirect('acceso','refresh');
               exit(0);
            }
            else
            {
              $this->session->set_userdata('errores','Ha introducido una Contraseña Incorrecta');              
            }
            redirect('acceso/cambiar_clave','refresh');
            exit(0);
        }               
    }
    
    function verificar_sesion()
    {  
      session_start(); 
      if(!isset($_SESSION['aprobado']) || !$this->session->userdata('aprobado'))
      {
        die('FALSE'); // SI LA SESION ESTÁ CERRADA DEBE DAR LA ORDEN DE SALIR DEL SISTEMA
      }
      else die('TRUE');                 
    }
}
/* End of file acceso.php */
/* Location: ./application/controllers/acceso.php */