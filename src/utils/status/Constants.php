<?php
	namespace Api\utils\status;

	class Constants
	{
	    const URL_BASE="http://173.249.21.6/v1";
		const CREATE = 201;
		const SERVER_ERROR = 500;
		const REDIRECT_FOUND = 302;
		const NO_CONTENT = 204;
		const NO_AUTHORIZE=401;

		const PENDING="pediente";
		const ACEPTED="aceptado";
		const REJECTED="rechazado";

		const Ok="ok";
		const FAIL_AUTH="failAuth";
        const ERROR="error";
        const NO_EXIST="noExist";
        const USER_EXIST="userExist";
        const SESSION_CLOSED="closed";
        const NO_MATCH="noMatch";
        //Generadores
        const GENERADOR_NUMERICO="0123456789";
        const GENERADOR_FULL="0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/";
	}
?>