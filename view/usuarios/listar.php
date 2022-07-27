<?php
require __DIR__ . '/../inicio-html.php'; ?>

<a href="/novo-usuario" class="btn btn-primary mb-2">
    Novo usuário
</a>

<form  action="/usuarios" method="post" >

 <div class="form-row">

  <div class="form-group col-md-2">
    <label for="login">Login</label>
    <input type="text" class="form-control" id="login" name="login" value="<?= $login; ?>" >
  </div>

  <div class="form-group col-md-2 ">
    <label for="nome">Nome</label>
    <input type="text" class="form-control" id="nome" name="nome" value="<?= $nome; ?>" >
  </div>

<div class="form-group col-md-2 ">
    <label for="ativo">Ativo</label>
    <select class="form-control" id="ativo" name="ativo"> 
      <option></option>
      <option <?= ($ativo == 'S' ? 'selected' : '' ) ?> value='S' >Sim</option>
      <option <?= ($ativo == 'N' ? 'selected' : '' ) ?> value='N' >Não</option>
    </select>
  </div>

  <div class="form-group col-md-2 ">
    <label for="administrador">Administrador</label>
    <select class="form-control" id="administrador" name="administrador" >
      <option></option>
      <option <?= ($administrador == 'S' ? 'selected' : '' ) ?> value='S' >Sim</option>
      <option <?= ($administrador == 'N' ? 'selected' : '' ) ?> value='N' >Não</option>
    </select>
  </div>

   <div class="form-group col-md-2">
     <label></label>
        <div>
            <button type="submit" class="btn btn-primary mb-2">Pesquisar</button>
            <?php if ($temPesquisa) : ?>
              <a href="/usuarios" class="btn btn-secondary mb-2" >
              Limpar
              </a>
            <?php endif; ?>
        </div>
    </div>

</div>
</form>

<?php if (count($usuarios) > 0) : ?>

 <table class="table  table-bordered">
  <thead >
    <tr>
      <th scope="col">Login</th>
      <th scope="col">Nome</th>
      <th scope="col">Adiministrador</th>
      <th scope="col">Ativo</th>
      <th scope="col">Operações</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($usuarios as $usuario): ?>
    <tr>
      <th><?= $usuario->getLogin(); ?></th>
      <td><?= $usuario->getNome(); ?></td>
      <td style="text-align:center;" ><?= ($usuario->ehAdm() ? 'Sim' : 'Não'); ?></td>
      <td style="text-align:center;" ><?= ($usuario->estaAtivo() ? 'Sim' : 'Não'); ?></td>
      <th style="text-align:center;" >   
            <a href="/alterar-usuario?id=<?= $usuario->getId(); ?>" class="btn btn-info btn-sm">
                Alterar
            </a>
            <a href="/remover-usuario?id=<?= $usuario->getId(); ?>" class="btn btn-danger btn-sm">
                Remover
            </a>        
      </th>
    </tr>
    <?php endforeach; ?>    
  </tbody>
</table>
  
<?php else: ?>

<div class="alert alert-info">
  Não encontrado nenhum usuário.
</div>

<?php endif; ?>

<?php
require __DIR__ . '/../fim-html.php';