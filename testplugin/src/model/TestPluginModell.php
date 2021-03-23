<?php

namespace Plugins\testplugin\src\model;

use App\JsonConfig;
use App\Model\Locale;
use App\Component\Curl;
use App\Component\Tcf\v2\Model\Vendor as TcfVendor;
use App\Component\Tcf\v2\Model\Purpose as TcfPurpose;
use App\Model\Plugins as PluginModel;
use App\Utils;
use App\Model\Model;

class TestPluginModell extends Model
{
	/** @var JsonConfig|null $repository */
	protected static $repository = null;

	/** @var string $repositoryFilename */
	protected static $repositoryFilename = 'cm-testplugin.json';

	/** @var bool $domainSpecificRepository */
	protected static $domainSpecificRepository = true;

	/**
	 * @param string $id
	 */
	protected function __construct(string $id)
	{
		parent::__construct($id);
		$this->assets = [];
	}

	/**
	 * @param $name
	 * @param $arguments
	 * @return string
	 */
	public function __call($name, $arguments)
	{
		return (string)$this->get($name,"");
	}

	/**
	 * Generic String Catcher...
	 *
	 * @param string $varname
	 * @return string
	 */
	public function getStringVar(string $varname = "")
	{
		if(!empty($varname))
		{
			return (string)$this->get($varname,"");
		}

	}


	/**
	 * @return string[]
	 */
	public function getFieldNames(): array
	{
		return array(
			"exampleInputName",
			"expampleCheckboxData",
			"exampleSelectBox"
		);

	}



	/**
	 * @param array $postData
	 * @return true
	 */
	public function setArray(array $postData): self
	{
		foreach($postData as $k=>$v)
		{
			$this->set($k, $v);
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public static function isActivated($search=""): bool
	{
		$returnVar =  array_filter(PluginModel::all(), function ($plugin) use ($search) {
			if (stristr($plugin->getpluginUID(),$search))
			{
				if ($plugin->getStringVar("activateForAllAccounts")=="on")
				{
					return true;
				}
				if ($plugin->getStringVar("activateForAllAccountsOnTheirOwn")=="on")
				{
					return true;
				}
			}
		});

		if($returnVar==true)
		{
			return true;
		}
		return false;
	}


}
