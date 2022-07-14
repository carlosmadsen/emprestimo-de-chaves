<?php include __DIR__ . '/../inicio-html.php'; ?>

    <form action="/salvar-instituicao" method="post">
        <div class="form-group">
            <label for="sigla">Sigla:</label>
            <input type="text" name="sigla" id="sigla" class="form-control" required="required" value="<?= $sigla ?>" >
        </div>
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" class="form-control" required="required" value="<?= $nome ?>" >
        </div>
        <button class="btn btn-primary">
            Salvar
        </button>
        <a href="/emprestimos" class="btn btn-secondary" >
            Voltar
        </a>        
    </form> 
	
<?php include __DIR__ . '/../fim-html.php'; ?>