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
class Estructura extends CI_Model
{
     function busca_oficina($frase)
     {           
       $sql="SELECT e.id_estructura as id, e.id_tipo_estructura as tipo, e.codigo_estructura, e.estructura,
              case 
                  when e.id_tipo_estructura<3 then e.estructura
                  when e.id_tipo_estructura=4 then e1.estructura
                  else e.estructura
              end as direccion,
              case 
                  when e.id_tipo_estructura<3 then e.estructura
                  when e.id_tipo_estructura=4 then e2.estructura
                  else e1.estructura
              end as oficina
             FROM e_estructura e
             INNER JOIN e_estructura e1 ON e1.id_estructura=e.id_superior
             INNER JOIN e_estructura e2 ON e2.id_estructura=e1.id_superior
             WHERE TRANSLATE(e.estructura,'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             LIKE TRANSLATE(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') AND e.activo=TRUE
             OR e.codigo_estructura LIKE '%$frase%' 
             ORDER BY tipo DESC, oficina, direccion, estructura ASC LIMIT 10;";

        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();          
        }
        else {return array('No hubo coincidencias');}
     }
     
     //LISTA TODAS LAS UNIDADES QUE SE ENCUENTRAN POR DEBAJO DE LA ESTRUCTURA DADA
     function listar_unidades($frase,$id_estructura)
     {           
       $sql="SELECT e.id_estructura as id, e.id_tipo_estructura as tipo, e.codigo_estructura, e.estructura, 
              case 
                  when e.id_tipo_estructura<3 then e.estructura
                  when e.id_tipo_estructura=4 then e1.estructura
                  else e.estructura
              end as direccion,
              case 
                  when e.id_tipo_estructura<3 then e.estructura
                  when e.id_tipo_estructura=4 then e2.estructura
                  else e1.estructura
              end as oficina
             FROM (-- OBTENEMOS OFICINAS
		select * from e_estructura
		where (id_superior=$id_estructura or id_estructura=$id_estructura) and activo=true
		union
		-- OBTENEMOS DIRECCIONES DE LINEA
		select d.* from e_estructura d
		join (select * from e_estructura where id_superior=$id_estructura or id_estructura=$id_estructura ) o on o.id_estructura=d.id_superior
		where d.activo=true
		union
		-- OBTENEMOS AREAS
		select a.* from e_estructura a
		join (select d.id_estructura, d.codigo_estructura, d.estructura, d.id_tipo_estructura, d.id_superior, d.activo from e_estructura d
		join (select * from e_estructura where id_superior=$id_estructura or id_estructura=$id_estructura ) o on o.id_estructura=d.id_superior) m on m.id_estructura= a.id_superior) e
             INNER JOIN e_estructura e1 ON e1.id_estructura=e.id_superior 
             INNER JOIN e_estructura e2 ON e2.id_estructura=e1.id_superior 
             WHERE TRANSLATE(e.estructura,'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             LIKE TRANSLATE(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') AND e.activo=TRUE
             OR e.codigo_estructura LIKE '%$frase%' 
             ORDER BY 1 LIMIT 10;";

        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();          
        }
        else {return array('No hubo coincidencias');}
     }
     
     // OBTENER ESTRUCTURAS INFERIORES A UNA ESTRUCTURA DADA
     function obtener_estructuras_inferiores($id_estructura)
     {
        $sql=" -- OBTENEMOS OFICINAS
              select * from e_estructura
              where (id_superior=$id_estructura or id_estructura=$id_estructura) 
              and activo=true
              union
              -- OBTENEMOS DIRECCIONES DE LINEA
              select d.*
              from e_estructura d
              join 
               (select * from e_estructura where id_superior=$id_estructura or id_estructura=$id_estructura) o
                on o.id_estructura=d.id_superior
              where d.activo=true
              union
              -- OBTENEMOS AREAS
              select a.*
              from e_estructura a
              join 
               (select d.* from e_estructura d
                join (select * from e_estructura where id_superior=$id_estructura or id_estructura=$id_estructura) o 
                      on o.id_estructura=d.id_superior) m on m.id_estructura= a.id_superior
              where a.activo=true
              order by 1";
        
        $query = $this->db->query($sql);
        return $query;        
     }
     
     // LISTAR UNIDADES QUE DEPENDEN DIRECTAMENTE DE UNA ESTRUCTURA DADA
     function listar_unidades_inferiores($id_estructura, $frase='')
     {
        $sql="select * from e_estructura
              where activo=true 
              and id_superior=$id_estructura
              and TRANSLATE(estructura,'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU')
                  like TRANSLATE(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU')
              order by estructura";
        
        $query = $this->db->query($sql);
        return $query;        
     }
     
     // LISTAR UNIDADES POSIBLES PARA DISTRIBUCION
     function listar_unidades_distribucion($id_estructura, $frase='')
     {
        $sql="select e.*, s.estructura as superior, s.codigo_estructura as codigo_superior
              from e_estructura e
              join e_estructura s on e.id_superior=s.id_estructura
              where e.activo=true               
              and (TRANSLATE(upper(e.estructura),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU')
                  like TRANSLATE(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU')
              or e.codigo_estructura like upper('%$frase%'))
	      and e.id_estructura!=$id_estructura
              and e.servicio_interno=true
              and (e.id_superior=$id_estructura
              or e.id_superior=(select id_superior from e_estructura
                              where id_estructura=$id_estructura
                              and activo=true))
              order by e.estructura";
        
        $query = $this->db->query($sql);
        return $query;        
     }
     
     // OBTENER DETALLES DE UNA ESTRUCTURA
     function obtener_estructura($id_estructura)
     {        
        $this->db->where('id_estructura', $id_estructura);        
        $query=$this->db->get('e_estructura');
        if($query->num_rows()==1){return $query->row_array();}
        else {return false;}
     }
}
?>