<?php include __DIR__ . '/../inicio-html.php'; ?>

    <form action="/salvar-chave<?= isset($id) ? '?id=' . $id : ''; ?>" method="post" >
        
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
            <label for="nome">Número</label>
            <input type="text" 
                id="numero" 
                name="numero" 
                class="form-control"
				required="required"
                value="<?= isset($numero) ? $numero : ''; ?>"
            >
        </div> 
		
		<div class="form-group">
            <label for="nome">Descrição</label>
            <input type="text" 
                id="descricao" 
                name="descricao" 
                class="form-control"
	            value="<?= isset($descricao) ? $descricao : ''; ?>"
            >
        </div> 

		<?php if (isset($id)) : ?>
		<div class="form-group">
            <label for="ativo">Ativo</label>
            <select class="form-control" id="ativo" name="ativo" required="required" >
				<option></option>
				<option <?= ((isset($ativo) and $ativo=='S') ? 'selected' : ''); ?> value='S' >Sim</option>
				<option <?= ((isset($ativo) and $ativo=='N') ? 'selected' : ''); ?> value='N' >Não</option>
			</select>
        </div> 
		<?php endif; ?>

        <button class="btn btn-primary">Salvar</button>
        <a href="/emprestimos" class="btn btn-secondary">
            Voltar
        </a>
    </form>

<?php include __DIR__ . '/../fim-html.php'; ?>