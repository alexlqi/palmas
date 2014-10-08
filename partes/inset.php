<div class="inset_form">
  <h3 class="titulo_form">Pertenece a:</h3>
	<div class="campo_form">
        <label class="">Área:</label>
        <select class="id_area" name="id_area"><?php 
		try{
			$bd=new PDO($dsnw, $userw, $passw, $optPDO);
			$res=$bd->query("SELECT * FROM areas WHERE id_empresa=".varEmpresa().";");
			echo '<option selected="selected" disabled="disabled">Elige área</option>';
			foreach($res->fetchAll(PDO::FETCH_ASSOC) as $v){
				echo '<option value="'.$v["id_area"].'">'.$v["nombre"].'</option>';
			}
		}catch(PDOException $err){
			echo "<option>Error: ".$err->getCode()." SELECT * FROM areas WHERE id_empresa=".varEmpresa().";"."</option>";
		}
		?></select>
    </div>
    <div class="campo_form">
        <label class="">Familia</label>
        <select class="id_familia" name="id_familia"><?php 
		//echo '<option selected="selected" disabled="disabled">Elige área</option>'; 
		?></select>
    </div>
    <div class="campo_form">
        <label class="">Sub familia</label>
        <select class="id_subfamilia" name="id_subfamilia"><?php 
		//echo '<option selected="selected" disabled="disabled">Elige área</option>';
		?></select>
    </div>
</div>
