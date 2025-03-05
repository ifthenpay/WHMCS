class IftpLang {


	static trans(key){
		if (typeof ifthenpaytranslations !== 'undefined' && typeof ifthenpaytranslations[key] !== 'undefined') {

			return ifthenpaytranslations[key];
		}

		return key;
	}

}

export default IftpLang;
