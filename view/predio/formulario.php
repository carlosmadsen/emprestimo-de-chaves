<?php include __DIR__ . '/../inicio-html.php'; ?>

    <form action="/salvar-predio<?= isset($id) ? '?id='.$id : ''; ?>" method="post" >
        
		<div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" 
                id="nome" 
                name="nome" 
                class="form-control"
				required="required"
                value="<?= isset($nome) ? $nome: ''; ?>"
            >
        </div> 
		

		<?php if (isset($id)) : ?>
		<div class="form-group">
            <label for="ativo">Ativo</label>
            <select class="form-control" id="ativo" name="ativo" required="required" >
				<option></option>
				<option <?= ((isset($ativo) and $ativo=='S') ? 'selected': '') ; ?> value='S' >Sim</option>
				<option <?= ((isset($ativo) and $ativo=='N') ? 'selected': '') ; ?> value='N' >Não</option>
			</select>
        </div> 
		<?php endif; ?>

        <button class="btn btn-primary">Salvar</button>
        <a href="/predios" class="btn btn-secondary">
            Voltar
        </a>
    </form>

<?php include __DIR__ . '/../fim-html.php'; ?>