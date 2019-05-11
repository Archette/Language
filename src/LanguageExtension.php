<?php

declare(strict_types=1);

namespace Archette\Language;

use Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Rixafy\Language\Command\LanguageUpdateCommand;
use Rixafy\Language\LanguageFacade;
use Rixafy\Language\LanguageFactory;
use Rixafy\Language\LanguageProvider;
use Rixafy\Language\LanguageRepository;

class LanguageExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'defaultLanguage' => Expect::string('en')
		]);
	}

	public function beforeCompile(): void
	{
		/** @var ServiceDefinition $serviceDefinition */
		$serviceDefinition = $this->getContainerBuilder()->getDefinitionByType(AnnotationDriver::class);
		$serviceDefinition->addSetup('addPaths', [['vendor/rixafy/language']]);
	}

	public function loadConfiguration(): void
	{
		$this->getContainerBuilder()->addDefinition($this->prefix('languageFacade'))
			->setFactory(LanguageFacade::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('languageRepository'))
			->setFactory(LanguageRepository::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('languageFactory'))
			->setFactory(LanguageFactory::class);

		$this->getContainerBuilder()->addDefinition($this->prefix('languageProvider'))
			->setFactory(LanguageProvider::class)
			->addSetup('provide', [$this->config->defaultLanguage]);

		$this->getContainerBuilder()->addDefinition($this->prefix('languageUpdateCommand'))
			->setFactory(LanguageUpdateCommand::class)
			->addTag('console.command', 'rixafy:language:update');
	}
}
