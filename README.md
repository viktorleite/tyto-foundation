### Tyto Foundation

**Um kernel de inicialização (bootstrap) minimalista, determinístico e modular para aplicações PHP modernas**, projetado para prover uma fundação sólida, segura e agnóstica de contexto antes de despachar a execução para roteadores, APIs, CLIs ou workers.

---

### Sumário
* [x] [Sobre o Projeto](#sobre-o-projeto)
* [x] [Pré-requisitos](#pré-requisitos)
* [x] [Instalação](#instalação)
* [x] [Uso](#uso)
* [x] [Documentação](#documentação)
* [x] [Roadmap](#roadmap)
* [x] [Contribuindo](#contribuindo)
* [ ] [Agradecimento](#agradecimento)
* [ ] [Colaboradores](#colaboradores)
* [x] [Licença](#licença)
* [x] [Contato](#contato)

---

### Sobre o Projeto

O **Tyto Foundation** é a fundação central do ecossistema Tyto PHP. Ele resolve a inicialização estrutural e rigorosa do ambiente PHP (guardrails de versão, extensões obrigatórias, configurações de runtime, timezone, locale, tratamento customizado de erros e integração robusta de logs em disco) separando totalmente a infraestrutura de boot (`boot()`) da execução do processo (`run()`). Através do padrão Fluent Builder e suporte a herança (Open/Closed Principle), permite que aplicações específicas — como o `HttpApplication` — estendam o kernel de forma limpa e modular.

---

### Pré-requisitos

Para utilizar este pacote, você precisa ter o ambiente configurado com:
* **PHP**: `>= 8.5`
* **Composer** instalado globalmente.

---

### Instalação

Adicione o pacote ao seu projeto via Composer:

```bash
composer require tyto-php/foundation

```

Ou inclua-o localmente em desenvolvimento mapeando o repositório via path no seu `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "../caminho/para/tyto-foundation"
    }
],
"require": {
    "tyto-php/foundation": "*"
}

```

---

### Uso

Exemplo de configuração do ponto de entrada (`public/index.php`) e do arquivo de inicialização (`bootstrap/app.php`):

```php
<?php

declare(strict_types=1);

// 1. Métrica de alta precisão
define('APP_START_TIME', microtime(true));

// Carrega o Bootstrap do Kernel
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';

// Despacha a execução da aplicação
$app->run(function ($app) {
    // Integração com roteadores, controllers ou requisições HTTP
});

```

E no seu arquivo `bootstrap/app.php`:

```php
<?php

declare(strict_types=1);

use Tyto\Foundation\Application;

$rootPath = dirname(__DIR__);

require_once $rootPath . '/vendor/autoload.php';

$config = require_once$rootPath . '/config/app.php';

return Application::init($rootPath)
    ->withDebug((bool) ($config['debug'] ?? false))
    ->withConfig($config);

```

Para mais exemplos, consulte a [Documentação]().

---

### Documentação

A documentação detalhada da API, ciclo de vida do bootstrap, regras de extensão e tratadores de erro pode ser encontrada no arquivo [DOCUMENTATION.md]() na raiz do projeto.

---

### Roadmap

* [x] Versão 0.1.0: Kernel de inicialização agnóstico, Singleton e Builder fluente.
* [x] Guardrails de versão do PHP e relatórios de extensões ausentes.
* [x] Integração de logs determinísticos em disco (`storage/logs/php.log`).
* [ ] Suporte avançado a hooks de ciclo de vida e eventos de shutdown.

---

### Contribuindo

Se você tiver uma sugestão para melhorar o projeto, faça um fork do repositório e crie uma pull request. Você também pode simplesmente abrir uma **issue** com a tag "melhoria". Não se esqueça de dar uma estrela ao projeto! Quaisquer contribuições que você fizer são **muito apreciadas.**

1. Fork do projeto
2. Crie sua branch de recurso (`git checkout -b feature/AmazingFeature`)
3. Confirme suas alterações (`git commit -m 'Add some AmazingFeature'`)
4. Empurre para a Filial (`git push origin feature/AmazingFeature`)
5. Abra uma **pull request**

---

### Agradecimento

Agradecimentos à comunidade PHP, PHP-FIG e aos colaboradores do ecossistema Tyto PHP.

---

### Contato

Viktor Leite - @ViktorLeite - contato@viktorleite.com

Website - [viktorleite.com.br](https://viktorleite.com.br)

Link do projeto: https://github.com/viktorleite/tyto-foundation

---

### Licença

Distribuído sob a licença MIT. Veja `LICENSE.txt` para mais informações.
O Projeto está disponível sob a Licença MIT. O Projeto também inclui bibliotecas externas que estão disponíveis sob várias licenças. Consulte `LICENSE` para obter mais informações.
