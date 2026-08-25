<?php

require_once __DIR__ . '/../php/model/endereco.php';
require_once __DIR__ . '/../php/model/pessoa.php';
require_once __DIR__ . '/../php/model/funcionario.php';
require_once __DIR__ . '/../php/model/corretor.php';
require_once __DIR__ . '/../php/model/cliente.php';
require_once __DIR__ . '/../php/model/proprietario.php';
require_once __DIR__ . '/../php/model/anuncio.php';
require_once __DIR__ . '/../php/model/condominio.php';
require_once __DIR__ . '/../php/model/imovel.php';
require_once __DIR__ . '/../php/services/imovelService.php';
require_once __DIR__ . '/../php/services/pessoaService.php';

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class ServiceTest extends TestCase
{
	public function testServiceClassesExposeExpectedMethods(): void
	{
		$this->assertTrue(method_exists(ImovelService::class, 'cadastrar'));
		$this->assertTrue(method_exists(ImovelService::class, 'atualizar'));
		$this->assertTrue(method_exists(ImovelService::class, 'atualizarAnuncio'));
		$this->assertTrue(method_exists(PessoaService::class, 'cadastrar'));
		$this->assertTrue(method_exists(PessoaService::class, 'atualizar'));
	}
}
