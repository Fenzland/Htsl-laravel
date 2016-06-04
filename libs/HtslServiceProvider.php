<?php

namespace Htsl\ForLaravel;

use Htsl\Htsl;
use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Compilers\Compiler;
use Illuminate\View\Compilers\CompilerInterface;

////////////////////////////////////////////////////////////////

class HtslServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$templatePath= dirname(__DIR__).'/templates';
		$this->publishes([
			"$templatePath/config.php.template"=> config_path('htsl.php'),
			"$templatePath/TExtension.php.template"=> app_path('Htsl/TExtension.php'),
		]);

		$app = $this->app;

		// Add the .htsl.php extension and register the Haml compiler with
		// Laravel's view engine resolver
		$app['view']->addExtension('htsl', 'htsl', function() use ($app) {
			return trait_exists('\App\Htsl\TExtension')?
			new class($app['htsl.compiler']) extends CompilerEngine{
				use \App\Htsl\TExtension;
			}:
			new CompilerEngine($app['htsl.compiler']);
		});
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('htsl.compiler', function( $app ) {

			$environment = config('htsl.environment');

			if(is_null($environment) || !trait_exists('\App\Htsl\TExtension'))
				throw new \Exception('Please publish the vendor via: artisan vendor:publish');

			return new class($app) extends Compiler implements CompilerInterface
			{
				private $htsl;
				public function __construct( $app )
				{
					$this->htsl= new Htsl(config('htsl'));
					$this->files= $app['files'];
					$this->cachePath= $app['config']['view.compiled'];

					$this->htsl->setBasePath($app['config']['view.paths'][0]);
				}
				public function compile( $path=null )
				{
					if( $path ){
						$this->path= $path;
					}
					if( !is_null($this->cachePath) ){
						$this->htsl->compile($this->path,$this->getCompiledPath($this->path));
					}
				}
				public function isExpired( $path )
				{
					return $this->htsl->isDebug()?:parent::isExpired($path);
				}
			};
		});
	}
}
