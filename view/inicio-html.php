<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

<?php if (isset($_SESSION['usuario'])) : ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="/instituicao">Instituição</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/usuarios">Usuários</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/predios">Prédios</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/chaves">Chaves</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="/emprestimos">Empréstimos</a>
        </li>
      </ul>   
         
     <ul class="navbar-nav ml-auto">
        <li class="nav-item">
               <a class="nav-link" href="/logout">Sair</a>
        </li>
    </ul>

    </div>
</nav>
<?php endif; ?>

<div class="container" >
    <div style="margin-top: 25px; margin-bottom: 25px;" >
        <h2><?= $titulo; ?></h2>
    </div>

     <?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert alert-<?= $_SESSION['tipo_mensagem']; ?>">
        <?= $_SESSION['mensagem']; ?>
    </div>
    <?php
        unset($_SESSION['mensagem']);
        unset($_SESSION['tipo_mensagem']);
     endif;
    ?>

