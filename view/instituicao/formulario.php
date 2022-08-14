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
        <div class="form-group">
            <label for="identificacao">Label para o número de identificação das pessoas:</label>
            <input type="text" name="identificacao" id="identificacao" class="form-control" value="<?= $identificacao ?>" >
        </div>
        <div class="form-group">
            <label for="_documento">Label para o número de documento das pessoas:</label>
            <input type="text" name="documento" id="documeto" class="form-control" required="required" value="<?= $documento ?>" >
        </div>
        <button class="btn btn-primary">
            Salvar
        </button>
        <a href="/emprestimos" class="btn btn-secondary" >
            Voltar
        </a>        
    </form> 
	
<?php include __DIR__ . '/../fim-html.php'; ?>