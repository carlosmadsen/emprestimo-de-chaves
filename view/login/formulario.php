<?php include __DIR__ . '/../inicio-html.php'; ?>

    <form action="/realiza-login" method="post">
        <div class="form-group">
            <label for="login">Login:</label>
            <input type="login" name="login" id="login" class="form-control" required="required" >
        </div>
        <div class="form-group">
            <label for="senha">Senha:</label>
            <input type="password" name="senha" id="senha" class="form-control" required="required" >
        </div>
        <button class="btn btn-primary">
            Entrar
        </button>
    </form> 
	
<?php include __DIR__ . '/../fim-html.php'; ?>