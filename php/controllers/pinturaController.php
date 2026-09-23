<?php

$isLocal = $_SERVER['SERVER_NAME'] === 'localhost';
ini_set('display_errors', $isLocal ? '1' : '0');
ini_set('display_startup_errors', $isLocal ? '1' : '0');
error_reporting(E_ALL);



class PinturaController
{

    public function pintar(array $dados)
    {
        try {
            $caminhoImagem = $dados['caminho'] ?? null;
            $cor = $dados['cor'] ?? null;

            if (!$caminhoImagem || !$cor) {
                return [
                    "status" => "erro",
                    "mensagem" => "Imagem ou cor não informada"
                ];
            }

            $isLocal = in_array(
                $_SERVER['SERVER_NAME'] ?? '',
                ['localhost', '127.0.0.1', '::1'],
                true
            );

            if ($isLocal) {
                $documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');
                $caminhoFisico =
                    $documentRoot .
                    str_replace('/', DIRECTORY_SEPARATOR, $caminhoImagem);
            } else {
                $caminhoImagem = preg_replace(
                    '#^/php/projeto-pi-front#',
                    '',
                    $caminhoImagem
                );

                $documentRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/\\');

                $caminhoFisico =
                    $documentRoot .
                    DIRECTORY_SEPARATOR .
                    ltrim(
                        str_replace('/', DIRECTORY_SEPARATOR, $caminhoImagem),
                        '/\\'
                    );
            }

            if (!file_exists($caminhoFisico)) {
                return [
                    "status" => "erro",
                    "mensagem" => "Imagem não encontrada: " . $caminhoFisico
                ];
            }

            $mime = mime_content_type($caminhoFisico);

            $arquivo = new CURLFile(
                $caminhoFisico,
                $mime,
                basename($caminhoFisico)
            );

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'http://127.0.0.1:5000/processar',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => [
                    'imagem' => $arquivo,
                    'cor' => $cor
                ],
                CURLOPT_RETURNTRANSFER => true,
            ]);

            $resposta = curl_exec($curl);

            if ($resposta === false) {
                $erro = curl_error($curl);
                curl_close($curl);

                throw new Exception($erro);
            }

            curl_close($curl);

            $resultado = json_decode($resposta, true);


            $blob = $resultado['blob'] ?? null;

            if ($blob) {
                $nomeResultado = uniqid('pintura_', true) . '.webp';

                $caminhoResultado =
                    dirname(__DIR__, 2)
                    . DIRECTORY_SEPARATOR
                    . 'assets'
                    . DIRECTORY_SEPARATOR
                    . 'resultados'
                    . DIRECTORY_SEPARATOR
                    . $nomeResultado;

                file_put_contents(
                    $caminhoResultado,
                    hex2bin($blob)
                );

                $resultado['caminho_resultado'] =
                    '/php/projeto-pi-front/assets/resultados/' . $nomeResultado;
            }

            return $resultado;
        } catch (Exception $e) {
            return [
                "status" => "erro",
                "mensagem" => "Erro ao pintar imagem: " . $e->getMessage()
            ];
        }
    }

    public function pintarMuitas(array $dados)
    {
        try {
            $caminhos = $dados['caminhos'] ?? null;
            $cor = $dados['cor'] ?? null;

            if (!$caminhos || !$cor) {
                return [
                    "status" => "erro",
                    "mensagem" => "Nenhuma imagem enviada"
                ];
            }

            $curl = curl_init();

            $arquivo = new CURLFile(
                $caminhos,
                mime_content_type($caminhos),
                basename($caminhos)
            );

            curl_setopt_array($curl, [
                CURLOPT_URL => 'http://127.0.0.1:5000/processar',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => [
                    'imagem' => $arquivo,
                    'cor' => $cor
                ],
                CURLOPT_RETURNTRANSFER => true,
            ]);

            $resposta = curl_exec($curl);

            if ($resposta === false) {
                throw new Exception(curl_error($curl));
            }

            curl_close($curl);

            $dados = json_decode($resposta, true);
        } catch (Exception $e) {
            return (["status" => "erro", "mensagem" => "Erro ao pintar imagem: " . $e->getMessage()]);
        }
    }
}
