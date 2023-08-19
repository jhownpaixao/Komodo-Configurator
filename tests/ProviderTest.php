<?php
namespace Test;

use Komodo\Configurator\ConfigurationProvider;
use PHPUnit\Framework\TestCase;

final class ProviderTest extends TestCase
{
    public function testProvider()
    {

        ConfigurationProvider::init(__DIR__ . '/Config');

        // #Configuration test
        $var = ConfigurationProvider::get('TesteConfig');
        $this->assertIsObject($var);
        $this->assertIsString($var->string);
        $this->assertIsArray($var->array);
        $this->assertIsObject($var->assoc);
        $this->assertIsBool($var->bool);
        $this->assertIsInt($var->int);

        // #Environment vaariables test
        $this->assertIsString($_ENV[ 'DB_HOST' ]);
        $this->assertIsString($_ENV[ 'DB_USER' ]);
        $this->assertIsString($_ENV[ 'DB_PASS' ]);
        $this->assertIsString($_ENV[ 'DB_NAME' ]);
        $this->assertIsString($_ENV[ 'APP_FOLDER' ]);

        $this->assertEquals('localhost', $_ENV[ 'DB_HOST' ]);
        $this->assertEquals('root', $_ENV[ 'DB_USER' ]);
        $this->assertEquals('', $_ENV[ 'DB_PASS' ]);
        $this->assertEquals('nameOfDataBase', $_ENV[ 'DB_NAME' ]);
        $this->assertEquals('/', $_ENV[ 'APP_FOLDER' ]);
    }
}
