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
class Bitacora_mdl extends CI_Model
{  
     function listar_bitacora($fecha_ini, $fecha_fin)
     {  
        $sql="SELECT * FROM z_bitacora
              WHERE fecha between to_date('$fecha_ini','DD/MM/YYYY')
                              and (to_date('$fecha_fin','DD/MM/YYYY') + 1)
              ORDER BY fecha desc;";        
        $query = $this->db->query($sql);
        if($query->num_rows()>0)
        {
           return $query->result_array();
        }
        else {return false;}
     }
}
?>