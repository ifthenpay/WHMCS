<?php

return [

	/* ------------------------------ Admin Common ------------------------------ */

	'your_current_version' => 'A sua versão atual é',
	'current_version_installed' => 'Versão instalada é',
	'a_new_version' => 'Uma nova versão',
	'is_available' => 'está disponivel.',
	'download' => 'Descarregar',
	'module_up_to_date' => 'O seu módulo está atualizado.',
	'backoffice_key' => 'Chave Backoffice',
	'reset' => 'Reset',
	'callback' => 'Callback',
	'callback_desc' => 'Selecione para ativar o Callback.',
	'show_payment_icon' => 'Exibir Ícone de Pagamento no Checkout',
	'show_payment_icon_desc' => 'Selecione para exibir o ícone de pagamento no checkout.',
	'deadline' => 'Validade',
	'callback_inactive' => 'Callback Inativo',
	'callback_active' => 'Callback Ativo',
	'anti_phishing_key' => 'Anti-phishing Key:',
	'callback_url' => 'Callback URL:',
	'version' => 'Versão',
	'no_deadline' => 'Sem validade',
	'min_amount' => 'Valor Mínimo',
	'max_amount' => 'Valor Máximo',
	'min_amount_desc' => 'Exibe método de pagamento apenas para pedidos com valor total superior ao valor inserido. Deixe vazio para exibir sempre.',
	'max_amount_desc' => 'Exibe método de pagamento apenas para pedidos com valor total inferior ao valor inserido. Deixe vazio para exibir sempre.',
	'cofidis_min_amount_desc' => 'Exibe método de pagamento apenas para pedidos com valor total superior ao valor inserido. O valor inserido não pode ser inferior ao valor definido no backoffice ifthenpay.',
	'cofidis_max_amount_desc' => 'Exibe método de pagamento apenas para pedidos com valor total superior ao valor inserido. O valor inserido não pode ser inferior ao valor definido no backoffice ifthenpay.',
	'none' => 'None',
	'msg_are_sure_reset_config' => 'Esta ação irá apagar a configuração atual deste método de pagamento, confirme para continuar,',



	/* ---------------------------- Admin Common ---------------------------- */

	'msg_invalid_minimum_amount' => 'Valor Mínimo Inválido',
	'msg_invalid_maximum_amount' => 'Valor Máximo Inválido',
	'msg_invalid_min_max_amount' => 'Valor Mínimo deve ser menor que o Valor Máximo.',
	'msg_invalid_deadline' => 'Validade inválida',
	'msg_invalid_backoffice_key' => 'Backoffice Key inválida.',
	'msg_invalid_backoffice_key_example' => 'Backoffice Key inválida. Deve ter o seguinte formato de exemplo: 1111-1111-1111-1111.',
	'msg_request_new_account' => 'Deseja pedir a criação de uma conta?',
	'msg_invalid_key' => 'Chave inválida',
	'msg_success_sending_account_request' => 'Pedido de criação de conta enviado com sucesso.',
	'msg_error_sending_account_request' => 'Erro ao enviar pedido de criação de conta, para mais informação verifique o ficheiro general_logs em modules/gateways/ifthenpaylib/lib/Log/logs/general_logs.log.',



	/* ---------------------------- Admin Multibanco ---------------------------- */

	'entity' => 'Entidade',
	'subentity' => 'Sub-Entidade',
	'multibanco_dynamic_reference' => 'Referência Dinâmica',
	'multibanco_deadline_desc' => 'Apenas disponivel para Multibanco Dinâmico. Selecione "0" para expirar a referência no mesmo dia da sua criação às 23:59. Selecione "Sem validade" para nunca expirar',
	'cancel_multibanco' => 'Cancelar Encomenda Multibanco',
	'cancel_multibanco_desc' => 'Selecione para cancelar automaticamente encomendas Multibanco após expirar a referência. Apenas funciona com referências dinâmicas. Executa juntamente com o cron diário.',
	'msg_invalid_entity' => 'Entidade inválida',
	'msg_invalid_subentity' => 'Sub-Entidade inválida',
	'msg_no_multibanco_accounts_found' => 'Não foram encontradas contas Multibanco para a sua Chave de Backoffice.',
	'msg_error_updating_multibanco_database' => 'Erro ao atualizar tabela da base de dados, para mais informação verifique o ficheiro multibanco em modules/gateways/ifthenpaylib/lib/Log/logs/multibanco.log.',



	/* ---------------------------- Admin Payshop ---------------------------- */

	'payshop_key' => 'Chave Payshop',
	'payshop_deadline_desc' => 'Introduza os dias de validade que pretende, entre 1 a 99 dias. Deixe vazio para nunca expirar.',
	'msg_invalid_payshop_key' => 'Chave Payshop inválida',
	'cancel_payshop' => 'Cancelar Encomenda Payshop',
	'cancel_payshop_desc' => 'Selecione para cancelar automaticamente encomendas Payshop após expirar a referência. Executa juntamente com o cron diário.',
	'msg_no_payshop_accounts_found' => 'Não foram encontradas contas Payshop para a sua Chave de Backoffice.',
	'msg_error_updating_payshop_database' => 'Erro ao atualizar tabela da base de dados, para mais informação verifique o ficheiro payshop em modules/gateways/ifthenpaylib/lib/Log/logs/payshop.log.',



	/* ---------------------------- Admin MB WAY ---------------------------- */

	'mbway_key' => 'Chave MB WAY',
	'show_mbway_countdown' => 'Exibir Contagem Decrescente MB WAY',
	'show_mbway_countdown_desc' => 'Selecione para exibir a contagem decrescente de 4 minutos do MB WAY no pagamento',
	'cancel_mbway' => 'Cancelar Encomenda MB WAY',
	'cancel_mbway_desc' => 'Selecione para cancelar automaticamente encomendas MB WAY se não forem pagas 30 minutos após a sua criação. Executa juntamente com o cron diário.',
	'msg_invalid_mbway_key' => 'Chave MB WAY inválida',
	'msg_no_mbway_accounts_found' => 'Não foram encontradas contas MB WAY para a sua Chave de Backoffice.',
	'mbway_payment_invoice' => 'MB WAY Pagamento {{invoice_id}}',
	'notification_description' => 'Descrição de Notificação da App',
	'notification_description_desc' => 'Pequena descrição exibida ao cliente na App MB WAY, use o texto {{invoice_id}} para passar o número do invoice_id.',
	'msg_invalid_notification_description' => 'Descrição de Notificação Inválida, não deve conter caracteres especiais excepto o texto {{invoice_id}} e não pode ter mais de 100 caracteres..',
	'msg_error_updating_mbway_database' => 'Erro ao atualizar tabela da base de dados, para mais informação verifique o ficheiro mbway em modules/gateways/ifthenpaylib/lib/Log/logs/mbway.log.',



	/* ---------------------------- Admin Ccard ---------------------------- */

	'ccard_key' => 'Chave de Cartão de Crédito',
	'msg_invalid_ccard_key' => 'Chave de Cartão de Crédito inválida',
	'cancel_ccard' => 'Cancelar Encomenda por Cartão de Crédito',
	'cancel_ccard_desc' => 'Selecione para cancelar automaticamente encomendas por Cartão de Crédito se não forem pagas 30 minutos após a sua criação. Executa juntamente com o cron diário.',
	'msg_no_ccard_accounts_found' => 'Não foram encontradas contas de Cartão de Crédito para a sua Chave de Backoffice.',
	'msg_error_updating_ccard_database' => 'Erro ao atualizar tabela da base de dados, para mais informação verifique o ficheiro ccard em modules/gateways/ifthenpaylib/lib/Log/logs/ccard.log.',



	/* ---------------------------- Admin Cofidis ---------------------------- */

	'cofidis_key' => 'Chave Cofidis Pay',
	'msg_invalid_cofidis_key' => 'Chave Cofidis Pay inválida',
	'cancel_cofidis' => 'Cancelar Encomenda por Cofidis Pay',
	'cancel_cofidis_desc' => 'Selecione para cancelar automaticamente encomendas por Cofidis Pay se não forem pagas 60 minutos após a sua criação. Executa juntamente com o cron diário.',
	'msg_no_cofidis_accounts_found' => 'Não foram encontradas contas de Cofidis Pay para a sua Chave de Backoffice.',
	'msg_error_updating_cofidis_database' => 'Erro ao atualizar tabela da base de dados, para mais informação verifique o ficheiro cofidis em modules/gateways/ifthenpaylib/lib/Log/logs/cofidis.log.',
	'msg_invalid_minimum_amount_ifthenpay' => 'Valor Mínimo Inválido, deve ser maior ou igual ao valor definido no backoffice ifthenpay.',
	'msg_invalid_maximum_amount_ifthenpay' => 'Valor Máximo Inválido, deve ser menor ou igual ao valor definido no backoffice ifthenpay.',



	/* ---------------------------- Admin ifthenpaygateway ---------------------------- */

	'ifthenpaygateway_key' => 'Chave de Gateway',
	'ifthenpaygateway_payment_methods' => 'Métodos de Pagamento',
	'ifthenpaygateway_default_payment_method' => 'Método de Pagamento por defeito',
	'show_icon_on_default' => 'ON - exibe icone por defeito',
	'show_icon_on_composite_image' => 'ON - exibe icone composito',
	'show_icon_off_method_name' => 'OFF - exibe título do método',
	'ifthenpaygateway_deadline_desc' => 'Introduza os dias de validade que pretende, entre 1 a 99 dias. Deixe vazio para nunca expirar.',
	'close_btn_label' => 'Texto do botão Fechar da Gateway',
	'close_btn_label_desc' => 'Substitui o texto do botão de regressar na página da gateway. Deixe vazio para usar texto por defeito.',
	'ifthenpaygateway_description' => 'Descrição',
	'ifthenpaygateway_description_desc' => 'Descrição exibida na página do gateway ifthenpay abaixo do valor.',
	'btn_request_gateway_method' => 'Pedir Método de Gateway',
	'ifthenpaygateway_select_a_gateway_key' => 'Selecione uma Chave de Gateway para exibir este campo.',
	'msg_invalid_ifthenpaygateway_key' => 'Chave Ifthenpay Gateway inválida',
	'cancel_ifthenpaygateway' => 'Cancelar Encomenda por Ifthenpay Gateway',
	'cancel_ifthenpaygateway_desc' => 'Selecione para cancelar automaticamente encomendas Ifthenpay Gateway após expirar o Link. Executa juntamente com o cron diário.',
	'msg_no_ifthenpaygateway_accounts_found' => 'Não foram encontradas contas de Ifthenpay Gateway para a sua Chave de Backoffice.',
	'msg_request_new_gateway_method' => 'Deseja pedir um método Gateway {%method%}?',
	'msg_error_updating_ifthenpaygateway_database' => 'Erro ao atualizar tabela da base de dados, para mais informação verifique o ficheiro ifthenpaygateway em modules/gateways/ifthenpaylib/lib/Log/logs/ifthenpaygateway.log.',
	'msg_invalid_ifthenpaygateway_description' => 'Descrição Inválida, não pode ter mais de 200 caracteres.',
	'msg_invalid_ifthenpaygateway_close_btn' => 'Texto do botão Fechar da Gateway Inválido, não pode ter mais de 50 caracteres.',
	'msg_invalid_gateway_methods' => 'Sem métodos de pagamento de gateway selecionados.',



	/* ---------------------------- Admin Pix ---------------------------- */

	'pix_key' => 'Chave Pix',
	'msg_invalid_pix_key' => 'Chave Pix inválida',
	'cancel_pix' => 'Cancelar encomenda por Pix',
	'cancel_pix_desc' => 'Selecione para cancelar automaticamente encomendas por Pix se não forem pagas 30 minutos após a sua criação. Executa juntamente com o cron diário.',
	'msg_no_pix_accounts_found' => 'Não foram encontradas contas de Pix para a sua Chave de Backoffice.',
	'msg_error_updating_pix_database' => 'Erro ao atualizar tabela da base de dados, para mais informação verifique o ficheiro pix em modules/gateways/ifthenpaylib/lib/Log/logs/pix.log.',



	/* -------------------------- Front Payment Details ------------------------- */

	'multibanco' => 'Multibanco',
	'mbway' => 'MB WAY',
	'payshop' => 'Payshop',
	'ccard' => 'Cartão de Crédito',
	'cofidis' => 'Cofidis Pay',
	'pix' => 'Pix',
	'ifthenpaygateway' => 'Ifthenpay Gateway',

	'pay_btn' => 'Pagar',
	'pay' => 'Pagar',
	'pay_by' => 'Pagar através de',
	'pay_with' => 'Pagar com',
	'entity_label' => 'Entidade',
	'reference_label' => 'Referência',
	'deadline_label' => 'Validade',
	'amount_label' => 'Valor',
	'payment_process_completed' => 'Processo de pagamento completado.',
	'wait_for_payment_verification' => 'Está a decorrer a verificação do pagamento.',



	/* --------------------------- Front Payment cofidis -------------------------- */

	'cofidis_desc_line_1' => 'COFIDIS PAY',
	'cofidis_desc_line_2' => ' - até 12 prestações sem juros',
	'cofidis_desc_line_3' => 'Será redirecionado para uma página segura a fim de efetuar o pagamento.',
	'cofidis_desc_line_4' => 'Pague a sua encomenda em prestações sem juros nem encargos através do seu cartão de débito ou crédito.',
	'cofidis_desc_line_5' => 'O pagamento das prestações será efetuado no cartão de débito ou crédito do cliente através de solução de pagamento assente em contrato de factoring entre a Cofidis e o Comerciante. Informe-se na Cofidis, registada no Banco de Portugal com o n 921.',



	/* --------------------------- Front Payment mbway -------------------------- */

	'mobile_number' => 'Número Telemóvel',
	'msg_mbway_invalid_number' => 'Número de telemóvel MB WAY inválido.',
	'mbway_status_payment_confirmed' => 'Pagamento confirmado!',
	'mbway_status_payment_rejected_by_user' => 'Pagamento rejeitado pelo utilizador!',
	'mbway_status_payment_expired' => 'Pagamento expirado!',
	'mbway_status_payment_declined' => 'Pagamento recusado!',
	'mbway_status_payment_error' => 'Erro no Pagamento!',
	'notification_sent' => 'Foi enviada uma notificação para a App MB WAY do seu smartphone.',
	'resend_mbway_notification' => 'Reenviar notificação!',



	/* --------------------------- Front Payment pix -------------------------- */

	'pix_name_label' => 'Nome',
	'pix_cpf_label' => 'CPF',
	'pix_email_label' => 'Email',

	'msg_pix_invalid_name' => 'Nome inválido.',
	'msg_pix_invalid_cpf' => 'CPF inválido.',
	'msg_pix_invalid_email' => 'Email inválido.',









];
