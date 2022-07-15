<?php include __DIR__ . '/../inicio-html.php'; ?>


<main class="login-form">
    <div class="cotainer">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Empréstimo de chaves</div>
                    <div class="card-body">
                         <form action="/realiza-login" method="post">

                            <div class="form-group row">
                                <label for="email_address" class="col-md-4 col-form-label text-md-right">Login:</label>
                                <div class="col-md-6">
                                     <input type="login" name="login" id="login" class="form-control" required="required" autofocus >
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">Senha:</label>
                                <div class="col-md-6">
                                    <input type="password" name="senha" id="senha" class="form-control" required="required" >
                                </div>
                            </div>
                    
                            <div class="form-group row">
                                <div class="col-md-4 "></div>
                                <div class="col-md-8 " style="text-align:left;">
                                    <button class="btn btn-primary" style="width:120px;">
                                        Entrar
                                    </button>
                                </div>
                            </div>

                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

</main>


<?php include __DIR__ . '/../fim-html.php'; ?>