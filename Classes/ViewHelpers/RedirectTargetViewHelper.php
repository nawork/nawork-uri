<?php

namespace Nawork\NaworkUri\ViewHelpers;


use Nawork\NaworkUri\Domain\Model\Domain;
use Nawork\NaworkUri\Domain\Model\Language;
use Nawork\NaworkUri\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class RedirectTargetViewHelper extends AbstractViewHelper {
	/**
	 * @param \Nawork\NaworkUri\Domain\Model\Url $url
	 *
	 * @return string
	 */
	public function render($url) {
		$domain = $url->getDomain();
		if ($domain instanceof Domain) {
			$domain = $domain->getUid();
		}

		$pageUid = $url->getPageUid();

		$parameters = $url->getParameters();

		$language = $url->getLanguage();
		if ($language instanceof Language) {
			$language = $language->getUid();
		}
		if ($language === NULL) {
			$language = 0;
		}

		$domainName = $url->getDomain() instanceof Domain ? $url->getDomain()->getDomainname() : GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY');
		ConfigurationUtility::getConfiguration($domainName);
		/* @var $translator \Nawork\NaworkUri\Utility\TransformationUtility */
		$translator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
			'Nawork\\NaworkUri\\Utility\\TransformationUtility'
		);
		$newUrlParameters = array_merge(
			\Nawork\NaworkUri\Utility\GeneralUtility::explode_parameters($parameters),
			array('id' => $pageUid, 'L' => $language)
		);
		// try to find a new url or create one, if it does not exist
		try {
			$newUrl = $translator->params2uri(
				\Nawork\NaworkUri\Utility\GeneralUtility::implode_parameters($newUrlParameters, FALSE),
				FALSE,
				TRUE
			);
			if (substr($newUrl, 0, 1) !== '/') {
				$newUrl = '/' . $newUrl;
			}
		} catch (\Exception $e) {
			$newUrl = 'Exception: ' . $e->getMessage();
		}

		return $newUrl;
	}
}