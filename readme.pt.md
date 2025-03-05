# Versões Obsoletas

**❌ As versões do módulo para WHMCS 5, 6 e 7 não são mais suportadas.**

    Estas versões não receberão atualizações, correções de bugs ou patches de segurança.
    Estas versões não receberão suporte técnico.
    É altamente recomendável atualizar para a Versão 1.3.1 ou a nova versão 8.0.0.

</br>

# ⚠️ Atenção

Este manual foi criado para a versão 8.0.0 do módulo Ifthenpay, desenvolvida para WHMCS 8.

Descarregue o módulo Ifthenpay para WHMCS.
| | WHMCS 8 |
|----------------------------|-----------------------------------------------------------------------------------------------------|
| Descarregar Ficheiros do Instalador | [ifthenpay v8.0.0](https://github.com/ifthenpay/WHMCS/releases/download/8.0.0/ifthenpay.zip) |

**Aviso**: Esta versão do módulo destina-se a uma instalação nova do **WHMCS 8** ou a uma atualização da **Ifthenpay v1.3.1**. A Ifthenpay **não se responsabiliza** por quaisquer problemas decorrentes de erros de migração.

</br>

# Módulo de pagamento Ifthenpay para WHMCS 8

Ler em ![Português](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/pt.png) [Português](readme.pt.md), ou ![Inglês](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/en.png) [Inglês](readme.md)

[1. Introdução](#introdução)

[2. Compatibilidade](#compatibilidade)

[3. Instalação](#instalação)

[4. Configuração](#configuração)

- [Multibanco](#multibanco)
- [MB WAY](#mb-way)
- [Cartão de Crédito](#cartão-de-crédito)
- [Payshop](#payshop)
- [Cofidis Pay](#cofidis-pay)
- [Pix](#pix)
- [Gateway Ifthenpay](#gateway-ifthenpay)

[5. Outros](#outros)

- [Suporte](#suporte)
- [Pedir conta adicional](#pedir-conta-adicional)
- [Limpar Configuração](#limpar-configuração)
- [Callback](#callback)
- [Cronjob](#cronjob)
- [Logs](#logs)
- [Atualizar de versões antigas](#atualizar-de-versões-antigas)

[6. Experiência do Utilizador Consumidor](#experiência-do-utilizador-consumidor)

- [Pagar com Multibanco](#pagar-com-multibanco)
- [Pagar com MB WAY](#pagar-com-mb-way)
- [Pagar com Cartão de Crédito](#pagar-com-cartão-de-crédito)
- [Pagar com Payshop](#pagar-com-payshop)
- [Pagar com Cofidis Pay](#pagar-com-cofidis-pay)
- [Pagar com Pix](#pagar-com-pix)
- [Pagar com Ifthenpay Gateway](#pagar-com-ifthenpay-gateway)

[7. Resolução de Problemas](#resolução-de-problemas)

[8. Licença](#licença)

</br>

# Introdução

Este é o módulo de gateway de pagamento da ifthenpay para a plataforma WHMCS, que disponibiliza os seguintes métodos de pagamento:

![ifthenpay](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/payment_methods_banner.png)

</br>

**Multibanco** é um método de pagamento português que permite ao consumidor pagar através de uma referência bancária. Permite a geração de referências de pagamento que o consumidor pode usar para pagar a sua encomenda num multibanco ou através de um serviço de home banking online.

**MB WAY** é a primeira solução interbancária que permite compras e transferências imediatas através de smartphones. Permite gerar um pedido de pagamento no smartphone do consumidor, e este pode autorizar o pagamento da sua encomenda através da aplicação MB WAY.

**Payshop** é um método de pagamento português que permite ao consumidor pagar com uma referência Payshop. Permite a geração de uma referência de pagamento que o consumidor pode usar para pagar a sua encomenda num agente Payshop ou nos CTT (Correios e Telecomunicações de Portugal).

**Cartão de Crédito** Esta extensão permite gerar um pagamento através de Visa ou MasterCard, que o consumidor pode usar para pagar a sua encomenda.

**Cofidis Pay** é uma solução de pagamento até 12 prestações sem juros que facilita o pagamento de compras ao dividi-las.

**Pix** é um método de pagamento brasileiro que permite transferências de dinheiro instantâneas e seguras em reais brasileiros. Os pagamentos podem ser feitos através de código QR ou introduzindo a chave Pix do destinatário numa aplicação bancária.

</br>

**É necessário um contrato com a Ifthenpay**

Veja mais em [ifthenpay](https://ifthenpay.com).

Adesão em [Adesão ifthenpay](https://www.ifthenpay.com/aderir/).

**Suporte**

Para suporte, por favor crie um ticket de suporte em [Suporte ifthenpay](https://helpdesk.ifthenpay.com/).

</br>

# Compatibilidade

## WHMCS

Siga a tabela abaixo para verificar a compatibilidade do módulo da Ifthenpay com a versão da sua plataforma WHMCS.
| Versão do Módulo Ifthenpay | Versão WHMCS 5, 6 e 7 | Versão WHMCS 8 |
|---------------------------|----------------|--------------------------------|
| Ifthenpay v1.0.0 a v1.3.1 | ❌ Não compatível | ✅ Compatível |
| Ifthenpay v8.0.0 | ❌ Não compatível | ✅ Compatível |

</br>

## PHP

Siga a tabela abaixo para verificar a compatibilidade do módulo da Ifthenpay com a versão da sua linguagem PHP.
| Versão do Módulo Ifthenpay | PHP 7.4 | PHP 8.1 | PHP 8.2 | PHP 8.3 |
|---------------------------|----------------|--------------|---|---------------|
| Ifthenpay v1.0.0 a v1.3.1 | ✅ | ✅ | ⚠️ Não testado | ⚠️ Não testado |
| Ifthenpay v8.0.0 | ✅ | ⚠️ Não testado | ⚠️ Não testado | ✅ |

</br>

# Instalação

⚠️ **Atenção**: Se estiver a atualizar da versão 1.0.0 até 1.3.1 para a versão 8.0.0 ou superior, consulte a secção [Atualizar de versões mais antigas](#atualizar-de-versoes-mais-antigas).

Descarregue o ficheiro de instalação da versão mais recente do módulo da ifthenpay na página de "releases" do GitHub [ifthenpay-whmcs](https://github.com/ifthenpay/WHMCS/releases).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/installation_download_installer.png)

</br>

Extraia o ficheiro descarregado e coloque o conteúdo na raiz da sua plataforma WHMCS.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/installation_upload_files.png)

</br>

Após carregar os ficheiros, aceda ao backoffice da sua loja online e vá para Setup (1) -> Apps & Integrations (2) -> Browse (3) -> Payments (4).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/installation_backoffice_payments.png)

</br>

Desça na página e procure pelos métodos de pagamento da ifthenpay, que agora deverão estar disponíveis para ativação.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/Installation_available_payment_methods.png)

</br>

Clique no cartão do método de pagamento que deseja ativar (por exemplo, Multibanco) e depois clique em "Activate" (1).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/installation_activate_method.png)

</br>

# Configuração

Após instalar o módulo e ativar um método de pagamento, será redirecionado para a página de configuração, onde terá de configurá-lo usando os detalhes da sua conta ifthenpay.
Também pode aceder à página de configuração selecionando Payments -> Payment Gateways na barra de navegação lateral esquerda e selecionando o método de pagamento ativo que deseja configurar, ou clicando em Apps & Integrations -> selecionando o separador Ativo e escolhendo o método de pagamento que deseja configurar.

</br>

## Multibanco

O método de pagamento Multibanco gera referências que podem ser utilizadas para pagar uma encomenda num multibanco ou home banking.
Dependendo da conta que contratou com a ifthenpay, poderá utilizar uma conta Multibanco de tipo estático ou dinâmico.
</br>

Uma conta de tipo **estático** gera referências utilizando um algoritmo e é utilizada se não pretender definir um limite de tempo (em dias) para as encomendas pagas com Multibanco.
</br>

Uma conta de tipo **dinâmico** gera referências por encomenda e é utilizada se pretender definir um limite de tempo (em dias) para as encomendas pagas com Multibanco.
</br>

Ao introduzir uma Chave de Backoffice válida, a Entidade e a Sub-Entidade são carregadas automaticamente.
</br>

A imagem abaixo mostra um exemplo de uma configuração minimamente funcional utilizando uma conta Multibanco de tipo estático.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_multibanco_static.png)

</br>

1. **Show on Order Form** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja;
2. **Display Name** - O nome do método de pagamento que aparece ao consumidor durante o checkout;
3. **Versão** - Exibe a versão atual e verifica se a versão instalada está atualizada com a versão mais recente;
4. **Chave de Backoffice** - Introduza a sua Chave de Backoffice da ifthenpay para carregar as Entidades e Sub-Entidades disponíveis. A Chave de Backoffice é fornecida após a conclusão do contrato e consiste em conjuntos de quatro dígitos separados por um hífen (-), por exemplo, 1111-1111-1111-1111;
5. **Entidade** - Selecione uma Entidade, qualquer entidade numérica para utilizar uma conta multibanco de tipo estático. Só pode escolher uma das Entidades associadas à Chave de Backoffice;
6. **Sub-entidade** - Selecione uma Sub-Entidade. Só pode escolher uma das Sub-Entidades associadas à Entidade escolhida anteriormente;
7. **Validade** - (opcional) Apenas disponível para contas de tipo dinâmico.
8. **Valor Mínimo** - (opcional) Introduza o valor mínimo para exibir este método de pagamento apenas encomenda com valores de acima deste;
9. **Valor Máximo** - (opcional) Introduza o valor máximo para exibir este método de pagamento apenas encomenda com valores de abaixo deste;
10. **Exibir Ícone de Pagamento no Checkout** - (opcional) Quando ativado, substitui o Nome de Exibição do método de pagamento apresentado no checkout pelo respetivo ícone.
11. **Cancelar Encomenda Multibanco** - (opcional) Apenas disponível para contas de tipo dinâmico;
12. **Callback** (opcional) Ative para ativar o Callback, ao selecionar esta opção o estado da encomenda será atualizado quando um pagamento for recebido;

Clique em Guardar (13) para guardar as alterações.

</br>

A imagem abaixo mostra um exemplo de uma configuração minimamente funcional utilizando uma conta Multibanco de tipo dinâmico.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_multibanco_dynamic.png)

</br>

1.  **Entidade** - Selecione uma Entidade, "Referência Dinâmica" para utilizar uma conta multibanco de tipo dinâmico. Só pode escolher uma das Entidades associadas à Chave de Backoffice;
2.  **Sub-entidade** - Selecione uma Sub-Entidade. Só pode escolher uma das Sub-Entidades associadas à Entidade escolhida anteriormente;
3.  **Prazo** - (opcional) Selecione o número de dias para o prazo;
    Exemplos de prazos:

    Escolhendo Validade de 0 dias, se uma encomenda for criada 22/03/2025 às 15:30, a referência Multibanco gerada expirará 22/03/2025 às 23:59, ou seja, no fim do dia em que foi gerada;

    Escolhendo Validade de 1 dia, se uma encomenda for criada 22/03/2025 às 9:30, a referência Multibanco gerada expirará 23/03/2025 às 23:59, ou seja, a referência Multibanco será válida durante o dia em que foi gerada mais 1 dia;

    Escolhendo Validade de 3 dias, se uma encomenda for criada 22/03/2025 às 20:30, a referência Multibanco gerada expirará 25/03/2025 às 23:59, ou seja, a referência Multibanco será válida durante o dia em que foi gerada mais 3 dias;

4.  **Cancelar Encomenda Multibanco** - (opcional) Quando ativado, permite que o cron de cancelamento de encomendas seja executado para este método específico. O cron de cancelamento é executado com o cron diário do WHMCS.

</br>

## MB WAY

O método de pagamento MB WAY utiliza um número de telemóvel fornecido pelo consumidor e gera um pedido de pagamento na aplicação MB WAY para smartphone. O consumidor pode então aceitar ou recusar o pagamento.
Ao introduzir uma Chave de Backoffice válida, as Chaves MB WAY são carregadas automaticamente.
</br>

A imagem abaixo mostra um exemplo de uma configuração minimamente funcional.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_mbway.png)

</br>

1. **Show on Order Form** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja.
2. **Display Name** - O nome do método de pagamento que aparece ao consumidor durante o checkout.
3. **Versão** - Exibe a versão atual e verifica se a versão instalada está atualizada com a versão mais recente.
4. **Chave de Backoffice** - Introduza a sua Chave de Backoffice da ifthenpay para carregar as Chaves MB WAY disponíveis. A Chave de Backoffice é fornecida após a conclusão do contrato e consiste em conjuntos de quatro dígitos separados por um hífen (-), por exemplo, 1111-1111-1111-1111.
5. **Chave MB WAY** - Selecione uma Chave. Só pode escolher uma das Chaves associadas à Chave de Backoffice.
6. **Valor Mínimo** - (opcional) Introduza o valor mínimo para exibir este método de pagamento apenas encomenda com valores de acima deste;
7. **Valor Máximo** - (opcional) Introduza o valor máximo para exibir este método de pagamento apenas encomenda com valores de abaixo deste;
8.  **Descrição da Notificação da App** - (opcional) Modifique esta string se desejar. Use a string "{{invoice_id}}" para passar o número da fatura na descrição;
9.  **Exibir Ícone de Pagamento no Checkout** - (opcional) Quando ativado, substitui o Nome de Exibição do método de pagamento apresentado no checkout pelo respetivo ícone;
10. **Exibir Contagem Decrescente MB WAY** - (opcional) quando ativado, exibirá uma contagem decrescente na página da fatura e dará feedback da ação do utilizador (pagamento concluído, recusado, erro). Poderá querer manter esta opção desativada se estiver a utilizar um módulo de checkout de terceiros que possa entrar em conflito com a contagem decrescente;
11. **Cancelar Encomenda MB WAY** - (opcional) Quando ativado, permite que o cron de cancelamento de encomendas seja executado para este método específico. O cron de cancelamento é executado com o cron diário do WHMCS.
12. **Callback** (opcional) Ative para ativar o Callback, ao selecionar esta opção o estado da encomenda será atualizado quando um pagamento for recebido;

Clique em Guardar (13) para guardar as alterações.

</br>

## Cartão de Crédito

O método de pagamento Cartão de Crédito permite o pagamento com Visa ou Mastercard através do gateway ifthenpay.
Ao introduzir uma Chave de Backoffice válida, as Chaves de Cartão de Crédito são carregadas automaticamente.
</br>

A imagem abaixo mostra um exemplo de uma configuração minimamente funcional.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_ccard.png)

</br>

1. **Show on Order Form** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja;
2. **Display Name** - O nome do método de pagamento que aparece ao consumidor durante o checkout;
3. **Versão** - Exibe a versão atual e verifica se a versão instalada está atualizada com a versão mais recente;
4. **Chave de Backoffice** - Introduza a sua Chave de Backoffice da ifthenpay para carregar as Chaves de Cartão de Crédito disponíveis. A Chave de Backoffice é fornecida após a conclusão do contrato e consiste em conjuntos de quatro dígitos separados por um hífen (-), por exemplo, 1111-1111-1111-1111;
5. **Chave de Cartão de Crédito** - Selecione uma Chave. Só pode escolher uma das Chaves associadas à Chave de Backoffice.
6. **Valor Mínimo** - (opcional) Introduza o valor mínimo para exibir este método de pagamento apenas encomenda com valores de acima deste;
7. **Valor Máximo** - (opcional) Introduza o valor máximo para exibir este método de pagamento apenas encomenda com valores de abaixo deste;
8. **Exibir Ícone de Pagamento no Checkout** - (opcional) Quando ativado, substitui o Nome de Exibição do método de pagamento apresentado no checkout pelo respetivo ícone.
9.  **Cancelar Encomenda de Cartão de Crédito** - (opcional) Quando ativado, permite que o cron de cancelamento de encomendas seja executado para este método específico. O cron de cancelamento é executado com o cron diário do WHMCS.

Clique em Guardar (10) para guardar as alterações.

</br>

## Payshop

O método de pagamento Payshop gera uma referência que pode ser paga em qualquer agente Payshop ou loja afiliada.
Ao introduzir uma Chave de Backoffice válida, as Chaves Payshop são carregadas automaticamente.
</br>

A imagem abaixo mostra um exemplo de uma configuração minimamente funcional.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_payshop.png)

</br>

1. **Show on Order Form** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja;
2. **Display Name** - O nome do método de pagamento que aparece ao consumidor durante o checkout;
3. **Versão** - Exibe a versão atual e verifica se a versão instalada está atualizada com a versão mais recente;
4. **Chave de Backoffice** - Introduza a sua Chave de Backoffice da ifthenpay para carregar as Chaves Payshop disponíveis. A Chave de Backoffice é fornecida após a conclusão do contrato e consiste em conjuntos de quatro dígitos separados por um hífen (-), por exemplo, 1111-1111-1111-1111;
5. **Chave Payshop** - Selecione uma Chave. Só pode escolher uma das Chaves associadas à Chave de Backoffice.
6. **Validade** - (opcional) Introduza o número de dias para o prazo da referência Payshop. De 1 a 99 dias, deixe vazio se não quiser que expire.
7. **Valor Mínimo** - (opcional) Introduza o valor mínimo para exibir este método de pagamento apenas encomenda com valores de acima deste;
8. **Valor Máximo** - (opcional) Introduza o valor máximo para exibir este método de pagamento apenas encomenda com valores de abaixo deste;
9. **Exibir Ícone de Pagamento no Checkout** - (opcional) Quando ativado, substitui o Nome de Exibição do método de pagamento apresentado no checkout pelo respetivo ícone.
10. **Cancelar Encomenda Payshop** - (opcional) Quando ativado, permite que o cron de cancelamento de encomendas seja executado para este método específico. O cron de cancelamento é executado com o cron diário do WHMCS.
11. **Callback** (opcional) Ative para ativar o Callback, ao selecionar esta opção o estado da encomenda será atualizado quando um pagamento for recebido;

Clique em Guardar (12) para guardar as alterações.

</br>

## Cofidis Pay

O método Cofidis Pay redireciona o utilizador para a página da Cofidis, onde é possível configurar o pagamento num número selecionado de vezes.
Ao introduzir uma Chave de Backoffice válida, as Chaves Cofidis Pay são carregadas automaticamente.
</br>

A imagem abaixo mostra um exemplo de uma configuração minimamente funcional.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_cofidis.png)

</br>

1. **Show on Order Form** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja;
2. **Display Name** - O nome do método de pagamento que aparece ao consumidor durante o checkout;
3. **Versão** - Exibe a versão atual e verifica se a versão instalada está atualizada com a versão mais recente;
4. **Chave de Backoffice** - Introduza a sua Chave de Backoffice da ifthenpay para carregar as Chaves Cofidis Pay disponíveis. A Chave de Backoffice é fornecida após a conclusão do contrato e consiste em conjuntos de quatro dígitos separados por um hífen (-), por exemplo, 1111-1111-1111-1111;
5. **Chave Cofidis Pay** - Selecione uma Chave. Só pode escolher uma das Chaves associadas à Chave de Backoffice.
6. **Valor Mínimo** - (opcional) Introduza o valor mínimo para exibir este método de pagamento apenas encomenda com valores de acima deste. Aviso Importante: Ao selecionar a Chave Cofidis, esta entrada é atualizada com o valor configurado no backoffice da ifthenpay e, ao editar, não pode ser inferior ao valor especificado no backoffice da ifthenpay;
7. **Valor Máximo** - (opcional) Introduza o valor máximo para exibir este método de pagamento apenas encomenda com valores de abaixo deste. Aviso Importante: Ao selecionar a Chave Cofidis, esta entrada é atualizada com o valor configurado no backoffice da ifthenpay e, ao editar, não pode ser superior ao valor especificado no backoffice da ifthenpay;
8. **Exibir Ícone de Pagamento no Checkout** - (opcional) Quando ativado, substitui o Nome de Exibição do método de pagamento apresentado no checkout pelo respetivo ícone.
9. **Cancelar Encomenda Cofidis Pay** - (opcional) Quando ativado, permite que o cron de cancelamento de encomendas seja executado para este método específico. O cron de cancelamento é executado com o cron diário do WHMCS.
10. **Callback** (opcional) Ative para ativar o Callback, ao selecionar esta opção o estado da encomenda será atualizado quando um pagamento for recebido;

Clique em Guardar (11) para guardar as alterações.

</br>

## Pix

O método de pagamento Pix permite o pagamento com CPF através do gateway ifthenpay.
Ao introduzir uma Chave de Backoffice válida, as Chaves Pix são carregadas automaticamente.
</br>

A imagem abaixo mostra um exemplo de uma configuração minimamente funcional.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_pix.png)

</br>

1. **Show on Order Form** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja;
2. **Display Name** - O nome do método de pagamento que aparece ao consumidor durante o checkout;
3. **Versão** - Exibe a versão atual e verifica se a versão instalada está atualizada com a versão mais recente;
4. **Chave de Backoffice** - Introduza a sua Chave de Backoffice da ifthenpay para carregar as Chaves Pix disponíveis. A Chave de Backoffice é fornecida após a conclusão do contrato e consiste em conjuntos de quatro dígitos separados por um hífen (-), por exemplo, 1111-1111-1111-1111;
5. **Chave Pix** - Selecione uma Chave. Só pode escolher uma das Chaves associadas à Chave de Backoffice.
6. **Valor Mínimo** - (opcional) Introduza o valor mínimo para exibir este método de pagamento apenas encomenda com valores de acima deste;
7. **Valor Máximo** - (opcional) Introduza o valor máximo para exibir este método de pagamento apenas encomenda com valores de abaixo deste;
8. **Exibir Ícone de Pagamento no Checkout** - (opcional) Quando ativado, substitui o Nome de Exibição do método de pagamento apresentado no checkout pelo respetivo ícone.
9. **Cancelar Encomenda Pix** - (opcional) Quando ativado, permite que o cron de cancelamento de encomendas seja executado para este método específico. O cron de cancelamento é executado com o cron diário do WHMCS.
10. **Callback** (opcional) Ative para ativar o Callback, ao selecionar esta opção o estado da encomenda será atualizado quando um pagamento for recebido;

Clique em Guardar (11) para guardar as alterações.

</br>

## Gateway Ifthenpay

O método de pagamento Gateway Ifthenpay permite que o consumidor seja redirecionado para uma página de gateway de pagamento onde é possível selecionar qualquer um dos métodos de pagamento acima para pagar a encomenda.
Ao introduzir uma Chave de Backoffice válida, as Chaves do Gateway Ifthenpay são carregadas automaticamente.
</br>

A imagem abaixo mostra um exemplo de uma configuração minimamente funcional.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/configuration_ifthenpaygateway.png)

</br>

1. **Show on Order Form** - Quando ativado, exibe esta opção de método de pagamento no checkout da sua loja;
2. **Display Name** - O nome do método de pagamento que aparece ao consumidor durante o checkout;
3. **Versão** - Exibe a versão atual e verifica se a versão instalada está atualizada com a versão mais recente;
4. **Chave de Backoffice** - Introduza a sua Chave de Backoffice da ifthenpay para carregar as Chaves do gateway ifthenpay disponíveis. A Chave de Backoffice é fornecida após a conclusão do contrato e consiste em conjuntos de quatro dígitos separados por um hífen (-), por exemplo, 1111-1111-1111-1111;
5. **Chave do gateway ifthenpay** - Selecione uma Chave. Só pode escolher uma das Chaves associadas à Chave de Backoffice.
6. **Métodos de Pagamento** - Clique na checkbox à esquerda de cada método de pagamento para mostrar/ocultar esse método de pagamento na página do gateway e escolha a conta do método de pagamento na caixa de seleção à direita do mesmo. Se não existirem contas criadas para um determinado método de pagamento, um botão substituirá a caixa de seleção, que poderá utilizar para solicitar a criação dessa conta.
7. **Método de Pagamento Padrão** - Selecione um Método de Pagamento que será selecionado na página do gateway por padrão.
8. **Texto do Botão Fechar do Gateway** - Texto exibido no botão "Regressar à Loja" na página do gateway;
9. **Descrição** - Texto exibido abaixo do valor, como uma descrição, na página do gateway;
10. **Validade** - (opcional) Introduza o número de dias para o prazo da referência Payshop. De 1 a 99 dias, deixe vazio se não quiser que expire.
11. **Valor Mínimo** - (opcional) Introduza o valor mínimo para exibir este método de pagamento apenas encomenda com valores de acima deste;
12. **Valor Máximo** - (opcional) Introduza o valor máximo para exibir este método de pagamento apenas encomenda com valores de abaixo deste;
13. **Exibir Ícone de Pagamento no Checkout** - Exibe a imagem do logótipo deste método de pagamento no checkout, escolha entre 3 opções:

    - DESLIGADO - mostrar título do método: exibe o Título do Método de Pagamento;
    - LIGADO - mostrar ícone padrão: exibe o logótipo do gateway ifthenpay;
    - LIGADO - mostrar ícone composto: exibe uma imagem composta de todos os logótipos dos métodos de pagamento que selecionou;

14. **Cancelar Encomenda do gateway ifthenpay** - (opcional) Quando ativado, permite que o cron de cancelamento de encomendas seja executado para este método específico. O cron de cancelamento é executado com o cron diário do WHMCS.
15. **Callback** (opcional) Ative para ativar o Callback, ao selecionar esta opção o estado da encomenda será atualizado quando um pagamento for recebido;

Clique em Guardar (16) para guardar as alterações.

</br>

# Outros

## Suporte

Na página Apps & Integrations->Payments, ao clicar em qualquer um dos cartões dos métodos de pagamento da ifthenpay, pode encontrar um link de Suporte (1) que o redireciona para a página de suporte da ifthenpay, onde pode criar um ticket de suporte.
Para sua conveniência, também pode aceder a este manual do utilizador clicando no link Instruções (2), que o redirecionará para a página do GitHub onde pode encontrar o manual do utilizador.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/other_support.png)

</br>

## Pedir conta adicional

Se já tiver uma conta ifthenpay mas não tiver contratado um método de pagamento necessário, pode fazer um pedido automático à ifthenpay.
O tempo de resposta para este pedido é de 1 a 2 dias úteis, com exceção do método de pagamento Cartão de Crédito, que pode exceder este tempo devido a requisitos de validação.
Para solicitar a criação de uma conta adicional, aceda à página de configuração do método de pagamento que pretende contratar e introduza a sua chave de backoffice (1). Se não tiver nenhuma conta para esse método de pagamento, uma janela de diálogo aparecerá perguntando se deseja solicitar uma conta para esse método de pagamento. Pode então clicar no botão "ok" (2) para enviar um e-mail automático solicitando a criação dessa conta de método de pagamento.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/request_account.png)

Como resultado, a equipa da ifthenpay adicionará o método de pagamento à sua conta, atualizando a lista de métodos de pagamento disponíveis no seu módulo.
Caso já tenha uma conta, mas por algum motivo necessite de outra, poderá abrir um ticket de suporte a solicitá-la.

**IMPORTANTE:** Ao pedir uma conta para o método de pagamento Cartão de Crédito, a equipa da ifthenpay entrará em contacto consigo para solicitar mais informações sobre a sua loja online e o seu negócio antes de ativar o método de pagamento.

</br>

## Limpar Configuração

Não é uma limpeza completa do módulo, mas sim uma forma de limpar a configuração atual do método de pagamento, caso precise de reconfigurá-lo.
Isto é útil nos seguintes cenários:

- Se adquiriu uma nova Chave de Backoffice e pretende atribuí-la ao seu website, mas já tem uma atribuída.
- Se pediu a criação de uma conta adicional por telefone ou ticket e pretende atualizar a lista de métodos de pagamento para utilizar a nova conta.
- Se pretende repor a configuração do método de pagamento para reconfigurá-lo.

Após configurar com sucesso um método de pagamento uma vez, a Chave de Backoffice será bloqueada e um botão "Repor" será exibido ao lado dela.
Para repor, clique no botão "Repor" (1) e confirme a ação clicando no botão "OK" (2).

**Atenção, esta ação irá limpar a configuração atual do método de pagamento.**

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/reset_configuration.png)

</br>

## Callback

**IMPORTANTE:** Apenas os métodos de pagamento Multibanco, MB WAY, Payshop, Cofidis Pay, Pix e Gateway Ifthenpay permitem a ativação do Callback. O método Cartão de Crédito altera o estado da encomenda automaticamente sem utilizar o Callback.

O Callback é uma funcionalidade que, quando ativada, permite que a sua loja receba notificações de pagamentos bem-sucedidos. Ao receber um pagamento bem-sucedido para uma fatura, o servidor ifthenpay comunica com a sua loja, alterando o estado da fatura para "Pago". Pode utilizar os pagamentos ifthenpay sem ativar o Callback, mas as suas encomendas não atualizarão automaticamente o seu estado.

Conforme mencionado nas configurações acima, para ativar o Callback, aceda à página de configuração da extensão e ative a opção "Ativar Callback". Após guardar as definições, o processo de associação da sua loja e método de pagamento aos servidores da ifthenpay será executado e, se ativado com sucesso, o grupo Callback exibirá agora o estado do Callback como um badge de cor verde "Callback Ativo" (1), a chave anti-phishing (2) e o URL do Callback (3).

Após ativar o Callback, não precisa de tomar mais nenhuma ação. O Callback está ativo e a funcionar.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/callback.png)

</br>

## Cronjob

Uma tarefa cron é uma tarefa agendada que é executada automaticamente em intervalos específicos no sistema, geralmente definida para se repetir todos os dias. A extensão ifthenpay fornece uma função que é executada quando o cron diário do WHMCS é executado, verifica o estado do pagamento e cancela as faturas que não foram pagas dentro do limite de tempo configurado. A tabela abaixo mostra o limite de tempo para cada método de pagamento, que a tarefa cron verifica e cancela as faturas que não foram pagas dentro do limite de tempo. Este limite de tempo pode ser configurado apenas para os métodos de pagamento Multibanco com Referências Dinâmicas, Payshop e Gateway Ifthenpay.

| Método de Pagamento      | Prazo de Pagamento                  |
| ------------------------ | ----------------------------------- |
| Multibanco               | Sem prazo                            |
| Multibanco Dinâmico      | Configurável de 0 a n dias          |
| MB WAY                   | 30 minutos                          |
| Payshop                  | Configurável de 1 a 99 dias         |
| Cartão de Crédito         | 30 minutos                          |
| Cofidis                  | 60 minutos                          |
| Pix                      | 30 minutos                          |
| Gateway Ifthenpay        | Configurável de 1 a 99 dias         |

Para ativar o cron, aceda à página de configuração do módulo e ative a opção "Cancelar Encomenda método de pagamento", depois clique em Guardar.

</br>

## Logs

### Localização e Propósito

Este módulo tem a sua própria cobertura de logs, e os ficheiros de logs resultantes podem ser encontrados em `/modules/gateways/ifthenpaylib/lib/Log/logs/`.
A tabela abaixo mostra os ficheiros de log e as suas funções.

| Ficheiro              | Função                                                               |
| ---------------------- | -------------------------------------------------------------------- |
| cron.log               | Regista os logs relacionados com a execução do cron de cancelamento. |
| general_logs.log       | Regista os logs não relacionados com um único método de pagamento. |
| multibanco.log         | Regista os logs relacionados com o método de pagamento Multibanco. |
| mbway.log              | Regista os logs relacionados com o método de pagamento MB WAY.    |
| payshop.log            | Regista os logs relacionados com o método de pagamento Payshop.   |
| ccard.log              | Regista os logs relacionados com o método de pagamento Cartão de Crédito. |
| cofidispay.log         | Regista os logs relacionados com o método de pagamento Cofidis Pay. |
| pix.log                | Regista os logs relacionados com o método de pagamento Pix.     |
| ifthenpaygateway.log   | Regista os logs relacionados com o método de pagamento Gateway Ifthenpay. |

</br>

### Níveis de Registo

Para evitar o preenchimento desnecessário dos ficheiros de registo, o módulo regista apenas eventos de nível de erro. Se houver necessidade de fazer debug e analisar os eventos de nível inferior, pode aceder ao ficheiro de configuração em `/modules/gateways/ifthenpaylib/lib/Config/Config.php`, e definir o nível de registo para informação, editando:

a linha 12:

    public const LOG_LEVEL = self::LOG_LEVEL_ERROR;

para:

    public const LOG_LEVEL = self::LOG_LEVEL_INFO;

</br>

# Atualizar de versões antigas

No momento da criação deste documento, a versão anterior (mais antiga) é a v1.3.1, a versão mais recente 8.0.0 tem algumas alterações que são atualizadas automaticamente, principalmente alterações nas tabelas da base de dados dos métodos ifthenpay.

Para tornar a transição o mais suave possível, siga este pequeno guia:
**Nota Importante**: Antes de prosseguir, tenha em mente que ambas as versões do módulo não podem coexistir ao mesmo tempo, uma vez que a mais recente é ativada, a mais antiga deixará de funcionar corretamente, e estas ações são irreversíveis sem interagir diretamente com a base de dados.
Poderá passar por um período em que as faturas não são canceladas automaticamente ou atualizadas como pagas pelo callback, isto deve-se à diferença de parâmetros da versão mais antiga para a mais recente.

</br>

## Carregar ficheiros de instalação

Carregue os ficheiros de instalação do módulo para a raiz do WHMCS, será avisado sobre a substituição dos ficheiros de hooks `/includes/hooks/ifthenpay.php`, aceite e proceda.

</br>

## Ativar métodos de pagamento

Após carregar os ficheiros, aceda ao back office de administração e vá a Setup (1) -> Apps & Integrations (2) -> Browse (3) -> Payments (4).

A imagem abaixo mostra os métodos de pagamento mais recentes distinguíveis pelo sufixo "V2".
Tomando o Cartão de Crédito como exemplo, ativaremos a versão mais recente "Ifthenpay Credit Card V2" (1) para substituir a versão mais antiga (2) posteriormente.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/upgrade_from_older_payments.png)

</br>

Ative o método de pagamento, Cartão de Crédito neste caso, clicando no botão "Activate" (1).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/upgrade_from_older_activate.png)

</br>

Configure o método de pagamento (1) (consulte [Configuração](#configuração) para outros métodos) e clique no botão "Save Changes" (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/upgrade_from_older_configure.png)

</br>

Vá ao método mais antigo, este mostrará tamanhos de ícones anormais, uma vez que deverá ter perdido os estilos CSS ao substituir o ficheiro de hooks durante a instalação da versão mais recente. Clique no botão "Deactivate" (1).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/upgrade_from_older_older_method.png)

</br>

Expanda a caixa de seleção (1) e selecione o método de pagamento equivalente para substituir (2), deve ter o mesmo nome, e clique no botão "Deactivate" (3).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/upgrade_from_older_deactivate_older.png)

</br>

Agora repita para outros métodos de pagamento que possa necessitar.

</br>

# Experiência do Utilizador Consumidor

O seguinte descreve a experiência do utilizador consumidor ao utilizar os métodos de pagamento ifthenpay numa instalação "stock" do WHMCS 8. Tenha em atenção que esta experiência pode mudar com a adição de extensões de checkout de terceiros.

## Selecionar método de pagamento

Na página de checkout, o consumidor pode escolher o método de pagamento.
Se a opção de mostrar o ícone de pagamento estiver desativada, o nome do método de pagamento será exibido.
O nome do método de pagamento pode ser editado na página de configuração no campo "Display Name".
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/ux_checkout_select_payment_text.png)

</br>

Se a opção de configuração "Exibir Ícone de Pagamento no Checkout" estiver ativada, o ícone será exibido no lugar do título do pagamento.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/ux_checkout_select_payment_icon.png)

</br>

O método de pagamento Gateway Ifthenpay fornece uma opção adicional para mostrar os ícones dos métodos de pagamento que estarão disponíveis dentro da página do gateway ifthenpay. Ao selecionar a opção "ON - mostrar ícone composito" no campo "Exibir Ícone de Pagamento no Checkout".
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/ux_checkout_select_payment_icon_composite.png)

</br>

Quando o utilizador seleciona um método de pagamento e clica no botão "Complete Order", será redirecionado para a página da fatura.

## Pagar com Multibanco

Os detalhes de pagamento Multibanco serão exibidos com a entidade, referência, validade e o valor a pagar.

</br>

**Nota**: No caso de atribuição de uma conta Multibanco estática ou Multibanco com Referências Dinâmicas sem definir uma data de expiração, o prazo de pagamento não será exibido.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_multibanco_details.png)

</br>

## Pagar com MB WAY

O formulário de número de telemóvel MB WAY será exibido, o utilizador deve selecionar o código de país correto (1), inserir um número válido (2) que já esteja associado à App MB WAY e clicar no botão "Pagar" (3).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_form.png)

</br>

Se a opção de configuração "Exibir Contagem Decrescente MB WAY" estiver desativada, será exibida uma mensagem simples e o consumidor receberá uma notificação na App MB WAY para autorizar o pagamento.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_no_countdown.png)

</br>

Se a opção de configuração "Exibir Contagem Decrescente MB WAY" estiver ativada, um temporizador de contagem decrescente será exibido e o consumidor receberá uma notificação na App MB WAY para autorizar o pagamento.
Se a contagem decrescente chegar a zero, o consumidor pode clicar no botão "Reenviar notificação MB WAY" para receber uma nova notificação na aplicação MB WAY.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_countdown.png)

</br>

Ao utilizar a contagem decrescente, a página da fatura será atualizada de acordo com as ações do consumidor ou quaisquer erros que possam ocorrer.

O estado de pagamento confirmado será exibido após o consumidor confirmar o pagamento na sua App MB WAY.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_paid.png)

</br>

O estado de expirado será exibido após atingir o final da contagem decrescente.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_expired.png)

</br>

O estado de rejeitado pelo utilizador será exibido após o consumidor recusar o pagamento na sua App MB WAY.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_rejected_by_user.png)

</br>

O estado de recusado será exibido após uma verificação do MB WAY retornar um erro relacionado com a associação da App MB WAY.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_refused.png)

</br>

O estado de erro será exibido após introduzir um número de telefone inválido ou se ocorrer um erro no MB WAY ou em ifthenpay.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_mbway_details_error.png)

</br>

## Pagar com Cartão de Crédito

Será exibido um botão "Pagar" (1), no qual o consumidor deve clicar para ser redirecionado para a página do Cartão de Crédito.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_ccard_form.png)

</br>

O utilizador será redirecionado para a página da gateway de Cartão de Crédito.
Preencha os dados do cartão de crédito, número do cartão (1), data de expiração (2), código de segurança (3), Nome no Cartão (4) e clique em Pagar (5).
Pode voltar atrás (6), regressando à página de fatura.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_ccard_gateway_page.png)

</br>

## Pagar com Payshop

Os detalhes de pagamento Payshop serão exibidos com a referência, validade e o valor a pagar.
</br>

**Nota**: No caso de configurar o método payshop sem definir uma data de expiração, o prazo de pagamento não será exibido.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_payshop_details.png)

</br>

## Pagar com Cofidis Pay

Será exibido um botão "Pagar" (1), no qual o consumidor deve clicar para ser redirecionado para a página do Cofidis Pay.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_form.png)

</br>

O utilizador será redirecionado para a página Cofidis Pay, na qual terá de passar por uma série de passos para concluir.

### Login/Registo

Aqui, o utilizador pode iniciar sessão (1) ou, se não tiver uma conta, registar-se no Cofidis Pay (2).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_1.png)

</br>

### Prestações e Informações Pessoais

Escolha o número de prestações e edite os dados de faturação e pessoais, se necessário.

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_2.png)

1. Selecione o número de prestações que deseja;
2. Verifique o resumo do plano de pagamento;
3. Preencha os seus dados pessoais e de faturação;
4. Carregue os ficheiros de identificação;
5. Clique em "Avançar" para continuar;

</br>

### Termos e Condições

Leia os Termos e Condições, selecione "Li e autorizo" (1) para aceitar e clique no botão "Avançar" (2) para prosseguir.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_3.png)

</br>

### Formalização do acordo

Clique em "Enviar Código" (1) para enviar um código de autenticação para o seu telefone.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_4.png)

</br>

### Código de autenticação de formalização do acordo

Introduza o código de autenticação recebido no telefone (1) e clique no botão "Confirmar Código" (2) para prosseguir.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_5.png)

</br>

### Resumo e Pagamento

Preencha os detalhes do seu cartão de crédito (1) (número, data de expiração e CW) e clique no botão "Validar" (2);

![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_6.png)

</br>

### Sucesso e regresso à loja

O contrato de pagamento foi bem-sucedido, o utilizador pode agora regressar à loja, esperando por um redirecionamento automático ou clicando no botão "sair".
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_cofidis_gateway_page_6.png)

</br>

## Pagar com Pix

O formulário Pix será exibido, o utilizador deve introduzir o seu nome (1), CPF (2), e-mail (3) e clicar no botão "Pagar" (4).
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_pix_form.png)

</br>

O utilizador será redirecionado para a página Pix.
Aqui, é possível proceder ao pagamento com uma de duas opções:

- Ler o código QR (1) com o telemóvel;
- Copiar o código Pix (2) e pagar com o banco online; 
 
Nota Importante: Para ser redirecionado de volta à loja após o pagamento, esta página deve ser deixada aberta. Se for fechada, o consumidor ainda poderá pagar, desde que já tenha lido o código Pix, apenas não será redirecionado de volta à loja.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_pix_gateway_page.png)

</br>

Após ser redirecionado de volta para a loja, o utilizador poderá ver uma mensagem a informar o sucesso da operação e que a verificação da transação está em curso.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_pix_gateway_return.png)

</br>

## Pagar com Ifthenpay Gateway

Será exibido um botão "Pagar" (1), no qual o consumidor deve clicar para ser redirecionado para a página do gateway ifthenpay.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_ifthenpaygateway_form.png)

</br>

O utilizador será redirecionado para a página do gateway ifthenpay.
Aqui, o utilizador pode verificar o valor e selecionar um dos métodos de pagamento disponíveis na página do gateway.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_ifthenpaygateway_gateway_page_1.png)

</br>

Ao escolher um método de pagamento offline como Multibanco ou Payshop, o utilizador deve anotar os detalhes de pagamento e clicar no botão "Concluir" (2), ou utilizar a aplicação de home banking para pagar imediatamente e confirmar o pagamento clicando no botão "Confirmo o Pagamento" (3).
</br>

Ao escolher um método de pagamento online como MB WAY, Cartão de Crédito, Pix, Google Pay e Apple Pay, o utilizador deve seguir as instruções no gateway e preencher os campos necessários para prosseguir. Quando terminar, clique no botão "Concluir" (2) para regressar à loja.
![img](https://github.com/ifthenpay/WHMCS/raw/assets/version_8/assets/paying_ifthenpaygateway_gateway_page_2.png)

</br>

# Resolução de Problemas

Aqui falaremos sobre alguns problemas comuns.

## Falta de permissões para ficheiros de registo

Pode instalar o módulo e, mais tarde, um erro ser lançado no módulo, fazendo com que o registador registe um evento. Pode acontecer que, em vez de registar o evento, outro erro seja lançado devido à incapacidade de registar o registo.
O registador do módulo requer permissões para criar, ler e escrever para os ficheiros de registo, por isso certifique-se de dar permissões suficientes para o fazer.

</br>

# Licença

Este projeto está licenciado sob a Licença Pública Geral GNU v3.0 (GPLv3).

    É livre de usar, modificar e distribuir este software, desde que siga os termos da GPLv3.
    A redistribuição com fins lucrativos deve estar em conformidade com a GPLv3.
    Este software vem sem qualquer garantia.

Para detalhes completos, consulte o ficheiro LICENSE ou leia a GNU GPLv3.
