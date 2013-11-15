<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script');?>

<div class="container_16">
<footer>
    <div class="grid_16" id="pie"> 
        <table width="100%">
            <tr>
                <td>
         <!-- Establecemos la base url para comunicarnos con javascript -->
                    <input type="hidden" id="base_url" value="<?php echo base_url() ?>"/>
            <center>
                    <?PHP
                    if ($this->session->userdata('aprobado')) {
                        echo $this->session->userdata('usuario');
                        echo ' - COD ';
                        echo $this->session->userdata('cod_estruct');
                        echo ' - ';
                        echo $this->session->userdata('nombre_estruct');
                    }
                    ?>                 
            </center></td>                   
            </tr>
            <tr>
                <td><center>Ministerio del Poder Popular para Relaciones Exteriores</center></td>                   
            </tr>
        </table>
    </div>
    <div class="clear"></div>
</footer>
</div> <!-- CIERRE DEL CONTENEDOR  -->
<div id="cargandoModal"></div>
<div id="VentanaModal"></div>
</body>
</html>