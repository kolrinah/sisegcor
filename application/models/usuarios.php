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
class Usuarios extends CI_Model
{
    function verificar_acceso($usuario,$clave)
    {
        $sql="select * from usr_usuarios u
              join e_estructura e using(id_estructura)
              where (u.usuario='$usuario' or u.correo='$usuario')
              and u.activo=true
              and u.clave='$clave';";
        
        $query = $this->db->query($sql);
        if($query->num_rows()==1){return $query->row();}
        else {return false;}
     }
     
     function obten_estructura($id_estructura)
     {
        $this->db->where('id_estructura', $id_estructura);
        $this->db->where('activo', 't');
        $query=$this->db->get('e_estructura');
        if($query->num_rows()==1){return $query->row_array();}
        else {return false;}
     }
     
     function cambiar_clave($id_user, $actual, $nueva)
     {
       $datos=array(
                'clave'=> $nueva
                );        
       $this->db->where('id_usuario', $id_user);
       $this->db->where('clave', $actual);
       $this->db->update('usr_usuarios', $datos);
       if ($this->db->affected_rows()>0){return true;}
       else {return false;}
     }
     
     function obtener_usuario($id_usuario)
     {        
        $seleccion='usr_usuarios.id_usuario, usr_usuarios.id_estructura, usr_usuarios.id_nivel';
        $seleccion.=', usr_usuarios.nombre, usr_usuarios.apellido, usr_usuarios.id_nivel';
        $seleccion.=', usr_usuarios.correo, usr_usuarios.administrador, usr_usuarios.activo';
        $seleccion.=', usr_usuarios.cedula, e_estructura.codigo_estructura, e_estructura.estructura';
        $seleccion.=', e_estructura.id_tipo_estructura';
          
        $this->db->select($seleccion);
        $this->db->where('id_usuario', $id_usuario);
        $this->db->join('e_estructura', 'usr_usuarios.id_estructura=e_estructura.id_estructura');
        $query=$this->db->get('usr_usuarios');
        if($query->num_rows()==1){return $query->row();}
        else {return false;}
     }
         
     function listar_usuarios($id_estructura)
     {
        $sql="select u.id_estructura, u.id_usuario, u.nombre,
                     u.apellido, u.id_nivel, u.activo, u.correo,
                     u.administrador, e.codigo_estructura, e.estructura,
                     e.id_tipo_estructura from usr_usuarios u
              join
              (-- OBTENEMOS OFICINAS
              select * from e_estructura
              where (id_superior=$id_estructura or id_estructura=$id_estructura) and activo=true
              union
              -- OBTENEMOS DIRECCIONES DE LINEA
              select d.* from e_estructura d
              join (select * from e_estructura 
                    where id_superior=$id_estructura or id_estructura=$id_estructura ) o
              on o.id_estructura=d.id_superior
              where d.activo=true
              union
              -- OBTENEMOS AREAS
              select a.* from e_estructura a
              join (select d.id_estructura, d.codigo_estructura, d.estructura, 
                    d.id_tipo_estructura, d.id_superior, d.activo from e_estructura d
              join (select * from e_estructura where id_superior=$id_estructura or id_estructura=$id_estructura ) o 
              on o.id_estructura=d.id_superior) m on m.id_estructura= a.id_superior
              where a.activo=true
              order by 1) e using (id_estructura)
              order by 1, 2";
        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
     }   
     
     // ARMAR LA LISTA PARA AUTOCOMPLETAR BUSCANDO ANALISTAS
     function listar_analistas($frase, $id_estructura, $id_usuario)
     { 
       $sql="select id_usuario, nombre || ' ' || apellido as usuario, nivel from usr_usuarios
             join usr_niveles using(id_nivel)
             where (translate(upper(nombre),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             like translate(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') or 
             translate(upper(apellido),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             like translate(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU'))             
             and id_usuario!=$id_usuario
             and activo=TRUE and id_estructura=$id_estructura
             order by usuario asc ";

        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();          
        }
        else {return array('No hubo coincidencias');}
     } 
}
?>