<?php
require __DIR__ . '/../inicio-html.php'; ?>

<?php if (count($predios) > 0) : ?>

	<a href="/nova-chave" class="btn btn-primary mb-2">
		Nova chave
	</a>

	<form  action="/chaves" method="post" >

	<div class="form-row">

	
		<div class="form-group col-md-2 ">
		<label for="predio">Prédio</label>
		<select class="form-control" id="predio" name="predio" >
			<option></option>       
			<?php foreach ($predios as $predio): ?>
			<option value="<?= $predio->getId(); ?>" <?= ($predio->getId() == $idPredio ? 'selected' : ''); ?> ><?= $predio->getNome(); ?></option>
			<?php endforeach; ?>        
		</select>
		</div>
	

	<div class="form-group col-md-2 ">
		<label for="nome">Número</label>
		<input type="text" class="form-control" id="numero" name="numero" value="<?= $numero; ?>" >
	</div>

	<div class="form-group col-md-2 ">
		<label for="nome">Descrição</label>
		<input type="text" class="form-control" id="descricao" name="descricao" value="<?= $descricao; ?>" >
	</div>

	<div class="form-group col-md-2 ">
		<label for="ativo">Ativo</label>
		<select class="form-control" id="ativo" name="ativo"> 
		<option></option>
		<option <?= ($ativo == 'S' ? 'selected' : '') ?> value='S' >Sim</option>
		<option <?= ($ativo == 'N' ? 'selected' : '') ?> value='N' >Não</option>
		</select>
	</div>

	<div class="form-group col-md-2">
		<label></label>
			<div>
				<button type="submit" class="btn btn-primary mb-2">Pesquisar</button>
				<?php if ($temPesquisa) : ?>
				<a href="/chaves" class="btn btn-secondary mb-2" >
				Limpar
				</a>
				<?php endif; ?>
			</div>
		</div>

	</div>
	</form>

	<?php if (!empty($idPredio)) : ?>
		<?php if (count($chaves) > 0) : ?>

			<table class="table  table-bordered">
			<thead >
				<tr>
				<th scope="col">Número</th>
				<th scope="col">Descrição</th>
				<th scope="col">Ativo</th>
				<th scope="col">Operações</th>
				</tr>
			</thead>
			<tbody>
				
				<?php foreach ($chaves as $chave): ?>
				<tr>
				<td><?= $chave->getNumero(); ?></td>
				<td><?= $chave->getDescricao(); ?></td>
				<td style="text-align:center;" ><?= ($chave->estaAtivo() ? 'Sim' : 'Não'); ?></td> 
				<td style="text-align:center;" >   
						<a href="/alterar-chave?id=<?= $chave->getId(); ?>" class="btn btn-info btn-sm">
							Alterar
						</a>
						<a href="/remover-chave?id=<?= $chave->getId(); ?>" class="btn btn-danger btn-sm">
							Remover
						</a>        
				</td>
				</tr>
				<?php endforeach; ?>    

			</tbody>
			</table>
		
		<?php else: ?>

			<div class="alert alert-info">
				Não foi encontrado nenhuma chave.
			</div>

		<?php endif; ?>

	<?php else: ?>

	<div class="alert alert-info">
	Por favor, selecione um prédio.
	</div>

	<?php endif; ?>


<?php else: ?>

	<div class="alert alert-info">
	No momento não há nenhum prédio cadastrado.
	</div>

<?php endif; ?>

<?php
require __DIR__ . '/../fim-html.php';
