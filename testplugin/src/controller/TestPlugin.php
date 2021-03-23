<?php

namespace Plugins\testplugin\src\controller;

use App\Controller\DomainDependantController;
use App\Model\CsrfToken;
use App\Model\Domain;
use App\Model\Locale;
use Plugins\testplugin\Config\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Plugins\testplugin\src\model\TestPluginModell;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Controller for Testplugin - domain dependent
 * @Route("/domains/{_domainId}/plugins/testplugin/", name="app_testplugin_")
 */
class TestPlugin extends DomainDependantController
{
	/**
	 * @Route("index", name="index")
	 * @return Response
	 */
	public function index(): Response
	{
		//check if is activated
		if(!TestPluginModell::isActivated(Config::PLUGIN_UUID))
		{
			//Nope - then go to dashboard...
			return $this->redirectToRoute('app_dashboard');
		}

		//Gibt noch keinen Eintrag...
		if (empty(TestPluginModell::all()))
		{
			//Create item for test purpose...
			$this->createTestPluginEntry();
		}

		/** @var Locale[] $locales */
		$locales = Locale::enabledLocales();

		//Render template with all Data
		return $this->render('/testplugin/templates/internal/index.html.twig', [
			"plugin" =>TestPluginModell::getXItem("0"),
			'locales' => $locales,
			'csrfToken' => CsrfToken::get(),
		]);
	}


	/**
	 * @Route("testplugin_save", name="save"), methods={"HEAD","POST"})
	 * @param Request $request
	 * @return Response
	 */
	public function editSettings(Request $request, TranslatorInterface $translator): Response
	{
		//get Field Data
		if(is_array(TestPluginModell::getFieldNames()))
		{
			foreach (TestPluginModell::getFieldNames() as $v)
			{
				$postPLuginData[$v]=$request->get($v);
			}
		}

		//Does not exist -> then create...
		if (TestPluginModell::exists($request->get("id")) == false) {

			//Create an entry for json file
			$PluginsClass = TestPluginModell::create();

			//Set Data from array in new entry
			$PluginsClass->setArray($postPLuginData);

			//finally save the data to the json file
			$PluginsClass->flush();

			//External CSS and JS
			$this->setCssAndJSForExternal();

			//Set Flash Message
			$this->addFlash('success', $translator->trans('A new Entry succesfully created.'));

			return $this->redirectToRoute('app_testplugin_index');

		}
		//get Plugin Data
		$pluginData = TestPluginModell::find($request->get("id"));

		//Set Data and save
		$pluginData->setArray($postPLuginData);
		$pluginData->flush();

		//External CSS and JS
		$this->setCssAndJSForExternal();


		//Set edited Flash Message
		$this->addFlash('success', $translator->trans('Existing Entry succesfully edited.'));

		return $this->redirectToRoute('app_testplugin_index');

	}

	/**
	 * @return bool
	 */
	private function setCssAndJSForExternal()
	{
		//Set Plugin CSS for this Domain
		$domain = Domain::activeDomain();
		$cssData = array("app_testplugin_css");
		$domain->addExtraCSS($cssData);

		$jsData = array("app_testplugin_js");
		$domain->addExtraJS($jsData);
		$domain->save();

		return true;
	}


	/**
	 * Most basic creation of an entry...
	 *
	 * @return bool
	 * Create a new entry with senseless data
	 */
	public function createTestPluginEntry(): bool
	{
		//Set Some Data
		$postPLuginData = array("exampleInputName"=>"Testdata 1");

		//Create an entry for json file
		$PluginsClass = TestPluginModell::create();

		//Set Data from array in new entry
		$PluginsClass->setArray($postPLuginData);

		//finally save the data to the json file
		$PluginsClass->flush();

		return true;
	}
}
?>
