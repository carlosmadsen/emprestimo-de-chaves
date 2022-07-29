<?php
require __DIR__ . '/../inicio-html.php'; ?>

<a href="/novo-predio" class="btn btn-primary mb-2">
    Novo prédio
</a>



<form  action="/predios" method="post" >

 <div class="form-row">

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

   <div class="form-group col-md-2">
     <label></label>
        <div>
            <button type="submit" class="btn btn-primary mb-2">Pesquisar</button>
            <?php if ($temPesquisa) : ?>
              <a href="/predios" class="btn btn-secondary mb-2" >
              Limpar
              </a>
            <?php endif; ?>
        </div>
    </div>

</div>
</form>

<?php if (count($predios) > 0) : ?>

 <table class="table  table-bordered">
  <thead >
    <tr>
      <th scope="col">Nome</th>
      <th scope="col">Ativo</th>
      <th scope="col">Nº Usuários</th>
      <th scope="col">Operações</th>
    </tr>
  </thead>
  <tbody>
    
    <?php foreach ($predios as $predio): ?>
    <tr>
      <td><?= $predio->getNome(); ?></td>
      <td style="text-align:center;" ><?= ($predio->estaAtivo() ? 'Sim' : 'Não'); ?></td> 
      <td><?= count($predio->getUsuarios()); ?> </td>     
      <td style="text-align:center;" >   
            <a href="/alterar-predio?id=<?= $predio->getId(); ?>" class="btn btn-info btn-sm">
                Alterar
            </a>
            <a href="/remover-predio?id=<?= $predio->getId(); ?>" class="btn btn-danger btn-sm">
                Remover
            </a>        
      </td>
    </tr>
    <?php endforeach; ?>    

  </tbody>
</table>
  
<?php else: ?>

<div class="alert alert-info">
  Não foi encontrado nenhum prédio.
</div>

<?php endif; ?>


<?php
require __DIR__ . '/../fim-html.php';