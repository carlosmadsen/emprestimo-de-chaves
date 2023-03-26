<?php
require __DIR__ . '/../inicio-html.php'; ?>

	<form  action="/historicos?filtrar=1" method="post" >

	<div class="form-row">		
	

	<div class="form-group col-md-2 ">
		<label for="nome">Número da Chave</label>
		<input type="text" class="form-control" id="numero" name="numeroChave" value="<?= $numeroChave; ?>" >
	</div>	

	<div class="form-group col-md-2">
		<label></label>
			<div>
				<button type="submit" class="btn btn-primary mb-2">Filtrar</button>
				<?php if ($temPesquisa) : ?>
				<a href="/historicos?limparFiltro=1" class="btn btn-secondary mb-2" >
				Limpar
				</a>
				<?php endif; ?>
			</div>
		</div>

	</div>
	</form>

	<?php if ($temPesquisa) : ?>
		<?php if (count($historicos) > 0) : ?>

			<table class="table  table-bordered">
			<thead >
				<tr>
				<th scope="col">Empréstimo</th>
				<th scope="col">Devolução</th>
				<th scope="col">Número da Chave</th>
				<th scope="col">Prédio</th>
				<th scope="col">Pessoa</th>
				</tr>
			</thead>
			<tbody>
				
				<?php foreach ($historicos as $historico): ?>
				<tr>
					<td>
						<?= 
							$historico->getDtEmprestimo()->format('d/m/Y H:i:s').'</br>'.
							$historico->getLoginUsuarioEmprestimo().' - '.
							$historico->getNomeUsuarioEmprestimo();						
						?></td>
					<td>
						<?= 
							(
								$historico->foiDevolvida() ? 
								$historico->getDtDevolucao()->format('d/m/Y H:i:s').'</br>'.
								$historico->getLoginUsuarioDevolucao().' - '.
								$historico->getNomeUsuarioDevolucao()
								: 
								''
							); 
						?>
					</td>
					<td><?= $historico->getNumeroChave(); ?></td>				
					<td><?= $historico->getNomePredio(); ?></td>				
					<td><?= $historico->getNomePessoa(); ?></td>				
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
	Por favor, pesquise por algum dos campos acima.
	</div>

	<?php endif; ?>




<?php
require __DIR__ . '/../fim-html.php';
