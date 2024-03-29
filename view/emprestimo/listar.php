<?php
require __DIR__ . '/../inicio-html.php'; ?>

<?php if (count($predios) > 0) : ?>

	<a href="/novo-emprestimo" class="btn btn-primary mb-2">
		Novo empréstimo
	</a>
	<form  action="/emprestimos?filtrar=1" method="post" >
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

	
	<div class="form-group col-md-2">
		<label></label>
			<div>
				<button type="submit" class="btn btn-primary mb-2">Filtrar</button>
				<?php if ($temPesquisa) : ?>
				<a href="/emprestimos?limparFiltro=1" class="btn btn-secondary mb-2" >
				Limpar
				</a>
				<?php endif; ?>
			</div>
		</div>

	</div>
	</form>

	<?php if (!empty($idPredio)) : ?>
		<?php if (count($emprestimos) > 0) : ?>

			<table class="table  table-bordered">
			<thead >
				<tr>
					<th scope="col">Nº Chave</th>
					<th scope="col">Nome</th>
					<th scope="col">Data e Hora</th>
					<th scope="col">Operações</th>
				</tr>
			</thead>
			<tbody>
				
			<?php foreach ($emprestimos as $emprestimo): ?>
				<tr>
					<td><?= $emprestimo->getChave()->getNumero(); ?></td>
					<td><?= $emprestimo->getPessoa()->getNome(); ?></td>
					<td style="text-align:center;" ><?=  $emprestimo->getDtEmprestimo()->format('d/m/Y H:i:s'); ?></td> 
					<td style="text-align:center;" > 				
						<a href="/devolver?id=<?= $emprestimo->getId(); ?>" class="btn btn-danger btn-sm">
							Devolver
						</a>        
					</td>
				</tr>
			<?php endforeach; ?>    

			</tbody>
			</table>
		
		<?php else: ?>

			<div class="alert alert-info">
				Não foi encontrado nenhum empréstimo.
			</div>

		<?php endif; ?>

	<?php else: ?>

	<div class="alert alert-info">
	Por favor, selecione um prédio.
	</div>

	<?php endif; ?>


<?php else: ?>
	<div class="alert alert-info">
	Seu usuário não está relacionado a nenhum prédio ativo.
	</div>
<?php endif; ?>

<?php
require __DIR__ . '/../fim-html.php';
