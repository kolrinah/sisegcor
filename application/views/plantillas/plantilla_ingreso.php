<?php if ( ! defined('BASEPATH')) exit('Sin Acceso Directo al Script'); 
    $this->load->view('plantillas/cabecera');
    $this->load->view('plantillas/encabezado');
    $this->load->view($contenido);
    $this->load->view('plantillas/pie_pagina');
?>