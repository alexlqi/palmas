<?php
include("partes/header.php");
include("scripts/botones_form.php");
?>
<script src="js/modulos.js"></script>
<style>
#botones_modulo *{
	-webkit-user-select: none; /* webkit (safari, chrome) browsers */
	-moz-user-select: none; /* mozilla browsers */
	-khtml-user-select: none; /* webkit (konqueror) browsers */
	-ms-user-select: none; /* IE10+ */
}
.boton_abrir_form, .boton_abrir_form_dos{
	display:inline-block;
	background-color:#36F;
	color:#FFF;
	margin:10px;
	width:120px !important;
	height:80px !important;
	cursor:pointer;
	font-weight:bold;
	font-size:1.1em;
}
.boton_abrir_form:hover, .boton_abrir_form_dos:hover{
	background-color:#C06;
}
.loading{
	margin-top:5px;
	display:none;
}
</style>
<div id="contenido">
<div id="tabs">
<ul>
  <li><a href="#acciones">Acciones</a></li>
  <li><a href="#reportes">Reportes</a></li>
  <li><a href="#config">Configuraciones</a></li>
</ul>
<div id="acciones">
	<div id="botones_modulo" style="" align="center">
    <?php //se muestran los botones de la categoria del usuario
		foreach($botones[$categoria] as $boton){ ?>
    	<div align="center" class="<?php echo $boton["accion"]; ?>" data-m="<?php echo $boton["metodo"]; ?>" data-form="<?php echo $boton["tabla"]; ?>">
          <div class="tabla" style="height:100%; text-align:center;"><div class="celda centrado_v">
        	<span><?php echo $boton["nombre"]; ?></span><br />
            <img class="loading" src="img/loading.gif" />
          </div></div>
        </div>
	<?php } ?>
    </div>
    <div id="formularios_modulo" style="padding-top:10px;"></div>
</div>
<div id="reportes">
	<label>Sección a reportar</label><select class="seccion" name="seccion" onchange="filtro1(this);">
    	<option value="eventos">Eventos</option>
        <option value="proveedores">Proveedores</option>
        <option value="bancos">Bancos</option>
    </select>
    <label>Filtrar por:</label><select class="filtro1" name="filtro1" onchange="filtro(this)"></select>
</div>
<div id="config">
</div>
</div>
</div>
<?php include("partes/footer.php"); ?>