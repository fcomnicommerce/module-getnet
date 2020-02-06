# Método de Pagamento da Getnet para Magento 2

## Requisitos
Magento 2.x

## Recursos

 - Cartão de Crédito
 - Device Fingerprint

## Instalação

    composer require fcamara/module-getnet
    php bin/magento setup:upgrade

## Testando o módulo em ambiente Sandbox:

**Criando uma conta na Getnet (Sandbox)**
Após instalar o módulo, crie uma conta no ambiente sandbox da getnet clicando [aqui](https://developers.getnet.com.br/register).

**Configurando o método de pagamento no Magento**

**Habilitando o método de pagamento:**

 - Loja > Configuração > Vendas > Métodos de Pagamento > Getnet
	 - Geral 
		 - **Ativar** selecione "Sim"
		 - **Ambiente** selecione "Sandbox"
		 - **Sandbox Endpoint** incluir "https://api-sandbox.getnet.com.br/"
		 - **Fingerprint Endpoint** incluir "https://h.online-metrix.net/fp/tags"
		 - **Fingerprint Sandbox Org Id** incluir "1snn5n9w"
		 - **CPF/CNPJ** Configure o atributo taxvat do cliente
		 - **Street** Configure as linhas de endereço corretamente de acordo com a sua loja
	-	Credenciais
		-	**Seller Id** Consulte sua conta getnet
		-	**Client Id** Consulte sua conta getnet
		-	**Client Secret** Consulte sua conta getnet
	- Métodos
		- **Ativar** incluir "Sim"

**Mostrar atributo taxvat no cadastro de cliente:**

 - Loja > Configuração > Clientes > Configuração de Cliente > Criar novas opções de conta
	 - **Mostrar número VAT no Frontend** selecione "Yes"

**Limpe os caches!**
