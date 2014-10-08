<?php 
include("partes/header.php"); 
include("scripts/funciones.php");
include("scripts/func_form.php");?>
<style>
#filtro_tipo label{
	margin-left:5px;
	padding:0px 5px;
	background-color:#903;
	color:#FFF;
}
</style>
<script src="js/home.js"></script>
<div id="contenido">
        <!-- Aquí van los controles para el calendario -->
        <div id="control_calendario" class="tabla" align="center">
          <div class="celda">
            <div id="mes_previo" class="mover_calendario" data-m="" data-a=""></div>
            <div id="mesanio" data-m="<?php esteMes(); ?>" data-a="<?php esteAnio(); ?>"></div>
            <div id="mes_siguiente" class="mover_calendario" data-m="" data-a=""></div>
          </div>
          <div id="filtro_tipo" class="celda">
          	  <div align="center">FILTROS</div>
              <label>Documento:</label><select id="tipo_proyecto"><!--
                  --><option selected="selected">Selecciona</option>
                  <option value="ambos">Ambos</option>
                  <option value="cotizacion">Cotizaciones</option>
                  <option value="event">Eventos</option>
              </select>
              <label>Tipo:</label><select id="tipo_evento"><!--
                  --><option selected="selected">Selecciona</option>
                  <option value="todos">Todos</option>
                  <?php tipoEventosOpt(); ?>
              </select>
          </div>
          <div id="estatus" class="celda">
          	<div align="left">Estatus de proyecto:</div>
            <div style="margin-bottom:10px;">
            	<label>Cotizaciones</label><span class="fondo_gris negrita borde_quince estatus_sin desvanecer" data-sel="cancelado">cancelado</span>
	            <span class="fondo_azul borde_quince estatus_evento desvanecer" data-sel="pendiente">Pendiente</span>
            </div>
            <div>
            	<label>Eventos</label><span class="fondo_rojo borde_quince estatus_sin desvanecer" data-sel="no_autorizado">Sin autorizar</span>
            	<span class="fondo_verde borde_quince estatus_evento desvanecer" data-sel="evento">Evento</span>
            </div>
          </div>
        </div>
        <div id="calendario" class="tabla" align="center">
        <!-- Aquí va la tabla del mes con divs -->
          <div id="dias" class="fila fondo_azul">
            <div class="celda">DOMINGO</div>
            <div class="celda">LUNES</div>
            <div class="celda">MARTES</div>
            <div class="celda">MIÉRCOLES</div>
            <div class="celda">JUEVES</div>
            <div class="celda">VIERNES</div>
            <div class="celda">SÁBADO</div>
          </div>
          <div id="semana1" class="semana fila fondo_gris">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
          <div id="semana2" class="semana fila fondo_gris">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
          <div id="semana3" class="semana fila fondo_gris">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
          <div id="semana4" class="semana fila fondo_gris">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
          <div id="semana5" class="semana fila fondo_gris">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
          <div id="semana6" class="semana fila fondo_gris" style="display:none;">
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
            <div class="celda"><div class="contenido_dia" style="height:100%; width:100%;"><span class="dia_der_top"></span></div></div>
          </div>
        </div>
        <!--temrina el -->  
</div><!-- //div tag de contenido -->
<?php include("partes/footer.php"); ?>