<?php include __DIR__ . '/../inicio-html.php'; ?>

    <form action="/emprestar" method="post" >
        
		<div class="form-group">
            <label for="nome">Prédio</label>
            <select class="form-control" id="predio" name="predio" required="required">
			<option></option>       
			<?php foreach ($predios as $predio): ?>
			<option value="<?= $predio->getId(); ?>" <?= ( (isset($idPredio) ? ($predio->getId() == $idPredio) : false) ? 'selected' : ''); ?> ><?= $predio->getNome(); ?></option>
			<?php endforeach; ?>        
		</select>
        </div> 

		<div class="form-group">
            <label for="numero_chave">Número da Chave</label>
            <input type="text" 
                id="numero_chave" 
                name="numero_chave" 
                class="form-control"
				required="required"
                value="<?= isset($numeroChave) ? $numeroChave : ''; ?>"
            >
        </div> 
		
		<?php if (!empty($labelIdentificacao) > 0) : ?>
		<div class="form-group">
            <label for="identificacao"><?= $labelIdentificacao ?></label>
            <input type="text" 
                id="identificacao" 
                name="identificacao" 
                class="form-control"
				required="required"
                value="<?= isset($identificacao) ? $identificacao : ''; ?>"
            >
        </div> 
		<?php endif; ?>

		<div class="form-group">
            <label for="documento"><?= $labelDocumento ?></label>
            <input type="text" 
                id="documento" 
                name="documento" 
                class="form-control"
				required="required"
                value="<?= isset($documento) ? $documento : ''; ?>"
            >
        </div> 		

        <button class="btn btn-primary">Salvar</button>
        <a href="/emprestimos" class="btn btn-secondary">
            Voltar
        </a>
    </form>

<?php include __DIR__ . '/../fim-html.php'; ?>