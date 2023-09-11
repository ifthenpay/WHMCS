# Atenção
O seguinte manual foi criado para a versão 1.3.0 da extensão ifthenpay para o WHMCS 8.


Download de versões da extensão ifthenpay para WHMCS.
|                            | WHMCS 8                                                                                             |
|----------------------------|-----------------------------------------------------------------------------------------------------|
| Link para descarregar os ficheiros de instalação | [ifthenpay v1.3.0](https://github.com/ifthenpay/WHMCS/releases/download/1.3.0/ifthenpay.zip) |

</br>
</br>

# Extensão de pagamentos ifthenpay para WHMCS 8

Ler em ![Português](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/pt.png) [Português](README.pt.md), e ![Inglês](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/en.png) [Inglês](README.md)

[1. Introdução](#introdução)

[2. Compatibilidade](#compatibilidade)

[3. Instalação](#instalação)

[4. Configuração](#configuração)
  * [Chave Backoffice](#chave-backoffice)
  * [Multibanco](#multibanco)
  * [Multibanco com Referências Dinâmicas](#multibanco-com-referências-dinâmicas)
  * [MB WAY](#mb-way)
  * [Cartão de Crédito](#cartão-de-crédito)
  * [Payshop](#payshop)

[5. Outros](#outros)
  * [Suporte](#suporte)
  * [Requerer criação de conta adicional](#requerer-criação-de-conta-adicional)
  * [Limpeza de Configuração](#limpeza-de-configuração)
  * [Callback](#callback)
  * [Cronjob](#cronjob)


[6. Experiência do Utilizador Consumidor](#experiência-do-utilizador-consumidor)
  * [Pagar encomenda com Multibanco](#pagar-encomenda-com-multibanco)
  * [Pagar encomenda com Payshop](#pagar-encomenda-com-payshop)
  * [Pagar encomenda com MB WAY](#pagar-encomenda-com-mb-way)
  * [Pagar encomenda com Credit Card](#pagar-encomenda-com-credit-card)


</br>

# Introdução
![ifthenpay](https://ifthenpay.com/images/all_payments_logo_final.png)

</br>

**Esta é a extensão ifthenpay para a plataforma WHMCS.**

**Multibanco** é um método de pagamento que permite ao consumidor pagar com referência bancária. Este extensão permite gerar referências de pagamento que o consumidor pode usar para pagar a sua encomenda numa caixa multibanco ou num serviço online de Home Banking. Este plugin usa a ifthenpay, uma das várias gateways disponíveis em Portugal.

**MB WAY** é a primeira solução inter-bancos que permite a compra e transferência imediata por via de smartphone e tablet. Este extensão permite gerar um pedido de pagamento ao smartphone do consumidor, e este pode autorizar o pagamento da sua encomenda na aplicação MB WAY. Este plugin usa a ifthenpay, uma das várias gateways disponíveis em Portugal.

**Payshop** é um método de pagamento que permite ao consumidor pagar com referência Payshop. Este extensão permite gerar uma referência de pagamento que o consumidor pode usar para pagar a sua encomenda num agente Payshop ou CTT. Este plugin usa a ifthenpay, uma das várias gateways disponíveis em Portugal.

**Credit Card** Esta extensão permite gerar um pagamento por Visa ou Master card, que o consumidor pode usar para pagar a sua encomenda. Este plugin usa a ifthenpay, uma das várias gateways disponíveis em Portugal.

**É necessário contrato com a ifthenpay**

Mais informações em [ifthenpay](https://ifthenpay.com). 

Adesão em [Adesão ifthenpay](https://www.ifthenpay.com/aderir/).

**Suporte**

Para suporte, por favor crie um ticked para suporte em [Suporte ifthenpay](https://helpdesk.ifthenpay.com/).

</br>

# Compatibilidade

Use a tabela abaixo para verificar a compatibilidade do extensão ifthenpay com a sua loja online:
|                           | WHMCS 8 [8.1.0 - 8.7.3] |
|---------------------------|-----------------------------|
| ifthenpay v1.3.0          | Compatível                  |

</br>


# Instalação
Descarregue o ficheiro de instalação do módulo ifthenpay para o WHMCS 8 a partir da página do GitHub [ifthenpay-whmcs](https://github.com/ifthenpay/WHMCS/releases).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/download_installer.png)
</br>

Descompacte o ficheiro descarregado e cole o seu conteúdo na raiz da sua loja online.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/upload_files.png)
</br>

Após fazer o upload dos ficheiros, aceda ao painel de administração da sua loja online e vá para Configuração (1) -> Aplicações e Integrações (2) -> Procurar (3) -> Pagamentos (4).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/backoffice_payments.png)
</br>

Procure o método de pagamento do módulo ifthenpay que deseja ativar (por exemplo, Multibanco) e clique em Ativar (1).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/activate_method.png)
</br>


# Configuração

Após a instalação do módulo e ativação de um método de pagamento, será redirecionado para a página de configuração do mesmo, onde deverá inserir os detalhes da sua conta ifthenpay.
A página de configuração também pode ser acedida selecionando Pagamentos -> Gateways de Pagamento no menu de navegação à esquerda e escolhendo o método de pagamento ativo que deseja configurar. Também pode aceder a esta página através de Aplicações e Integrações -> selecionando a aba Ativo e escolhendo o método de pagamento que deseja configurar.

</br>


## Chave Backoffice

Cada configuração de método de pagamento requer a introdução da Chave de Backoffice para carregar as contas disponíveis. A Chave de Backoffice é fornecida após a conclusão do contrato e consiste em conjuntos de quatro dígitos separados por um hífen (-).
Aqui está um exemplo para o Multibanco, e este procedimento é o mesmo para outros métodos de pagamento também.
Introduza a Chave de Backoffice (1) e clique em Guardar (2). A página será recarregada, exibindo novamente o formulário de configuração, mas com as contas disponíveis carregadas e opções de configuração adicionais.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_save_backofficekey.png)

</br>


## Multibanco

O método de pagamento Multibanco, gera referências por algoritmo e é usado se não desejar atribuir um tempo limite (em dias) para encomendas pagas com Multibanco. A Entidade e Sub-Entidade são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Mostrar no Formulário de Pedido** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja.
2. **Nome de Exibição** - O nome do método de pagamento que aparece ao consumidor durante o checkout.
3. **Modo Sandbox** - Quando ativado, impede a ativação do callback com o servidor.
4. **Ativar Callback** - Quando ativado, o estado do pedido será atualizado quando o pagamento for recebido.
5. **Mostrar Ícone de Pagamento no Checkout** - (opcional) Quando ativado, substitui o Nome de Exibição do método de pagamento apresentado no checkout pelo respetivo ícone.
6. **Cancelar Pedido Multibanco** - (opcional) Quando ativado, permite a execução do cronjob de cancelamento de pedidos específico para este método.
7. **Entidade** - Selecione uma Entidade. Apenas pode escolher uma das Entidades associadas à Chave de Backoffice.
8. **Subentidade** - Selecione uma Subentidade. Apenas pode escolher uma das Subentidades associadas à Entidade escolhida anteriormente.

Clique em salvar (9) para guardar as alterações. 
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_multibanco.png)

</br>


## Multibanco com Referências Dinâmicas

O método de pagamento Multibanco com Referências Dinâmicas, gera referências por pedido e é usado se desejar atribuir um tempo limite (em dias) para encomendas pagas com Multibanco. A Entidade e Chave Multibanco são carregadas automaticamente, na introdução da Chave Backoffice. Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

Seguir os passos da configuração do Multibanco (indicados acima) com a seguinte alteração:

1. **Entidade** - Selecionar "Referências Dinâmicas de Multibanco", esta entidade só estará disponível para seleção se tiver efetuado contrato para criação de conta Multibanco com Referências Dinâmicas;
2. **Chave Multibanco** - Selecionar uma Chave Multibanco. Apenas pode selecionar uma das Chaves Multibanco associadas à Entidade escolhida anteriormente;
3. **Validade** - Selecionar o número de dias de validade da referência Multibanco. Ao deixar vazio, a referência Multibanco não expira.

Exemplos de validades:

- Escolhendo Validade de 0 dias, se uma encomenda for criada 22/03/2023 às 15:30, a referência Multibanco gerada expirará 22/03/2023 às 23:59, ou seja, no fim do dia em que foi gerada;
- Escolhendo Validade de 1 dia, se uma encomenda for criada 22/03/2023 às 9:30, a referência Multibanco gerada expirará 23/03/2023 às 23:59, ou seja, a referência Multibanco será válida durante o dia em que foi gerada mais 1 dia;
- Escolhendo Validade de 3 dias, se uma encomenda for criada 22/03/2023 às 20:30, a referência Multibanco gerada expirará 25/03/2023 às 23:59, ou seja, a referência Multibanco será válida durante o dia em que foi gerada mais 3 dias;

Clique em salvar (4) para guardar as alterações. 
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_multibanco_dynamic.png)

</br>


## MB WAY

O método de pagamento MB WAY, usa um número de telemóvel dado pelo consumidor e gera um pedido de pagamento à aplicação MB WAY do smartphone deste, a qual pode aceitar ou recusar.
As Chaves MB WAY são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Mostrar no Formulário de Pedido** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja.
2. **Nome de Exibição** - O nome do método de pagamento que aparece para o consumidor durante o checkout.
3. **Modo Sandbox** - Quando ativado, impede a ativação do callback com o servidor.
4. **Ativar Callback** - Quando ativado, o estado do pedido será atualizado quando o pagamento for recebido.
5. **Mostrar Ícone de Pagamento no Checkout** - (opcional) Quando ativado, substitui o Nome de Exibição do método de pagamento apresentado no checkout pelo respetivo ícone.
6. **Cancelar Pedido MB WAY** - (opcional) Quando ativado, permite a execução do cronjob de cancelamento de pedidos específico para este método.
7. **Chave MB WAY** - Selecione uma Chave. Apenas pode escolher uma das Chaves associadas à Chave de Backoffice.

Clique em salvar (8) para guardar as alterações. 
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_mbway.png)

</br>


## Cartão de Crédito

O método de pagamento Cartão de Crédito, permite pagar com cartão de crédito Visa ou Mastercard através da gateway ifthenpay.
As chaves de Cartão de Crédito são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Mostrar no Formulário de Pedido** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja.
2. **Nome de Exibição** - O nome do método de pagamento que aparece para o consumidor durante o checkout.
3. **Mostrar Ícone de Pagamento no Checkout** - (opcional) Quando ativado, substitui o Nome de Exibição do método de pagamento apresentado no checkout pelo respetivo ícone.
4. **Cancelar Pedido de Cartão de Crédito** - (opcional) Quando ativado, permite a execução do cronjob de cancelamento de pedidos específico para este método.
5. **Chave de Cartão de Crédito** - Selecione uma Chave. Apenas pode escolher uma das Chaves associadas à Chave de Backoffice.

Clique em Guardar (6) para guardar as alterações.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_ccard.png)

</br>


## Payshop

O método de pagamento Payshop, gera uma referência que pode ser paga em qualquer agente Payshop ou loja aderente.
As Chaves Payshop são carregadas automaticamente, na introdução da Chave Backoffice.
Configure o método de pagamento, a imagem abaixo mostra um exemplo de configuração minimamente funcional.

1. **Mostrar no Formulário de Pedido** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja.
2. **Nome de Exibição** - O nome do método de pagamento que aparece para o consumidor durante o checkout.
3. **Modo Sandbox** - Quando ativado, impede a ativação do callback com o servidor.
4. **Ativar Callback** - Quando ativado, o estado do pedido será atualizado quando o pagamento for recebido.
5. **Mostrar Ícone de Pagamento no Checkout** - (opcional) Quando ativado, substitui o Nome de Exibição do método de pagamento apresentado no checkout pelo respetivo ícone.
6. **Cancelar Pedido Payshop** - (opcional) Quando ativado, permite a execução do cronjob de cancelamento de pedidos específico para este método.
7. **Chave Payshop** - Selecione uma Chave. Você só pode escolher uma das Chaves associadas à Chave de Backoffice.
8. **Validade** - Selecione o número de dias para o prazo da referência Payshop. De 1 a 99 dias; deixe vazio se não desejar que a referência expire.

Clique em Guardar (9) para guardar as alterações.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/config_payshop.png)

</br>


# Outros
  
## Suporte

Na página Apps & Integrações -> Pagamentos, ao clicar em qualquer um dos métodos de pagamento ifthenpay, encontrará um link de Suporte (1) que o redirecionará para a página de suporte do ifthenpay, onde pode criar um ticket de suporte.
Para sua comodidade, também pode aceder este manual clicando no link de Instruções (2), que o redirecionará para a página do GitHub onde pode encontrar o manual do usuário.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/support.png)
</br>


 ## Requerer criação de conta adicional

Se já tem uma conta ifthenpay mas ainda não contratou um método de pagamento necessário, pode fazer um pedido automático com a ifthenpay.
 O tempo de resposta para este pedido é de 1 a 2 dias úteis, com a exceção do método de pagamento por Cartão de Crédito que pode exceder este tempo por ser necessário validação.
Para solicitar a criação de uma conta adicional, aceda à página de configuração do método de pagamento que deseja contratar e clique em Enviar Email (1).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/request_account.png)
</br>


No caso de já possuir uma conta Multibanco com referências estáticas e precisar de uma conta Multibanco com referências dinâmicas, pode fazê-lo na página de configuração do Multibanco, clicando em Enviar Email (1) abaixo de Pedido Multibanco com validade.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/request_account_multibanco_dynamic.png)
</br>

Assim, a equipa da ifthenpay adicionará o método de pagamento à sua conta, atualizando a lista de métodos de pagamento disponíveis no seu extensão.

**IMPORTANTE:** Ao pedir uma conta para o método de pagamento por Cartão de Crédito, a equipa da ifthenpay irá contactá-lo para pedir mais informações sobre a sua loja online e o seu negócio antes de ativar o método de pagamento.

</br>


## Limpeza de Configuração

Não se trata de um reset real, mas sim de uma forma de limpar a configuração atual do método de pagamento, caso necessite de a reconfigurar.
Isto é útil nos seguintes cenários:
  
- Se adquiriu uma nova Chave Backoffice e pretende atribuí-la ao seu site, mas já tem uma atualmente atribuída;
- Se pediu a criação de uma conta adicional por telefone ou ticket e pretende atualizar a lista de métodos de pagamento para usar a nova conta.
- Se pretende limpar as configurações do método de pagamento para voltar a configurar;

Na configuração do método de pagamento selecionado, clique no botão Desativar (1), selecione um método de pagamento alternativo (2) e clique no botão Desativar no modal (3) para confirmar.

**Atenção, esta ação irá limpar as atuais configurações do método de pagamento.**

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/deactivate.png)
</br>

Após desativar o método de pagamento, agora pode reativá-lo na página Apps & Integrations->Payments.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/activate_method.png)

</br>
Após a ativação, será solicitado que introduza novamente a Chave de Backoffice.
</br>


## Callback

IMPORTANTE: apenas os métodos de pagamento Multibanco, MB WAY e Payshop permitem ativar o Callback. O cartão de crédito altera o estado da encomenda automaticamente sem utilizar o Callback.

O Callback é uma funcionalidade que quando ativa, permite que a sua loja receba a notificação de um pagamento bem-sucedido. Ao receber um pagamento com sucesso de uma encomenda, o servidor da ifthenpay comunica com a sua loja, mudando o estado da encomenda para "Em Processamento". Pode usar os pagamentos da ifthenpay sem ativar o Callback, mas as suas encomendas não atualizaram o estado automaticamente;

Como mencionado acima em configurações, para ativar o Callback, aceda à página de configurações do extensão e ative a opção Ativar Callback. Após salvar as configurações, é executado o processo de associação da sua loja e método de pagamento aos servidores da ifthenpay, e será exibido um novo grupo "Callback" (apenas informativo) que apresenta estado do Callback (1), a chave anti-phishing (2), e a URL do Callback (3).

Após ativar o Callback não necessita de tomar mais nenhuma ação, o Callback está ativo e a funcionar.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/callback.png)

</br>


## Cronjob

Um cron job é uma tarefa agendada que é executada automaticamente em intervalos específicos no sistema, normalmente configurados para repetir todos os dias. A extensão ifthenpay fornece uma função que é executada quando o cron job do WHMCS é executado, esta verifica o estado do pagamento e cancela pedidos que não foram pagos dentro do limite de tempo configurado. A tabela abaixo mostra o tempo limite para cada método de pagamento, o qual o cronjob verifica e cancela as encomendas que não foram pagas dentro do tempo limite.Este tempo limite pode ser configurado apenas para o método de pagamento Multibanco com Referências Dinâmicas e Payshop.

| Método de Pagamento| Validade do pagamento          |
|--------------------|--------------------------------|
| Multibanco         | Não possui                     |
| Dynamic Multibanco | Configurável de 1 a n dias     |
| MB WAY             | 30 minutos                     |
| Payshop            | Configurável de 1 a 99 dias    |
| Credit Card        | 30 minutos                     |

Para ativar o cron job, aceda à página de configuração da extensão e ative a opção "Ativar Cron Job de Cancelamento", em seguida, clique em Salvar.

</br>



# Experiência do Utilizador Consumidor

O seguinte descreve a experiência do utilizador consumidor ao usar os métodos de pagamento da ifthenpay numa instalação "stock" do WHMCS 8, esta pode mudar com a adição de extensões de one-page-checkout.

Na página de finalização da compra, o consumidor pode escolher o método de pagamento.
Se a opção "Mostrar Ícone de Pagamento" estiver desativa, será exibido o nome do método de pagamento.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/checkout_no_icons.png)
</br>

Se a opção "Mostrar Ícone de Pagamento" estiver ativa, o ícone será exibido.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/checkout_icons.png)

</br>


## Pagar encomenda com Multibanco

Selecione o método de pagamento Multibanco (1) e clique em Completar Pedido (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/select_multibanco.png)
</br>


A página da fatura será exibida, mostrando a entidade, a referência, a validade e o valor a pagar.
Nota: No caso de atribuir uma conta Multibanco estática ou Multibanco com Referências Dinâmicas sem definir uma data de validade, a validade de pagamento não será exibido.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_multibanco.png)

</br>


## Pagar encomenda com Payshop

Selecione o método de pagamento Payshop (1) e clique em Completar Pedido (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/select_payshop.png)
</br>

A página de fatura será exibida, mostrando a referência, a validade e o valor a pagar.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_payshop.png)

</br>



## Pagar encomenda com MB WAY

Selecione o método de pagamento MB WAY (1) e clique em Completar Pedido (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/select_mbway.png)
</br>

O utilizador encontrará um formulário para inserir o número de telemóvel (1) e clicar em Pagar Agora (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_mbway_start.png)
</br>

Será exibido uma contagem decrescente, e o consumidor receberá uma notificação na aplicação MB WAY para autorizar o pagamento.
Se a contagem decrescente chegar a zero, o consumidor pode clicar no botão "Reenviar notificação MB WAY" para receber uma nova notificação na aplicação MB WAY.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_mbway_end.png)
</br>



Quando o consumidor autoriza o pagamento na aplicação MB WAY e o pagamento é recebido, a contagem decrescente é substituída pelo painel "Pedido Pago!".
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_mbway_paid.png)

</br>


## Pagar encomenda com Credit Card

Selecione o método de pagamento por Cartão de Crédito (1) e clique em Completar Pedido (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/select_ccard.png)
</br>

O utilizador será redirecionado para a página do gateway de Cartão de Crédito da ifthenpay.
Preencha os dados do cartão de crédito, número do cartão (1), data de validade (2), código de segurança (3), Nome no Cartão (4), e clique em Pagar (5).
É possível voltar (6), regressando à página de checkout. 
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version8/assets/invoice_ccard.png)
</br>



Chegou ao final do manual da extensão ifthenpay para WHMCS 8.
