<?php include __DIR__ . '/../inicio-html.php'; ?>

    <form action="/salvar-minha-conta" method="post" >
        
		<div class="form-group">
            <label for="login">Login</label>
            <input type="text" 
                id="login" 
                name="login" 
                class="form-control"
				required="required"
                value="<?= isset($login) ? $login : ''; ?>"
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

		<div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" 
                id="email" 
                name="email" 
                class="form-control"
				required="required"
                value="<?= isset($email) ? $email : '' ; ?>"
            >
        </div> 		       

		<div class="form-group" id="linha_senha_atual" >
            <label for="senha_atual">Senha atual</label>
            <input type="password" 
                id="senha_atual" 
                name="senha_atual" 
                class="form-control"
                required="required"
                value=""
            >
        </div> 

         <div class="form-group"  >
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="S" name="alterar_senha" id="alterar_senha" />
                <label class="form-check-label" for="alterar_senha">Alterar senha</label>
            </div>
        </div> 

		<div class="form-group" id="linha_nova_senha1" >
            <label for="nova_senha1">Nova senha</label>
            <input type="password" 
                id="nova_senha1" 
                name="nova_senha1" 
                class="form-control"	
                value=""
            >
        </div> 

		<div class="form-group" id="linha_nova_senha2" >
            <label for="nova_senha2">Repita a nova senha</label>
            <input type="password" 
                id="nova_senha2" 
                name="nova_senha2" 
                class="form-control"
                value=""
            >
        </div> 

        <button class="btn btn-primary">Salvar</button>
    </form>

<?php include __DIR__ . '/../fim-html.php'; ?>