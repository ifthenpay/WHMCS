
import { ifthenpayConfig } from './ifthenpayConfig.js';
import { multibancoConfig } from './multibancoConfig.js';
import { payshopConfig } from './payshopConfig.js';
import { mbwayConfig } from './mbwayConfig.js';
import { ccardConfig } from './ccardConfig.js';
import { cofidisConfig } from './cofidisConfig.js';
import { pixConfig } from './pixConfig.js';
import { ifthenpaygatewayConfig } from './ifthenpaygatewayConfig.js';



document.addEventListener("DOMContentLoaded", () => {

	multibancoConfig();
	payshopConfig();
	mbwayConfig();
	ccardConfig();
	cofidisConfig();
	pixConfig();
	ifthenpaygatewayConfig();

	ifthenpayConfig();

});
