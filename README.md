# API de gerenciamento de dispositivos IoT
## Contexto
Implementar em PHP >= 7.2 um sistema de cadastro de escolas, turmas e alunos.

## Funcionalidades
- CRUD de Escolas, turmas e alunos

## Tecnologias

- Laravel/ Laravel Sail - Uma interface de linha de comando leve para interagir com o ambiente de desenvolvimento Docker padrão do Laravel. Sail fornece um excelente ponto de partida para construir um aplicativo Laravel usando PHP, MySQL
- Jetstream/Inertia -  Oferece todo o poder do Vue.js sem a complexidade do roteamento do lado do cliente
- Docker - O Docker permite que você separe seus aplicativos de sua infraestrutura para que você possa entregar software rapidamente
- MySQL - Um sistema de gerenciamento de banco de dados (SGBD), que utiliza a linguagem SQL (Linguagem de Consulta Estruturada, do inglês Structured Query Language) como interface

## Instalação

##### Requisito obrigátorio
Antes de tudo você precisa ter o docker e o docker-compose e também o git.
Caso não tenha instalado, aqui alguns links de referência:
- Aqui encontra os passos para instalação do Docker => https://docs.docker.com/get-docker/ 
- Aqui encontra os passos para instalação do Docker Compose => https://docs.docker.com/compose/ 
- Aqui encontra os passos para instalação do git => https://git-scm.com/book/en/v2/Getting-Started-Installing-Git

##### Clone o projeto
Com o git instalado e em um diretório da sua escolha, baixe o projeto e acesse seu diretório:

```sh
git clone https://github.com/YolandoQ/school-network-manager.git
```

```sh
cd school-network-manager
```

Aproveite e configure o .env, pro nosso caso basta copiar o .example:
```sh
cp .env.example .env
```

##### Suba o serviço
Nesse momento, você precisa instalar as dependências do projeto pra conseguir utilizar o laravel sail, execute esses comandos:

```sh
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

```sh
./vendor/bin/sail up -d
```

Se houver qualquer conflito de portas você pode comentar, alterar ou apagar a linha correspondente a porta conflituosa no arquivo docker-compose.yml na raiz do projeto:

```sh
        ports:
            - '8888:80' 
            - '${APP_PORT:-80}:80'
            - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'
```
       
##### Configurações necessárias
Com o serviço rodando precisamos configurar e instalar algumas coisas, então execute os seguintes comandos:

```sh
./vendor/bin/sail artisan key:generate
```

```sh
./vendor/bin/sail artisan migrate
```

```sh
./vendor/bin/sail npm install
```

```sh
./vendor/bin/sail npm run dev
```

Feito isso, o sistema deve estar rodando no seu localhost nas portas:

- localhost:8888
- localhost
- localhost:5173

Preparei uma seeder pra teste, caso julgue necessário, popula o banco de dados com alguns registros:

```sh
./vendor/bin/sail artisan db:seed --class="TestSeeder"
```
