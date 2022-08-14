</div>

<?php if (isset($_SESSION['usuario']['instituicao'])) : ?>
<section>
	<footer class="text-center text-lg-start bg-light text-muted" style="margin-top: 30px; width: 100%; position: relative; bottom: 0;">
	<div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
		<?= $_SESSION['usuario']['instituicao']['nome'] ?>
	</div>
	</footer>
</section>
<?php endif; ?>

</body>
</html>