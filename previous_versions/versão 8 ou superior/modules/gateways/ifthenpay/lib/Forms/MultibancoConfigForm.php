<?php

declare(strict_types=1);

namespace WHMCS\Module\Gateway\ifthenpay\Forms;

if (!defined("WHMCS")) {
	die("This file cannot be accessed directly");
}

use WHMCS\Module\Gateway\ifthenpay\Forms\ConfigForm;
use WHMCS\Module\Gateway\Ifthenpay\Payments\Gateway;
use WHMCS\Module\Gateway\ifthenpay\Forms\Composite\Elements\Input;

class MultibancoConfigForm extends ConfigForm
{
	protected $paymentMethod = Gateway::MULTIBANCO;

	protected function checkConfigValues(): array
	{
		$entidade = $this->gatewayVars['entidade'];
		$subEntidade = $this->gatewayVars['subEntidade'];
		return $entidade && $subEntidade ? ['entidade' => $entidade, 'subEntidade' => $subEntidade] : [];
	}

	private function addValidatyCancelMultibancoOrderInput(): void
	{
		// the '00' option is necessary because there was detected a behaviour where, if the value 0 was present, it would always be selected regardless of the value of the database. This requires there to be a conversion from '00' to 0 when ever using the value.
		$options = [
			'' => \AdminLang::trans('configMultibancoNoDeadlineName'),
			'00' => '0',
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5',
			'6' => '6',
			'7' => '7',
			'8' => '8',
			'9' => '9',
			'10' => '10',
			'11' => '11',
			'12' => '12',
			'13' => '13',
			'14' => '14',
			'15' => '15',
			'16' => '16',
			'17' => '17',
			'18' => '18',
			'19' => '19',
			'20' => '20',
			'21' => '21',
			'22' => '22',
			'23' => '23',
			'24' => '24',
			'25' => '25',
			'26' => '26',
			'27' => '27',
			'28' => '28',
			'29' => '29',
			'30' => '30',
			'31' => '31',
			'45' => '45',
			'60' => '60',
			'90' => '90',
			'120' => '120',
		];


		$this->form->add($this->ioc->makeWith(Input::class, [
			'friendlyName' => \AdminLang::trans('multibancoDeadline'),
			'type' => 'dropdown',
			'name' => 'multibancoValidity',
			'options' => $options,
			'description' => \AdminLang::trans('multibancoDeadlineDescription'),
		]));
	}

	protected function addDynamicMbInputs(): void
	{
		if (!$this->ifthenpayGateway->checkDynamicMb($this->ifthenpayUserAccount)) {
			$this->form->add($this->ioc->makeWith(Input::class, [
				'type' => 'System',
				'name' => 'UsageNotes',
				'value' => '<button type="button" class="btn btn-danger">' . \AdminLang::trans('notMultibancoDeadline') .
				'</button>' . '<br><br>' . \AdminLang::trans('requestMultibancoDeadline') .
				':<br><a id="requestMultibancoDynamicAccount" class="btn btn-success" href="">' . \AdminLang::trans('sendEmailNewAccount') . '</a><br><br>'
			]));
			$this->ifthenpayLogger->info('user with no multibanco deadline field notification added to form with success');
		} else {
			$this->ifthenpaySql->addRequestIdValidadeToMultibancoTable();
			$this->addValidatyCancelMultibancoOrderInput();
		}

	}

	protected function addPaymentInputsToForm(): void
	{
		if (!$this->configValues) {
			$this->addToOptions();
		} else {
			$this->options[$this->configValues['entidade']] = $this->configValues['entidade'];
			$this->addToOptions(true);
		}

		// cancel multibanco order (checkbox)
		if ($this->ifthenpayGateway->checkDynamicMb($this->ifthenpayUserAccount)) {
			$this->form->add($this->ioc->makeWith(Input::class, [
				'friendlyName' => \AdminLang::trans('cancelMultibancoOrder'),
				'type' => 'yesno',
				'name' => 'cancelMultibancoOrder',
				'description' => \AdminLang::trans('cancelMultibancoOrderDescription'),
			]));
		}

		// multibanco entity (dropdown)

		// rename dynamic entity name if exists
		if (isset($this->options['MB'])) {
			$this->options['MB'] = \ADMINLANG::trans('configMultibancoDynamicEntityName');
		}

		$this->form->add($this->ioc->makeWith(Input::class, [
			'friendlyName' => \AdminLang::trans('multibancoEntity'),
			'type' => 'dropdown',
			'name' => 'entidade',
			'options' => $this->options,
			'description' => \AdminLang::trans('multibancoEntityDescription'),
		]));
		$this->ifthenpayLogger->info('multibanco entidade input config added with success to form', ['options' => $this->options]);


		// multibanco subentity (dropdown)
		if (!$this->configValues) {
			$subEntityOptions = [
				'' => ''
			];
		} else {
			$entidade = $this->configValues['entidade'];

			$subentities = $this->ifthenpayGateway->getSubEntitiesByEntity($entidade);

			foreach ($subentities as $subentity) {
				$subEntityOptions[$subentity] = $subentity;
			}
		}

		$this->form->add($this->ioc->makeWith(Input::class, [
			'friendlyName' => \AdminLang::trans('multibancoSubEntity'),
			'type' => 'dropdown',
			'name' => 'subEntidade',
			'options' => $subEntityOptions,
			'description' => \AdminLang::trans('multibancoSubEntityDescription'),
		]));

		// multibanco validaty (dropdown) and request new account (button) if doesn't exist
		$this->addDynamicMbInputs();
		$this->ifthenpayLogger->info('multibanco subentidade input config added with success to form', ['options' => $this->options]);
	}

	public function setGatewayBuilderData(): void
	{
		if ($this->configValues) {
			parent::setGatewayBuilderData();
			$this->gatewayDataBuilder->setEntidade($this->configValues['entidade']);
			$this->gatewayDataBuilder->setSubEntidade($this->configValues['subEntidade']);
		}
	}
}