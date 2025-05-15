
# Mini ERP - Controle de Pedidos, Produtos, Cupons e Estoque

Este projeto é um **Mini ERP** desenvolvido como parte de um teste técnico para a vaga de Desenvolvedor Back-End PHP na Montink. Ele oferece funcionalidades essenciais para o gerenciamento de uma pequena operação de e-commerce, com integração de cupons, controle de estoque, carrinho de compras, frete e finalização de pedidos com envio de e-mail e webhook de status.

## 🚀 Tecnologias Utilizadas

- **PHP Puro** (seguindo padrão MVC)
- **MySQL**
- **HTML5**, **CSS3** (com uso de Bootstrap)
- **JavaScript** (incluindo jQuery)
- **Composer** – [https://getcomposer.org/download/](https://getcomposer.org/download/)
- Consumo de API externa (ViaCEP)
- Envio de e-mail via `email.php`

## 📂 Estrutura do Projeto

```
├── controllers/
│   ├── CarrinhoController.php
│   ├── CupomController.php
│   ├── PedidoController.php
│   ├── ProdutoController.php
│   └── WebhookController.php
│
├── models/
│   ├── Cupom.php
│   ├── Estoque.php
│   ├── Pedido.php
│   └── Produto.php
│
├── public/
│   ├── css/
│   │   └── cupons.css
│   ├── js/
│   │   └── cep.js
│   └── index.php 
│
├── views/
│   ├── index.php
│   ├── confirmacao.php
│   ├── cupons.php
│   └── finalizar.php
│
├── sql/
│   └── estrutura.sql 
│
├── carrinho.php
├── database.php
├── email.php
├── helpers.php
├── router.php
└── README.md
```

## ✅ Funcionalidades

- Cadastro e atualização de **produtos** com variações e controle de **estoque**.
- Criação e aplicação de **cupons** com regras de valor mínimo e validade.
- Adição de produtos ao **carrinho** com controle de estoque.
- Cálculo de **frete** baseado no subtotal:
  - R$ 20,00 se < R$52,00
  - R$ 15,00 entre R$52,00 e R$166,59
  - **Grátis** para subtotal ≥ R$200,00
- Validação de **CEP via ViaCEP**
- Finalização de **pedido** com envio de e-mail de confirmação
- Suporte a **webhook** para atualização ou cancelamento de pedidos

## 📦 Banco de Dados

Quatro tabelas principais:

- `produtos`
- `estoque`
- `cupons`
- `pedidos`

O script SQL para criação do banco está incluído no repositório.

## 🔄 Webhook

O arquivo `WebhookController.php` escuta requisições POST com `id` e `status` de pedido:
- `status = cancelado`: remove o pedido
- `status = finalizado` ou outro: atualiza status

## ✉️ Envio de E-mail

Após o pedido ser finalizado, um e-mail de confirmação é enviado ao cliente com os detalhes do pedido e o endereço de entrega.

## 🛠️ Instalação

1. Clone o repositório:
```bash
git clone https://github.com/LuisFurmiga/mini-erp.git
cd mini-erp
```

2. Configure o banco de dados em `database.php`
3. Certifique-se de ter um servidor local (como XAMPP ou Laragon) com PHP + MySQL.
4. Execute no navegador através de `localhost/mini-erp/index.php`

## 📌 Observações

- O projeto foi desenvolvido com foco em simplicidade e boas práticas de organização (MVC).
- Recomendado rodar localmente usando **XAMPP**.

## 🧠 Considerações Técnicas

- Código limpo e com separação de responsabilidades
- Scripts organizados por controladores, modelos e visões
- A lógica de aplicação está encapsulada nos controllers
- Utilização de sessão para controle do carrinho

---

Desenvolvido para o processo seletivo da [Montink](https://montink.com/)
