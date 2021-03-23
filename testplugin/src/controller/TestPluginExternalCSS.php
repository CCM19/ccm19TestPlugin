<?php

namespace Plugins\testplugin\src\controller;

use App\Component\HttpFoundation\CssResponse;
use App\Controller\ExternalController;
use App\Utils;
use Plugins\testplugin\Config\Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Plugins\testplugin\src\model\TestPluginModell;


/**
 * Controller for Testplugin - domain dependent
 * @Route("/testplugin/", name="app_testplugin_")
 */
class TestPluginExternalCSS extends ExternalController
{
	/**
	 * @Route("testplugin.css", name="css")
	 * @return CssResponse
	 */
	public function index(): CssResponse
	{

		//check if is activated
		if(!TestPluginModell::isActivated(Config::PLUGIN_UUID))
		{
			//Nope - then go to dashboard...
			return new Response('CssFile not active.', 404);
		}

		$response = new CssResponse();

		// Workaround: HTTP-Header Cache-Control würde sonst auf private, must-revalidate etc. gesetzt
		Utils::resetCacheControl($response);

		// Lebensdauer der CSS-Datei auf ein Jahr setzen, da ein Zeitstempel an die CSS-Route angehängt wird
		$response->headers->remove('Cache-Control');
		$response->setCache([
			'max_age' => 31536000,
			'immutable' => true,
			'public' => true,
		]);

		//Render CSS template with all Data
		return $this->render('/testplugin/templates/external/ExternalCss.css.twig', [], $response);
	}
}
?>
