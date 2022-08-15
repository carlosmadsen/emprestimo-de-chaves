# emprestimo-de-chaves
Aplicação para gerenciar o empréstimo de chaves de salas de aula ou malex de bibliotecas.
## Requisitos:
Ter instalado o composer e o PHP (versão 7.3 ou superior) com com extensão do pdo_sqlite habilitada.
No windows tem de descomentar (remover o ;) a linha "extension=pdo_sqlite" no arquivo php.ini.
## Instalação:
No terminal: 
```
git clone https://github.com/carlosmadsen/emprestimo-de-chaves.git
cd emprestimo-de-chaves
composer install 
```
Criação do banco de dados sqlite (no windows).
```
.\vendor\bin\doctrine.bat orm:schema-tool:create
```
Para configurar a aplicação em outro servidor de banco de dados edite o arquivo "src\Infra\EntityManagerCreator.php".

Cadastrando Instituição:
```
php .\commands\cadastrar-instituicao.php <sigla> <nome>
```
Por exemplo: 
```
php .\commands\cadastrar-instituicao.php FURG "Universidade Federal do Rio Grande"
```
Cadastrando usuário administrador:
```
php .\commands\cadastrar-usuario-adm.php <login> <senha> <email> <nome> <sigla-instituicao>
```
Por exemplo: 
```
php .\commands\cadastrar-usuario-adm.php carlos senha123 carlos.fake@furg.br "Carlos Alberto Madsen" FURG
```
Inicializando o servidor web: 
```
php -S localhost:8080 -t public
```
## Exemplo de uso:
Acesse no seu navegador o endereço http://localhost:8080/ e se identifique com o login e senha definidos no comando "cadastrar-usuario-adm.php". 
## Dica:
Se estiver rodando no windows e ocorrer um erro na aplicação, pare o servidor web e rode o seguinte comando na pasta do projeto: 
```
.\vendor\bin\doctrine orm:generate-proxies
```
Em seguida reinicie o servidor web: 
```
php -S localhost:8080 -t public
```
