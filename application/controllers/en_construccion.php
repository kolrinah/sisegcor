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
class En_construccion extends CI_Controller {

    function __construct() 
    {
      parent::__construct();
      $this->load->helper('form');
      //$this->load->library('form_validation');
      //$this->load->model('Usuarios');
      //$this->load->model('Estructura');
      //$this->load->model('Proyectos');
      //$this->load->model('Crud');
    }
    
    function index()
    {        
      // VERIFICAMOS SI EXISTE SESION ABIERTA    
      if (!$this->session->userdata('aprobado')) {redirect ('acceso', 'refresh'); exit();}
    
      $data=array();
      $data['titulo']='Sección en Construcción';
            
      $data['contenido']='en_construccion';
      $data['imagen']= array(
          'src' => base_url().'imagenes/construccion.gif',
          'alt' => 'En Construcción',                    
          'title' => 'Sección en Construcción');
            
      $this->load->view('plantillas/plantilla_general',$data);    
    }
}
?>