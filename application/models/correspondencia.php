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
class Correspondencia extends CI_Model
{
    // OBTIENE LA CANTIDAD DE REGISTROS SEGUN FECHA, TIPO Y ESTRUCTURA DE LA CORRESPONDENCIA
     function cantidad_registros($codigo_generado, $mes, $anho, $id_estructura)
     {        
       $sql="select * from cor_correspondencias cor
             join cor_movimientos mov using(id_correspondencia)
             join usr_usuarios usr on mov.id_usuario=usr.id_usuario
             where cor.codigo_generado like '$codigo_generado%'
             and mov.id_tipo_movimiento=1
             and usr.id_estructura=$id_estructura
             and date_part('month',cor.fecha_recepcion)=$mes
             and date_part('year',cor.fecha_recepcion)=$anho
             order by cor.codigo_generado desc";
       
       $query = $this->db->query($sql);
       if ($query->num_rows()==0) return 0;
       
       $ultimo_cod=$query->result();// PARA OBTENER LA PRIMERA FILA DEL RESULTADO
       
       //   codigo_generado = EXXXX-XXXX-NNNN Necesitamos la ultima cifra 'NNNN'        
       $ultimo_cod=explode("-",$ultimo_cod['0']->codigo_generado);
       
       $ultimo_cod=intval($ultimo_cod['2']); // Al aplicar Explode:0-1-'2' 
       	
       if ($query->num_rows()<$ultimo_cod){return $ultimo_cod;}
       else {return $query->num_rows();}
     }
     
     // BUSCAMOS SI LA CORRESPONDENCIA ESTÁ REPETIDA
     function buscar_repetidas($id_organismo, $nro_comunicado, $fecha_emision, $entrante)
     {
       date_default_timezone_set('America/Caracas'); // Establece la Hora de Venezuela para funciones de fecha y hora              
       //Reemplazamos el '/' por '-' para que interprete la fecha latina (d-m-y)
       $fecha_emision=  str_replace('/', '-', $fecha_emision);
       
       $fecha1=Date("d/m/Y",(strtotime($fecha_emision)-864000)); // 10 días antes de la fecha 
       $fecha2=Date("d/m/Y",(strtotime($fecha_emision)+864000)); // 10 días despues de la fecha 
       
       $sql="select cor.*, org.*, usr.*, tp.*, cl.*, st.*, mov.*, tmov.* from cor_correspondencias cor
             join dir_organismos org using(id_organismo)
             join usr_usuarios usr on usr.id_usuario=cor.id_usuario_asignado
             join cor_tipos tp using (id_tipo)
             join cor_clasificaciones cl using(id_clasificacion)
             join cor_estatus st using(id_estatus)
             join (-- OBTENEMOS EL ULTIMO MOVIMIENTO<9 DE CADA CORRESPONDENCIA
 		      select id_correspondencia, max(id_movimiento) AS id_movimiento from cor_movimientos
 		      where id_tipo_movimiento<9
 		      group by id_correspondencia) um using(id_correspondencia)
	     join cor_movimientos mov using (id_movimiento)
             join cor_tipos_movimiento tmov using (id_tipo_movimiento)
	     join usr_usuarios umov on (umov.id_usuario=mov.id_usuario)
             where id_organismo=$id_organismo
             and TRANSLATE(nro_comunicado,'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU')
             like TRANSLATE(upper('%$nro_comunicado'),'áÁéÉíÍóÓúÚ', 'aAeEiIoOuU')
             and entrante='$entrante'
             and fecha_emision between to_date('$fecha1','DD/MM/YYYY') 
                                   and to_date('$fecha2','DD/MM/YYYY')";
       
       $query = $this->db->query($sql);       
       return $query;
     }

     // OBTENEMOS LOS REGISTROS DE CORRESPONDENCIA ASIGNADAS
     // LOS FUNCIONARIOS SOLO VERAN SUS CORRESPONDENCIAS ASIGNADAS, PERO
     // LOS DISTRIBUIDORES Y JEFES ADEMAS VERAN LAS CORRESPONDENCIAS DE LA ESTRUCTURA ASIGNADAS A OTROS
     function correspondencia_asignada($entrante, $fecha_ini, $fecha_fin, $id_usuario, $id_estructura, $id_tipo, $id_clasificacion)
     {  
       $entrante=($entrante)?'true':'false'; // CONVERSION NECESARIA PARA VARIABLES BOOL 
       $id_tipo=($id_tipo==0)?'':('and id_tipo='.$id_tipo);
       $id_clasificacion=($id_clasificacion==0)?'':('and id_clasificacion='.$id_clasificacion);
         
       $sql="select cor.*, org.*, usr.*, tp.*, cl.*, st.*, mov.*, tmov.* from cor_correspondencias cor
             join dir_organismos org using(id_organismo)
             join usr_usuarios usr on usr.id_usuario=cor.id_usuario_asignado
             join cor_tipos tp using (id_tipo)
             join cor_clasificaciones cl using(id_clasificacion)
             join cor_estatus st using(id_estatus)
             join (-- OBTENEMOS EL ULTIMO MOVIMIENTO<9 DE CADA CORRESPONDENCIA
 		      select id_correspondencia, max(id_movimiento) AS id_movimiento from cor_movimientos
 		      where id_tipo_movimiento<9
 		      group by id_correspondencia) um using(id_correspondencia)
	     join cor_movimientos mov using (id_movimiento)
             join cor_tipos_movimiento tmov using (id_tipo_movimiento)
	     join usr_usuarios umov on (umov.id_usuario=mov.id_usuario)		     	
             where (cor.id_usuario_asignado=$id_usuario or umov.id_usuario=$id_usuario)     
             and st.caso_cerrado=false
             and cor.entrante=$entrante
             $id_tipo
             $id_clasificacion
             and cor.fecha_recepcion between to_date('$fecha_ini','DD/MM/YYYY') and to_date('$fecha_fin','DD/MM/YYYY')";
      $sql.=($id_estructura=='0')?'': //  ES DISTRIBUIDOR O JEFE
            " union
             select cor.*, org.*, usr.*, tp.*, cl.*, st.*, mov.*, tmov.* from cor_correspondencias cor
             join dir_organismos org using(id_organismo)
             join usr_usuarios usr on usr.id_usuario=cor.id_usuario_asignado
             join cor_tipos tp using (id_tipo)
             join cor_clasificaciones cl using(id_clasificacion)
             join cor_estatus st using(id_estatus)
             join (-- OBTENEMOS EL ULTIMO MOVIMIENTO<9 DE CADA CORRESPONDENCIA
 		      select id_correspondencia, max(id_movimiento) AS id_movimiento from cor_movimientos
 		      where id_tipo_movimiento<9
 		      group by id_correspondencia) um using(id_correspondencia)
	     join cor_movimientos mov using (id_movimiento)
             join cor_tipos_movimiento tmov using (id_tipo_movimiento)
	     join usr_usuarios umov on (umov.id_usuario=mov.id_usuario)
             where (tmov.id_tipo_movimiento=2 or tmov.id_tipo_movimiento=6 or tmov.id_tipo_movimiento=7)
             and cor.id_estructura_owner=$id_estructura
             and cor.id_usuario_asignado!=$id_usuario
             and umov.id_usuario!=$id_usuario
             and st.caso_cerrado=false
             and cor.entrante=$entrante
             $id_tipo
             $id_clasificacion
             and cor.fecha_recepcion between to_date('$fecha_ini','DD/MM/YYYY') and to_date('$fecha_fin','DD/MM/YYYY')";
      $sql.=" order by 1 desc";

       $query = $this->db->query($sql);
       
       return $query;
     }
     
     // OBTENEMOS LOS REGISTROS DE CORRESPONDENCIA GENERAL DE NUESTRA UNIDAD E INFERIORES
     // LOS FUNCIONARIOS SOLO VERAN LAS CORRESPONDENCIAS ABIERTAS 
     // NO ASIGNADAS DONDE HICIERON MOVIMIENTOS, PERO
     // LOS DISTRIBUIDORES Y JEFES ADEMAS VERAN LAS CORRESPONDENCIAS DE LAS UNIDADES
     // INFERIORES A LA ESTRUCTURA ASIGNADAS A OTROS
     function correspondencia_general($entrante, $fecha_ini, $fecha_fin, $id_usuario, $estructura, $id_tipo, $id_clasificacion)
     {  
       // BUSCAMOS EL CODIGO DE LA UNIDAD ADMINISTRATIVA DEL USUARIO
       $sql="select * from usr_usuarios
             join e_estructura using(id_estructura)
             where id_usuario=$id_usuario";
       $query = $this->db->query($sql);
       $c=($query->num_rows()>0)?$query->row():die('Error');         
         
       $entrante=($entrante)?'true':'false'; // CONVERSION NECESARIA PARA VARIABLES BOOL 
       $id_tipo=($id_tipo==0)?'':('and id_tipo='.$id_tipo);
       $id_clasificacion=($id_clasificacion==0)?'':('and id_clasificacion='.$id_clasificacion);
  
       $sql="select cor.*, org.*, usr.*, tp.*, cl.*, st.*, mov.*, tmov.* from cor_correspondencias cor
             join dir_organismos org using(id_organismo)
             join usr_usuarios usr on usr.id_usuario=cor.id_usuario_asignado
             join cor_tipos tp using (id_tipo)
             join cor_clasificaciones cl using(id_clasificacion)
             join cor_estatus st using(id_estatus)
             join (-- SELECCIONAMOS LAS CORRESPONDENCIAS 
	          -- DONDE EL USUARIO HAYA EJECUTADO MOVIMIENTOS
	          select id_correspondencia from cor_movimientos
	          where id_tipo_movimiento<9
	          and id_usuario=$id_usuario
	          group by id_correspondencia		   
	          ) m using (id_correspondencia)
             join (-- OBTENEMOS EL ULTIMO MOVIMIENTO<9 DE CADA CORRESPONDENCIA
 		      select id_correspondencia, max(id_movimiento) AS id_movimiento from cor_movimientos
 		      where id_tipo_movimiento<9
 		      group by id_correspondencia) um using(id_correspondencia)
	     join cor_movimientos mov using (id_movimiento)
             join cor_tipos_movimiento tmov using (id_tipo_movimiento)
             where cor.id_usuario_asignado!=$id_usuario
             and st.caso_cerrado=false ";
      $sql.=($entrante=='true')?" and cor.entrante=$entrante 
                                  and cor.codigo_generado not like 'S$c->codigo_estructura-%' ":
                                " and (cor.entrante=$entrante 
                                       or cor.codigo_generado like 'S$c->codigo_estructura-%') ";
      $sql.="
             $id_tipo
             $id_clasificacion
             and cor.fecha_recepcion between to_date('$fecha_ini','DD/MM/YYYY') and to_date('$fecha_fin','DD/MM/YYYY')";
      $sql.=($estructura=='0')?'': //  ES DISTRIBUIDOR O JEFE
            " union
             select cor.*, org.*, usr.*, tp.*, cl.*, st.*, mov.*, tmov.* from cor_correspondencias cor 
             join dir_organismos org using(id_organismo) 
             join usr_usuarios usr on usr.id_usuario=cor.id_usuario_asignado 
             join cor_tipos tp using (id_tipo) 
             join cor_clasificaciones cl using(id_clasificacion) 
             join cor_estatus st using(id_estatus) 
             join (-- OBTENEMOS EL ULTIMO MOVIMIENTO<9 DE CADA CORRESPONDENCIA
 		      select id_correspondencia, max(id_movimiento) AS id_movimiento from cor_movimientos
 		      where id_tipo_movimiento<9
 		      group by id_correspondencia) um using(id_correspondencia)
	     join cor_movimientos mov using (id_movimiento) 
             join cor_tipos_movimiento tmov using (id_tipo_movimiento)
             where ($estructura) 
             and cor.id_usuario_asignado!=$id_usuario 
             and st.caso_cerrado=false ";
      $sql.=($entrante=='true')?" and cor.entrante=$entrante 
                                  and cor.codigo_generado not like 'S$c->codigo_estructura-%' ":
                                " and (cor.entrante=$entrante 
                                       or cor.codigo_generado like 'S$c->codigo_estructura-%') ";
      $sql.="
             $id_tipo
             $id_clasificacion
             and cor.fecha_recepcion between to_date('$fecha_ini','DD/MM/YYYY') and to_date('$fecha_fin','DD/MM/YYYY')";
      $sql.=" order by 1 desc";
       
//var_dump($sql);
       $query = $this->db->query($sql);
       
       return $query;
     } 
     
     // OBTENEMOS LOS REGISTROS DE CORRESPONDENCIA GENERAL DE NUESTRA UNIDAD E INFERIORES
     // LOS FUNCIONARIOS SOLO VERAN LAS CORRESPONDENCIAS CERRADAS 
     // NO ASIGNADAS DONDE HICIERON MOVIMIENTOS, PERO
     // LOS DISTRIBUIDORES Y JEFES ADEMAS VERAN LAS CORRESPONDENCIAS DE LAS UNIDADES
     // INFERIORES A LA ESTRUCTURA ASIGNADAS A OTROS
     function correspondencia_archivo($entrante, $fecha_ini, $fecha_fin, $id_usuario, $estructura, $id_tipo, $id_clasificacion)
     { 
       // BUSCAMOS EL CODIGO DE LA UNIDAD ADMINISTRATIVA DEL USUARIO
       $sql="select * from usr_usuarios
             join e_estructura using(id_estructura)
             where id_usuario=$id_usuario";
       $query = $this->db->query($sql);
       $c=($query->num_rows()>0)?$query->row():die('Error');         
         
       $entrante=($entrante)?'true':'false'; // CONVERSION NECESARIA PARA VARIABLES BOOL 
       $id_tipo=($id_tipo==0)?'':('and id_tipo='.$id_tipo);
       $id_clasificacion=($id_clasificacion==0)?'':('and id_clasificacion='.$id_clasificacion);
         
       $sql="select cor.*, org.*, usr.*, tp.*, cl.*, st.*, mov.*, tmov.* from cor_correspondencias cor
             join dir_organismos org using(id_organismo)
             join usr_usuarios usr on usr.id_usuario=cor.id_usuario_asignado
             join cor_tipos tp using (id_tipo)
             join cor_clasificaciones cl using(id_clasificacion)
             join cor_estatus st using(id_estatus)
             join (-- SELECCIONAMOS LAS CORRESPONDENCIAS 
	          -- DONDE EL USUARIO HAYA EJECUTADO MOVIMIENTOS
	          select id_correspondencia from cor_movimientos
	          where id_tipo_movimiento<9
	          and id_usuario=$id_usuario
	          group by id_correspondencia		   
	          ) m using (id_correspondencia)
             join (-- OBTENEMOS EL ULTIMO MOVIMIENTO<9 DE CADA CORRESPONDENCIA
 		      select id_correspondencia, max(id_movimiento) AS id_movimiento from cor_movimientos
 		      where id_tipo_movimiento<9
 		      group by id_correspondencia) um using(id_correspondencia)
	     join cor_movimientos mov using (id_movimiento)
             join cor_tipos_movimiento tmov using (id_tipo_movimiento)
             where st.caso_cerrado=true ";
      $sql.=($entrante=='true')?" and cor.entrante=$entrante 
                                  and cor.codigo_generado not like 'S$c->codigo_estructura-%' ":
                                " and (cor.entrante=$entrante 
                                       or cor.codigo_generado like 'S$c->codigo_estructura-%') ";
      $sql.="             
             $id_tipo
             $id_clasificacion
             and cor.fecha_recepcion between to_date('$fecha_ini','DD/MM/YYYY') and to_date('$fecha_fin','DD/MM/YYYY')";
      $sql.=($estructura=='0')?'': //  ES DISTRIBUIDOR O JEFE
            " union
             select cor.*, org.*, usr.*, tp.*, cl.*, st.*, mov.*, tmov.* from cor_correspondencias cor 
             join dir_organismos org using(id_organismo) 
             join usr_usuarios usr on usr.id_usuario=cor.id_usuario_asignado 
             join cor_tipos tp using (id_tipo) 
             join cor_clasificaciones cl using(id_clasificacion) 
             join cor_estatus st using(id_estatus)
             join (-- OBTENEMOS EL ULTIMO MOVIMIENTO<9 DE CADA CORRESPONDENCIA
 		      select id_correspondencia, max(id_movimiento) AS id_movimiento from cor_movimientos
 		      where id_tipo_movimiento<9
 		      group by id_correspondencia) um using(id_correspondencia)
	     join cor_movimientos mov using (id_movimiento)
             join cor_tipos_movimiento tmov using (id_tipo_movimiento)
             where (true $estructura) 
             and cor.id_usuario_asignado!=$id_usuario 
             and st.caso_cerrado=true ";
      $sql.=($entrante=='true')?" and cor.entrante=$entrante 
                                  and cor.codigo_generado not like 'S$c->codigo_estructura-%' ":
                                " and (cor.entrante=$entrante 
                                       or cor.codigo_generado like 'S$c->codigo_estructura-%') ";
      $sql.="             
             $id_tipo
             $id_clasificacion
             and cor.fecha_recepcion between to_date('$fecha_ini','DD/MM/YYYY') and to_date('$fecha_fin','DD/MM/YYYY')";
             
      $sql.=" order by 1 desc";
  //  var_dump($sql);  
      $query = $this->db->query($sql);
       
      return $query;
     }        
     
     // OBTENEMOS CORRESPONDENCIA SEGUN ID_CORRESPONDENCIA
     function get_correspondencia($id_correspondencia)
     {        
       $sql="select * from cor_correspondencias cor
             join dir_organismos org using(id_organismo)
             join usr_usuarios usr on usr.id_usuario=cor.id_usuario_asignado
             join e_estructura e on cor.id_estructura_owner=e.id_estructura
             join cor_tipos tp using (id_tipo)
             join cor_clasificaciones cl using(id_clasificacion)
             join cor_estatus st using(id_estatus)
             where cor.id_correspondencia=$id_correspondencia";

       $query = $this->db->query($sql);       
       return $query;
     }
     
      // OBTENEMOS MOVIMIENTOS DE CORRESPONDENCIA SEGUN ID_CORRESPONDENCIA
     function get_movimientos($id_correspondencia)
     {        
       $sql="select * from cor_movimientos mov
             join cor_correspondencias cor using(id_correspondencia)             
             join usr_usuarios usr on usr.id_usuario=mov.id_usuario
             join cor_tipos_movimiento tp using (id_tipo_movimiento)                          
             where cor.id_correspondencia=$id_correspondencia
             order by id_movimiento asc";

       $query = $this->db->query($sql);       
       return $query;
     }
     
     // OBTENEMOS LOS MOVIMIENTOS POSIBLES DEPENDIENDO DEL ULTIMO MOVIMIENTO <9
     function get_movimientos_posibles($id_ultimo_movimiento, $c1=true, $c2=true, $c3=true, $entrante=true)
     {
       $c1=($c1)?'true':'false'; // CONVERSION NECESARIA PARA VARIABLES BOOL 
       $c2=($c2)?'true':'false'; // CONVERSION NECESARIA PARA VARIABLES BOOL 
       $c3=($c3)?'true':'false'; // CONVERSION NECESARIA PARA VARIABLES BOOL 
       $entrante=($entrante)?'true':'false'; // CONVERSION NECESARIA PARA VARIABLES BOOL 
       
       $sql="select * from cor_movimientos_posibles
             join cor_tipos_movimiento on id_movimiento_posible=id_tipo_movimiento
             where id_ultimo_movimiento=$id_ultimo_movimiento
             and c1=$c1
             and c2=$c2
             and c3=$c3
             and entrante=$entrante
             order by id_movimiento_posible";
       
       $query = $this->db->query($sql);
       return $query;
     }
     
     // OBTENEMOS EL ULTIMO MOVIMIENTO DE LA CORRESPONDENCIA EXCLUYENDO EL TIPO >9
     function get_ultimo_movimiento($id_correspondencia)
     {
       $sql="select m.*, usr.nombre, usr.apellido from cor_movimientos m
             join usr_usuarios usr using (id_usuario)
             join (   
                    SELECT max(id_movimiento) AS id_movimiento
                    FROM cor_movimientos 
                    Where id_correspondencia=$id_correspondencia and id_tipo_movimiento<9
                  ) j using (id_movimiento);";
       
       $query = $this->db->query($sql);
       return $query;         
     }
}
?>