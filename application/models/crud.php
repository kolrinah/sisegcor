<?php
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
class Crud extends CI_Model
{
   // FUNCIONES GENERALES: CREATE, READ, UPDATE, DELETE
   function listar_registros($tabla,$donde=0) 
   {
     if ($donde!=0) $this->db->where($donde);       
     $query = $this->db->get($tabla);
     return $query;
   }
   
   function contar_items($tabla, $datos)
   {
     $this->db->where($datos);
     $this->db->from($tabla);
     return $this->db->count_all_results();
   }
   
   function buscar_item($tabla, $datos)
   {
     $this->db->or_where($datos);
     $this->db->from($tabla);
     echo $this->db->count_all_results();
   }
   
   function insertar_registro($tabla, $datos)
   {
       $this->db->insert($tabla, $datos);
       if ($this->db->affected_rows()>0){return true;}
       else {return false;}
   }
   
   function eliminar_registro($tabla, $donde)
   {
       $this->db->delete($tabla, $donde);
       if ($this->db->affected_rows()>0){return true;}
       else {return false;}
   }
   
   function actualizar_registro($tabla, $datos, $donde)
   {
       $this->db->update($tabla, $datos, $donde);  
       if ($this->db->affected_rows()>0){return true;}
       else {return false;}
   }
   
   function listar_tablas()
   {
       $tables = $this->db->list_tables();
       return $tables;
   }
   
   function listar_campos($tabla)
   {
       $campos = $this->db->field_data($tabla);
       return $campos;
   }
   
   function agregar_campo($tabla, $comando)
   {
     $sql = "ALTER TABLE $tabla ADD COLUMN $comando";

        $query = $this->db->query($sql);
        return $query;        
   }
   
   function borrar_campo($tabla, $campo)
   {
     $sql = "ALTER TABLE $tabla DROP COLUMN $campo";

     $query = $this->db->query($sql);
     return $query;        
   }
   
   // EJECUTA COMANDOS SQL
     function ventana_sql($sql)
     { 
        $query = $this->db->query($sql);
        
        if (is_bool($query))return $query;
                
        return $query->result_array();        
     }
     
   function buscarLDAP($patron)
   {       
       $ldaphost="repldap.mppre.gob.ve";
       $ldapport="389";
       $resLDAP=ldap_connect($ldaphost, $ldapport) ;
       if(!$resLDAP){return 0;}
       //echo "paso 1 aprobado<br/>";
       ldap_set_option($resLDAP,LDAP_OPT_PROTOCOL_VERSION,3);
       $ldapbin=@ldap_bind($resLDAP,"cn=admin,dc=gob,dc=ve","12wsxzaq");
       if(!$ldapbin) {return 0;}
       //echo "si se conectó<br/>";
       $dn="ou=people,dc=mppre,dc=gob,dc=ve";       
       $necesito=array("givenname","sn","mail","description");
       // PRIMERO BUSCAMOS POR CI
       $filtro="(&(description=$patron))";
       $busqueda= ldap_search($resLDAP,$dn,$filtro,$necesito);
       $resultado= ldap_get_entries($resLDAP,$busqueda);		
       if($resultado["count"]==0)  // USUARIO NO ENCONTRADO
       {
         //AHORA BUSCAMOS POR MAIL
         unset ($resultado);  
         $filtro="(&(mail=$patron))";
         $busqueda= ldap_search($resLDAP,$dn,$filtro,$necesito);
         $resultado= ldap_get_entries($resLDAP,$busqueda);
         if($resultado["count"]==0){return json_encode($resultado);} // USUARIO NO ENCOTRADO             
       }
       // USUARIO ENCONTRADO
       $nombre=$resultado[0]["givenname"];
       $apellido=$resultado[0]["sn"];
       $mail=$resultado[0]["mail"];
       $cedula=$resultado[0]["description"];
       
       $usuario=array(
           'count'    => 1,
           'nombre'   => mb_convert_case(trim($nombre[0]),MB_CASE_TITLE,'UTF-8'),
           'apellido' => mb_convert_case(trim($apellido[0]),MB_CASE_TITLE,'UTF-8'),
           'mail'     => mb_convert_case(trim($mail[0]),MB_CASE_LOWER,'UTF-8'),
           'cedula'   => trim($cedula[0])
       );       
       return json_encode($usuario);
   }
   
// EMULADOR LDAP
   function ebuscarLDAP($patron)
   {       
     $usuario=array(
           'count'    => 1,
           'nombre'   => 'Hector',
           'apellido' => 'Martinez',
           'mail'     => 'hector.martinez164@mppre.gob.ve',
           'cedula'   => '11410164'
       );
       return json_encode($usuario); 
       //  return false;
   }
}
?>