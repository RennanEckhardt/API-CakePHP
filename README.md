# Introdução ao Projeto CakePHP

 API desenvolvida no CakePHP/Postman é uma api que  gerenciar informações de lojas e seus endereços associados. 

 Esta documentação fornecerá detalhes sobre os controllers, rotas, migrações e outros aspectos relevantes do projeto.
 https://drive.google.com/file/d/1MrJoynXFX5HuZaIMZMqlWv7KUi-8xljO/view?usp=share_link
 

## Inicialização do Projeto

1. **Configure o banco de dados:**
   Certifique-se de ter configurado o banco no arquivo 'app_local.php'

2. **Inicie servidor:**
   No terminal, navegue até o diretório raiz da pasta e rode o seguinte comando:

   ```bash
   cake server
   ```
   ## Usando a API
    Buscar as lojas cadastradas na tabela stores
    ```bash
    GET http://localhost:8765/stores
   ```
    Adiciona uma nova loja.
    ```bash
    POST http://localhost:8765/stores/add
    Parâmetros Exemplo:
    {
    "name": "joaoloja",
    }

   ```
    Edita o nome de uma loja.
    ```bash
    POST /http://localhost:8765/stores/edit
    Parâmetros Exemplo:
    {
    "name": "joaoloja",
    "id": 1
    }


   ```
    
    Deleta uma loja e seus dados, incluindo os da tabela ”address.”
   ```bash
     POST http://localhost:8765/stores/delete
       Parâmetros Exemplo:
    {
    "id": 24
    }


   ```
    Busca todos os dados da tabela ”addresses.”
   ```bash
   GET http://localhost:8765/Addresses/view
   ```
   Adiciona ou edita os dados da tabela ”addresses.” *Lembrando que so e possivel adicionar um endereço a uma loja ja existente.
   ```bash
   POST /http://localhost:8765/Addresses/add
   {
    "foreign_id": 24,
    "postal_code": "32140670",
    "street_number": "123",
    "complement": "casa amarela",
    "foreign_table": "Stores"
    }

   ```
