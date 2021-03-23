<?php

namespace Plugins\testplugin\src\controller;

use App\Component\HttpFoundation\JavascriptResponse;
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
class TestPluginExternalJS extends ExternalController
{
	/**
	 * @Route("testplugin.js", name="js")
	 * @return Response
	 */
	public function index(): Response
	{
		//check if is activated
		if(!TestPluginModell::isActivated(Config::PLUGIN_UUID))
		{
			//Nope - then go to dashboard...
			return new Response('JSFile not active.', 404);
		}
		$response = new JavascriptResponse();
		$response->headers->set('X-Robots-Tag', 'noindex, nofollow');

		// Workaround: HTTP-Header Cache-Control würde sonst auf private, must-revalidate etc. gesetzt
		Utils::resetCacheControl($response);

		//Render template with all Data
		return $this->render('/testplugin/templates/external/ExternalJs.js.twig', [], $response);
	}

}
?>
