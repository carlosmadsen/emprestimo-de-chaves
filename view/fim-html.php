</div>

<?php if (isset($_SESSION['rodape'])) : ?>
<section>
	<footer class="text-center text-lg-start bg-light text-muted" style="width: 100%; position: absolute; bottom: 0;">
	<div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
		<?= $_SESSION['rodape'] ?>
	</div>
	</footer>
</section>
<?php endif; ?>

</body>
</html>