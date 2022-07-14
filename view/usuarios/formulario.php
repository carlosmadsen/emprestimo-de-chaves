<?php include __DIR__ . '/../inicio-html.php'; ?>

    <form action="/salvar-usuario<?= isset($usuario) ? '?id='.$usuario->getId() : ''; ?>" method="post" >
        
		<div class="form-group">
            <label for="login">Login</label>
            <input type="text" 
                id="login" 
                name="login" 
                class="form-control"
				required="required"
                value="<?= isset($usuario) ? $usuario->getLogin(): ''; ?>"
            >
        </div> 

		<div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" 
                id="senha" 
                name="senha" 
                class="form-control"
				<?= (!isset($usuario) ?  'required="required"' : ''); ?>
                value=""
            >
        </div> 

		<div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" 
                id="nome" 
                name="nome" 
                class="form-control"
				required="required"
                value="<?= isset($usuario) ? $usuario->getNome(): ''; ?>"
            >
        </div> 

		<div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" 
                id="email" 
                name="email" 
                class="form-control"
				required="required"
                value="<?= isset($usuario) ? $usuario->getEmail(): ''; ?>"
            >
        </div> 

		<div class="form-group">
            <label for="observacao">Observação</label>
            <input type="text" 
                id="observacao" 
                name="observacao" 
                class="form-control"
                value="<?= isset($usuario) ? $usuario->getObservaca(): ''; ?>"
            >
        </div> 

		<div class="form-group">
            <label for="administrador">Administrador</label>
            <select class="form-control" id="administrador" name="administrador" >
				<option></option>
				<option <?= ((isset($usuario) and  $usuario->ehAdm()) ? 'selected': '') ; ?> value='S' >Sim</option>
				<option <?= ((isset($usuario) and !$usuario->ehAdm()) ? 'selected': '') ; ?> value='N' >Não</option>
			</select>
        </div> 

		<?php if (isset($usuario)) : ?>
		<div class="form-group">
            <label for="ativo">Ativo</label>
            <select class="form-control" id="ativo" name="ativo" >
				<option></option>
				<option <?= ((isset($usuario) and  $usuario->estaAtivo()) ? 'selected': '') ; ?> value='S' >Sim</option>
				<option <?= ((isset($usuario) and !$usuario->estaAtivo()) ? 'selected': '') ; ?> value='N' >Não</option>
			</select>
        </div> 
		<?php endif; ?>

        <button class="btn btn-primary">Salvar</button>
        <a href="/usuarios" class="btn btn-secondary">
            Voltar
        </a>
    </form>

<?php include __DIR__ . '/../fim-html.php'; ?>