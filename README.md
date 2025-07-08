# API de Simulação de Comissão de Vendas

**Laravel 11**.

## 1. Estrutura do Projeto

Minha prioridade na arquitetura foi seguir os princípios **SOLID** para ter um código limpo, fácil de testar e de dar manutenção. A organização ficou assim:

-   `app/Http/Controllers/Api/SaleController.php`
-   `app/Services/CommissionService.php`
-   `app/Services/SaleStorageService.php`
-   `app/Http/Requests/StoreSaleRequest.php`
-   `routes/api.php`

A lógica de negócio mais importante, o cálculo das comissões, ficou isolada no `CommissionService` assim usando o conceito de SRP.

## 2. Minhas Decisões em Pontos-Chave

O desafio deixou algumas questões em aberto. Abaixo, explico as decisões que tomei:

#### a. Como Salvar os Dados?

-   **O Problema:** O requisito pedia para simular a persistência de dados sem um banco de dados real.
-   **Minha Solução:** Optei por usar um **arquivo JSON** para guardar as simulações.
-   **Por quê?** Arrays em memória seriam perdidos entre as requisições. O arquivo JSON garantiu uma persistência real para que os endpoints `GET` e `DELETE` funcionassem corretamente.

#### b. Como Gerar IDs Únicos?

-   **O Problema:** Com um endpoint `DELETE /sales/{id}`, eu precisava de um ID único para cada venda, mas não tinha um banco com auto-incremento.
-   **Minha Solução:** Usei o helper `Str::uuid()` do Laravel.
-   **Por quê?** UUID é a solução padrão da indústria para gerar IDs únicos em cenários como este, garantindo unicidade sem depender de um estado central.

#### c. E se Algo Der Errado?

-   **O Problema:** O desafio focava nos caminhos de sucesso, mas uma boa API precisa tratar bem os erros.
-   **Minha Solução:** Implementei o tratamento de erros seguindo os padrões de APIs REST.
-   **Por quê?** A API retorna erros `422` para dados de entrada inválidos e `404` para recursos não encontrados, tornando-a mais previsível e robusta.

## 3. Como Rodar o Projeto

Para rodar o projeto na sua máquina, siga estes passos:

**Você vai precisar de:** PHP >= 8.2 e Composer.

1.  **Clone o projeto:**
    ```bash
    git clone [https://github.com/Hentadin/-laravel-comiss-o-api.git](https://github.com/Hentadin/-laravel-comiss-o-api.git)
    cd -laravel-comiss-o-api
    ```

2.  **Instale as dependências:**
    ```bash
    composer install
    ```

3.  **Configure o ambiente:**
    *Este projeto foi feito com Laravel 11. Os passos abaixo preparam o ambiente corretamente.*
    ```bash
    # Copia o arquivo de ambiente
    cp .env.example .env

    # Gera a chave da aplicação
    php artisan key:generate

    # Prepara as rotas de API
    php artisan install:api

    # Cria o nosso "banco de dados" em JSON
    touch storage/app/sales.json
    echo "[]" > storage/app/sales.json
    ```

4.  **Inicie o servidor:**
    ```bash
    php artisan serve
    ```
    Pronto! A API estará rodando em `http://127.0.0.1:8000`.

## 4. Como Usar a API (Endpoints)

A URL base para acessar a API, ao rodar localmente, é `http://127.0.0.1:8000/api`. Todos os endpoints abaixo são relativos a essa URL.

---

### **1. Registrar uma Simulação de Venda**

-   **Endpoint:** `POST /api/sales`
-   **Descrição:** Cria uma nova simulação de venda com base no valor total e no tipo, e retorna os dados completos com as comissões calculadas e um ID único.

-   **Exemplo `cURL`:**
    ```bash
    curl -X POST [http://127.0.0.1:8000/api/sales](http://127.0.0.1:8000/api/sales) \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{
        "valor_total": 1500,
        "tipo_venda": "afiliada"
    }'
    ```

-   **Resposta de Sucesso (201 Created):**
    ```json
    {
        "valor_total": 1500,
        "tipo_venda": "afiliada",
        "comissoes": {
            "plataforma": 150,
            "produtor": 900,
            "afiliado": 450
        },
        "id": "9c8e1a1b-1b1c-4b1d-8e1f-1a1b1c1d1e1f"
    }
    ```

---

### **2. Listar Todas as Simulações**

-   **Endpoint:** `GET /api/sales`
-   **Descrição:** Retorna uma lista com todas as simulações de vendas que foram registradas.

-   **Exemplo `cURL`:**
    ```bash
    curl -X GET [http://127.0.0.1:8000/api/sales](http://127.0.0.1:8000/api/sales)
    ```

-   **Resposta de Sucesso (200 OK):**
    ```json
    [
        {
            "valor_total": 1000,
            "tipo_venda": "direta",
            "comissoes": {
                "plataforma": 100,
                "produtor": 900,
                "afiliado": 0
            },
            "id": "a1b2c3d4-e5f6-a7b8-c9d0-e1f2a3b4c5d6"
        },
        {
            "valor_total": 1500,
            "tipo_venda": "afiliada",
            "comissoes": {
                "plataforma": 150,
                "produtor": 900,
                "afiliado": 450
            },
            "id": "9c8e1a1b-1b1c-4b1d-8e1f-1a1b1c1d1e1f"
        }
    ]
    ```

---

### **3. Remover uma Simulação**

-   **Endpoint:** `DELETE /api/sales/{id}`
-   **Descrição:** Apaga permanentemente uma simulação de venda com base no seu ID.

-   **Exemplo `cURL`:**
    *Primeiro, copie um `id` da resposta do endpoint de listagem (`GET /api/sales`).*
    ```bash
    curl -X DELETE [http://127.0.0.1:8000/api/sales/9c8e1a1b-1b1c-4b1d-8e1f-1a1b1c1d1e1f](http://127.0.0.1:8000/api/sales/9c8e1a1b-1b1c-4b1d-8e1f-1a1b1c1d1e1f)
    ```

-   **Resposta de Sucesso (204 No Content):**
    -   A API retornará uma resposta vazia com o status `204`, que significa que a operação foi bem-sucedida e não há conteúdo a ser exibido. Se o ID não for encontrado, retornará um erro `404 Not Found`.


## 5. Algumas Notas Finais

-   **Escalabilidade:** A arquitetura com Services foi pensada para facilitar a escalabilidade. Se for preciso usar um banco de dados no futuro, basta trocar a implementação do `SaleStorageService` sem precisar alterar outras partes do código.

-   **Testabilidade:** Isolar a lógica em classes de serviço também facilita (e muito) a criação de testes unitários, permitindo validar as regras de negócio de forma rápida e independente do resto do framework.
