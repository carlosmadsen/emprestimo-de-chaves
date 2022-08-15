<?php include __DIR__ . '/../inicio-html.php'; ?>

    <form action="/salvar-pessoa<?= isset($id) ? '?id='.$id : ''; ?>" method="post" >
        
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
                value="<?= isset($idocumento) ? $documento : ''; ?>"
            >
        </div> 

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
       

        <button class="btn btn-primary">Salvar</button>
        <a href="/pessoas" class="btn btn-secondary">
            Voltar
        </a>
    </form>

<?php include __DIR__ . '/../fim-html.php'; ?>