<?php

namespace Plugins\testplugin\assets;

use App\Component\HttpFoundation\CssResponse;
use App\Component\HttpFoundation\JavascriptResponse;
use App\Utils;
use Plugins\testplugin\Config\Config;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



/**
 * Controller für Assets - internal Assets
 * @Route("/testplugin/", name="assets_testplugin_")
 */
class Assets extends AbstractController
{
	/**
	 * @Route("style.css", name="css")
	 * @return Response
	 */
	public function index():Response
	{
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

		$parameters = array();

		return $this->render('/'.CONFIG::PLUGIN_DIR_NAME.'/templates/internal/style-css.css.twig', $parameters, $response);

	}

	/**
	 * @Route("script.js", name="js")
	 * @return Response
	 */
	public function script():Response
	{
		$response = new JavascriptResponse();

		// Workaround: HTTP-Header Cache-Control würde sonst auf private, must-revalidate etc. gesetzt
		Utils::resetCacheControl($response);

		// Lebensdauer der CSS-Datei auf ein Jahr setzen, da ein Zeitstempel an die CSS-Route angehängt wird
		$response->headers->remove('Cache-Control');
		$response->setCache([
			'max_age' => 31536000,
			'immutable' => true,
			'public' => true,
		]);

		$parameters = array();

		return $this->render('/'.CONFIG::PLUGIN_DIR_NAME.'/templates/internal/script.js.twig', $parameters, $response);

	}

	/**
	 * @Route("image", name="img")
	 * @param Request $request
	 * @return BinaryFileResponse
	 */
	public function showImage(Request $request): BinaryFileResponse
	{
		$pluginPath = Utils::getBaseDir()."/plugins/".CONFIG::PLUGIN_DIR_NAME."/assets/images/";

		$img = $request->get("imgname");
		if(!file_exists($pluginPath.basename($img)))
		{
			return new BinaryFileResponse(Utils::getBaseDir() . '/public/img/ccm19_logo_weiss_klein.png', 200, [], true, null, true, true);
		}
		return new BinaryFileResponse($pluginPath.basename($img), 200, [], true, null, true, true);
	}
}

?>
