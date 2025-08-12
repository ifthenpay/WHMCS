<?php

return [

	/* ------------------------------ Admin Common ------------------------------ */

	'your_current_version' => 'Su versión actual es',
	'current_version_installed' => 'La versión actual instalada es',
	'a_new_version' => 'Una nueva versión',
	'is_available' => 'está disponible.',
	'download' => 'Descargar',
	'module_up_to_date' => 'Su módulo está actualizado.',
	'backoffice_key' => 'Clave de Backoffice',
	'reset' => 'Restablecer',
	'callback' => 'Callback',
	'callback_desc' => 'Activar para habilitar Callback.',
	'show_payment_icon' => 'Mostrar Icono de Pago en la Caja',
	'show_payment_icon_desc' => 'Activar para mostrar el icono de pago durante la caja.',
	'deadline' => 'Plazo',
	'callback_inactive' => 'Callback Inactivo',
	'callback_active' => 'Callback Activo',
	'anti_phishing_key' => 'Clave Anti-phishing:',
	'callback_url' => 'URL de Callback:',
	'version' => 'Versión',
	'no_deadline' => 'Sin plazo',
	'min_amount' => 'Valor Mínimo',
	'max_amount' => 'Valor Máximo',
	'min_amount_desc' => 'Mostrar este método de pago solo para pedidos con un valor total mayor que el valor insertado. Dejar vacío para mostrar siempre este método de pago.',
	'max_amount_desc' => 'Mostrar este método de pago solo para pedidos con un valor total menor que el valor insertado. Dejar vacío para mostrar siempre este método de pago.',
	'cofidis_min_amount_desc' => 'Mostrar este método de pago solo para pedidos con un valor total mayor que el valor insertado. El valor ingresado no puede ser menor que el valor definido en el backoffice de ifthenpay.',
	'cofidis_max_amount_desc' => 'Mostrar este método de pago solo para pedidos con un valor total menor que el valor insertado. El valor ingresado no puede ser mayor que el valor definido en el backoffice de ifthenpay.',
	'none' => 'Ninguno',
	'msg_are_sure_reset_config' => 'Esta acción borrará la configuración actual de este método de pago, confirme para continuar.',



	/* ---------------------------- Admin Common ---------------------------- */

	'msg_invalid_minimum_amount' => 'Valor Mínimo Inválido',
	'msg_invalid_maximum_amount' => 'Valor Máximo Inválido',
	'msg_invalid_min_max_amount' => 'El Valor Mínimo debe ser menor que el Valor Máximo',
	'msg_invalid_deadline' => 'Plazo Inválido',
	'msg_invalid_backoffice_key' => 'Clave de Backoffice Inválida.',
	'msg_invalid_backoffice_key_example' => 'Clave de Backoffice Inválida. Debe tener el formato: 1111-1111-1111-1111.',
	'msg_request_new_account' => '¿Desea solicitar una nueva Cuenta?',
	'msg_invalid_key' => 'Clave Inválida',
	'msg_success_sending_account_request' => 'Solicitud de cuenta enviada con éxito.',
	'msg_error_sending_account_request' => 'Error al enviar el correo electrónico de solicitud de cuenta, para obtener más información, consulte el archivo general_logs en modules/gateways/ifthenpaylib/lib/Log/logs/general_logs.log.',



	/* ---------------------------- Admin Multibanco ---------------------------- */

	'entity' => 'Entidad',
	'subentity' => 'Sub-Entidad',
	'multibanco_dynamic_reference' => 'Referencia Dinámica',
	'multibanco_deadline_desc' => 'Solo disponible para Multibanco Dinámico. Seleccione "0" para que la referencia expire el mismo día de su creación a las 23:59. Seleccione "Sin plazo" para que nunca expire.',
	'cancel_multibanco' => 'Cancelar Pedido Multibanco',
	'cancel_multibanco_desc' => 'Activar para cancelar automáticamente los pedidos de Multibanco si la referencia ha expirado. Solo funciona con referencias dinámicas. Se ejecuta junto con el cron diario.',
	'msg_invalid_entity' => 'Entidad Inválida',
	'msg_invalid_subentity' => 'Sub-Entidad Inválida',
	'msg_no_multibanco_accounts_found' => 'No se encontraron cuentas de Multibanco para su Clave de Backoffice.',
	'msg_error_updating_multibanco_database' => 'Error al actualizar la tabla de la base de datos de multibanco, para obtener más información, consulte el archivo multibanco en modules/gateways/ifthenpaylib/lib/Log/logs/multibanco.log.',



	/* ---------------------------- Admin Payshop ---------------------------- */

	'payshop_key' => 'Clave de Payshop',
	'payshop_deadline_desc' => 'Ingrese los días hasta la fecha límite, entre 1 y 99 días. Deje vacío para que nunca expire.',
	'msg_invalid_payshop_key' => 'Clave de Payshop Inválida',
	'cancel_payshop' => 'Cancelar Pedido de Payshop',
	'cancel_payshop_desc' => 'Activar para cancelar automáticamente los pedidos de Payshop si la referencia ha expirado. Se ejecuta junto con el cron diario.',
	'msg_no_payshop_accounts_found' => 'No se encontraron cuentas de Payshop para su Clave de Backoffice.',
	'msg_error_updating_payshop_database' => 'Error al actualizar la tabla de la base de datos de payshop, para obtener más información, consulte el archivo payshop en modules/gateways/ifthenpaylib/lib/Log/logs/payshop.log.',



	/* ---------------------------- Admin MB WAY ---------------------------- */

	'mbway_key' => 'Clave de MB WAY',
	'show_mbway_countdown' => 'Mostrar Cuenta Regresiva de MB WAY',
	'show_mbway_countdown_desc' => 'Activar para mostrar la cuenta regresiva de 4 minutos de MB WAY en la factura.',
	'cancel_mbway' => 'Cancelar Pedido de MB WAY',
	'cancel_mbway_desc' => 'Activar para cancelar automáticamente los pedidos de MB WAY si no se pagan 30 minutos después de la creación. Se ejecuta junto con el cron diario.',
	'msg_invalid_mbway_key' => 'Clave de MB WAY Inválida',
	'msg_no_mbway_accounts_found' => 'No se encontraron cuentas de MB WAY para su Clave de Backoffice.',
	'mbway_payment_invoice' => 'Factura de Pago MB WAY {{invoice_id}}',
	'notification_description' => 'Descripción de la Notificación de la Aplicación',
	'notification_description_desc' => 'Pequeña descripción mostrada al cliente en la aplicación de teléfono MB WAY, use la cadena {{invoice_id}} para pasar el número invoice_id.',
	'msg_invalid_notification_description' => 'Descripción de Notificación Inválida, no puede contener caracteres especiales además de {{invoice_id}},e no puede tener más de 100 caracteres.',
	'msg_error_updating_mbway_database' => 'Error al actualizar la tabla de la base de datos de mbway, para obtener más información, consulte el archivo mbway en modules/gateways/ifthenpaylib/lib/Log/logs/mbway.log.',



	/* ---------------------------- Admin CCard ---------------------------- */

	'ccard_key' => 'Clave de Tarjeta de Crédito',
	'msg_invalid_ccard_key' => 'Clave de Tarjeta de Crédito Inválida',
	'cancel_ccard' => 'Cancelar Pedido de Tarjeta de Crédito',
	'cancel_ccard_desc' => 'Activar para cancelar automáticamente los pedidos de Tarjeta de Crédito si no se pagan 30 minutos después de la creación. Se ejecuta junto con el cron diario.',
	'msg_no_ccard_accounts_found' => 'No se encontraron cuentas de Tarjeta de Crédito para su Clave de Backoffice.',
	'msg_error_updating_ccard_database' => 'Error al actualizar la tabla de la base de datos de ccard, para obtener más información, consulte el archivo ccard en modules/gateways/ifthenpaylib/lib/Log/logs/ccard.log.',



	/* ---------------------------- Admin Cofidis ---------------------------- */

	'cofidis_key' => 'Clave de Cofidis Pay',
	'msg_invalid_cofidis_key' => 'Clave de Cofidis Pay Inválida',
	'cancel_cofidis' => 'Cancelar Pedido de Cofidis Pay',
	'cancel_cofidis_desc' => 'Activar para cancelar automáticamente los pedidos de Cofidis Pay si no se pagan 60 minutos después de la creación. Se ejecuta junto con el cron diario.',
	'msg_no_cofidis_accounts_found' => 'No se encontraron cuentas de Cofidis para su Clave de Backoffice.',
	'msg_error_updating_cofidis_database' => 'Error al actualizar la tabla de la base de datos de cofidis, para obtener más información, consulte el archivo cofidis en modules/gateways/ifthenpaylib/lib/Log/logs/cofidis.log.',
	'msg_invalid_minimum_amount_ifthenpay' => 'Valor Mínimo Inválido, debe ser mayor o igual al valor definido en el backoffice ifthenpay.',
	'msg_invalid_maximum_amount_ifthenpay' => 'Valor Máximo Inválido, debe ser menor o igual al valor definido en el backoffice ifthenpay.',

	/* ---------------------------- Admin ifthenpaygateway ---------------------------- */

	'ifthenpaygateway_key' => 'Clave de Gateway',
	'ifthenpaygateway_payment_methods' => 'Métodos de Pago',
	'ifthenpaygateway_default_payment_method' => 'Método de Pago Predeterminado',
	'show_icon_on_default' => 'ON - mostrar icono predeterminado',
	'show_icon_on_composite_image' => 'ON - mostrar icono compuesto',
	'show_icon_off_method_name' => 'OFF - mostrar título del método',
	'ifthenpaygateway_deadline_desc' => 'Ingrese los días hasta la fecha límite, entre 1 y 99 días. Deje vacío para que nunca expire.',
	'close_btn_label' => 'Texto del Botón Cerrar Gateway',
	'close_btn_label_desc' => 'Reemplaza el texto del botón de retorno en la página del gateway. Deje vacío para usar el valor predeterminado.',
	'ifthenpaygateway_description' => 'Descripción',
	'ifthenpaygateway_description_desc' => 'Descripción mostrada en la página del gateway ifthenpay debajo del valor.',
	'btn_request_gateway_method' => 'Solicitar Método de Gateway',
	'ifthenpaygateway_select_a_gateway_key' => 'Seleccione una clave de Ifthenpay Gateway para ver este campo.',
	'msg_invalid_ifthenpaygateway_key' => 'Clave de Ifthenpay Gateway Inválida',
	'cancel_ifthenpaygateway' => 'Cancelar Pedido de Ifthenpay Gateway',
	'cancel_ifthenpaygateway_desc' => 'Activar para cancelar automáticamente los pedidos de Ifthenpay Gateway si no se pagan 60 minutos después de la creación. Se ejecuta junto con el cron diario.',
	'msg_no_ifthenpaygateway_accounts_found' => 'No se encontraron cuentas de Ifthenpay Gateway para su Clave de Backoffice.',
	'msg_request_new_gateway_method' => '¿Desea solicitar un Método de Gateway {%method%}?',
	'msg_error_updating_ifthenpaygateway_database' => 'Error al actualizar la tabla de la base de datos de ifthenpaygateway, para obtener más información, consulte el archivo ifthenpaygateway en modules/gateways/ifthenpaylib/lib/Log/logs/ifthenpaygateway.log.',
	'msg_invalid_ifthenpaygateway_description' => 'Descripción Inválida, no puede tener más de 200 caracteres.',
	'msg_invalid_ifthenpaygateway_close_btn' => 'Texto del Botón Cerrar Gateway Inválida, no puede tener más de 50 caracteres.',
	'msg_invalid_gateway_methods' => 'No se seleccionaron métodos de pago.',


	/* ---------------------------- Admin Pix ---------------------------- */

	'pix_key' => 'Clave Pix',
	'msg_invalid_pix_key' => 'Clave Pix Inválida',
	'cancel_pix' => 'Cancelar Pedido Pix',
	'cancel_pix_desc' => 'Activar para cancelar automáticamente los pedidos Pix si no se pagan 30 minutos después de la creación. Se ejecuta junto con el cron diario.',
	'msg_no_pix_accounts_found' => 'No se encontraron cuentas Pix para su Clave de Backoffice.',
	'msg_error_updating_pix_database' => 'Error al actualizar la tabla de la base de datos pix, para obtener más información, consulte el archivo pix en modules/gateways/ifthenpaylib/lib/Log/logs/pix.log.',



	/* -------------------------- Front Payment Details e forms ------------------------- */

	'multibanco' => 'Multibanco',
	'mbway' => 'MB WAY',
	'payshop' => 'Payshop',
	'ccard' => 'Tarjeta de Crédito',
	'cofidis' => 'Cofidis Pay',
	'pix' => 'Pix',
	'ifthenpaygateway' => 'Ifthenpay Gateway',

	'pay_btn' => 'Pagar',
	'pay' => 'Pagar',
	'pay_by' => 'Pagar por',
	'pay_with' => 'Pagar con',
	'entity_label' => 'Entidad',
	'reference_label' => 'Referencia',
	'deadline_label' => 'Plazo',
	'amount_label' => 'Valor',
	'payment_process_completed' => 'Proceso de pago completado.',
	'wait_for_payment_verification' => 'La verificación está en curso.',



	/* --------------------------- Front Payment cofidis -------------------------- */

	'cofidis_desc_line_1' => 'COFIDIS PAY',
	'cofidis_desc_line_2' => ' - hasta 12 cuotas sin intereses',
	'cofidis_desc_line_3' => 'Será redirigido a una página segura para realizar el pago.',
	'cofidis_desc_line_4' => 'Pague su pedido en cuotas sin intereses ni cargos a través de su tarjeta de débito o crédito.',
	'cofidis_desc_line_5' => 'El pago de las cuotas se efectuará en la tarjeta de débito o crédito del cliente a través de una solución de pago basada en un contrato de factoring entre Cofidis y el Comerciante. Infórmese en Cofidis, registrada en el Banco de Portugal con el n.º 921.',



	/* --------------------------- Front Payment mbway -------------------------- */

	'mobile_number' => 'Número de Móvil',
	'msg_mbway_invalid_number' => 'Número de móvil MB WAY inválido.',
	'mbway_status_payment_confirmed' => '¡Pago confirmado!',
	'mbway_status_payment_rejected_by_user' => '¡Pago rechazado por el usuario!',
	'mbway_status_payment_expired' => '¡Pago expirado!',
	'mbway_status_payment_declined' => '¡Pago denegado!',
	'mbway_status_payment_error' => '¡Error de pago!',
	'notification_sent' => 'Se ha enviado una notificación a su aplicación de smartphone MB WAY.',
	'resend_mbway_notification' => '¡Reenviar notificación!',



	/* --------------------------- Front Payment pix -------------------------- */

	'pix_name_label' => 'Nombre',
	'pix_cpf_label' => 'CPF',
	'pix_email_label' => 'Correo Electrónico',

	'msg_pix_invalid_name' => 'Nombre Inválido.',
	'msg_pix_invalid_cpf' => 'CPF Inválido.',
	'msg_pix_invalid_email' => 'Correo Electrónico Inválido.',

];
