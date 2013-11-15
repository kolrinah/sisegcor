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
class Organismos extends CI_Model
{
     function listar_organismos($frase, $tipo_organismo)
     {           
       $sql="select * from dir_organismos
             where (translate(upper(organismo),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             like translate(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU')
             or translate(upper(pertenece_a),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') 
             like translate(upper('%$frase%'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU') )
             and activo=TRUE and id_tipo_organismo=$tipo_organismo 
             order by organismo desc";

        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();          
        }
        else {return array('No hubo coincidencias');}
     }     
     
     // Lista Organismos por Tipo de Organismo
     function cuantos_organismos($id_tipo_organismo)
     {
       $sql="select * from dir_organismos
             where id_tipo_organismo=$id_tipo_organismo";

        $query = $this->db->query($sql);        
        return $query; 
     }  
}
?>