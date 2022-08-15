<?php
require __DIR__ . '/../inicio-html.php'; ?>

<a href="/nova-pessoa" class="btn btn-primary mb-2">
    Nova pessoa
</a>

<form  action="/pessoas" method="post" >

 <div class="form-row">

<?php if (!empty($labelIdentificacao) > 0) : ?>
  <div class="form-group col-md-2">
    <label for="identificacao"><?= $labelIdentificacao ?></label>
    <input type="text" class="form-control" id="identificacao" name="identificacao" value="<?= $identificacao; ?>" >
  </div>
<?php endif; ?>

  <div class="form-group col-md-2 ">
    <label for="documento"><?= $labelDocumento ?></label>
    <input type="text" class="form-control" id="documento" name="documento" value="<?= $documento; ?>" >
  </div>

  <div class="form-group col-md-2 ">
    <label for="nome">Nome</label>
    <input type="text" class="form-control" id="nome" name="nome" value="<?= $nome; ?>" >
  </div>

   <div class="form-group col-md-2">
     <label></label>
        <div>
            <button type="submit" class="btn btn-primary mb-2">Pesquisar</button>
            <?php if ($temPesquisa) : ?>
              <a href="/pessoas?limparFiltro=1" class="btn btn-secondary mb-2" >
              Limpar
              </a>
            <?php endif; ?>
        </div>
    </div>

</div>
</form>

<?php if ($temPesquisa) : ?>
	<?php if (count($pessoas) > 0) : ?>

	<table class="table  table-bordered">
	<thead >
		<tr>
			<?php if (!empty($labelIdentificacao) > 0) : ?>
			<th scope="col"><?= $labelIdentificacao ?></th>
			<?php endif; ?>
		<th scope="col"><?= $labelDocumento ?></th>
		<th scope="col">nome</th>
		<th scope="col">Operações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($pessoas as $pessoa): ?>
		<tr>
		<td><?= $pessoa->getNrIdentificacao(); ?></td>
		<td><?= $pessoa->getNrDocumento(); ?></td>
		<td><?= $pessoa->getNome(); ?></td>
		<td style="text-align:center;" >   
				<a href="/alterar-pessoa?id=<?= $pessoa->getId(); ?>" class="btn btn-info btn-sm">
					Alterar
				</a>
				<a href="/remover-pessoa?id=<?= $pessoa->getId(); ?>" class="btn btn-danger btn-sm">
					Remover
				</a>        
		</td>
		</tr>
		<?php endforeach; ?>    
	</tbody>
	</table>
	
	<?php else: ?>

		<div class="alert alert-info">
		Não foi encontrada nenhuma pessoa.
		</div>

	<?php endif; //count pessoas ?>

<?php else: ?>

	<div class="alert alert-info">
		Por favor, pesquise por algum dos campos acima.
	</div>

<?php endif; //temPesquisa ?>


<?php
require __DIR__ . '/../fim-html.php';