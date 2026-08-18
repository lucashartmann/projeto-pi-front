import { listarPessoas } from "./modules/usuarios.js";
import { listarImoveis } from "./modules/imoveis.js";

let quantImoveis = 0;
let quantImoveisPendentes = 0;
let quantImoveisVendidos = 0;
let quantImoveisAlugados = 0;
let quantImoveisParaAluguel = 0;
let quantImoveisParaVenda = 0;
let quantImoveisParaVendaAluguel = 0;

let quantFuncionarios = 0;
let quantCaptadores = 0;
let quantCorretores = 0;
let quantAdministradores = 0;
let quantClientes = 0;
let quantProprietarios = 0;
let quantVistoriadores = 0;
let quantFinanceiros = 0;

let faturamentoTotal = 0;
let faturamentoMensal = 0;
let faturamentoAnual = 0;
let lucroMensal = 0;
let lucroAnual = 0;


document.addEventListener("DOMContentLoaded", async () => {
    const pessoas = await listarPessoas();

    const dataAtual = new Date();
    let divGrafico = null;
    let canvasGrafico = null;

    const divPai = document.createElement('div');
    divPai.id = 'container-pai-graficos';

    if (pessoas) {
        quantFuncionarios = pessoas.filter(pessoa => pessoa.tipo === "FUNCIONARIO").length;
        quantClientes = pessoas.filter(pessoa => pessoa.tipo === "CLIENTE").length;
        quantProprietarios = pessoas.filter(pessoa => pessoa.tipo === "PROPRIETARIO").length;
        quantCorretores = pessoas.filter(pessoa => pessoa.tipo === "CORRETOR").length;
        quantCaptadores = pessoas.filter(pessoa => pessoa.tipo === "CAPTADOR").length;
        quantAdministradores = pessoas.filter(pessoa => pessoa.tipo === "ADMINISTRADOR").length;
        quantVistoriadores = pessoas.filter(pessoa => pessoa.tipo === "VISTORIADOR").length;
        quantFinanceiros = pessoas.filter(pessoa => pessoa.tipo === "FINANCEIRO").length;
        divGrafico = document.createElement('div');
        divGrafico.id = 'div-grafico-pessoas';
        canvasGrafico = document.createElement('canvas');
        canvasGrafico.id = 'grafico-pessoas';
        divGrafico.appendChild(canvasGrafico);
        new Chart(canvasGrafico.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Funcionários', 'Clientes', 'Proprietários', 'Corretores', 'Captadores', 'Administradores', 'Vistoriadores', 'Financeiros'],
                datasets: [{
                    label: 'Quantidade de Pessoas',
                    data: [quantFuncionarios, quantClientes, quantProprietarios, quantCorretores, quantCaptadores, quantAdministradores, quantVistoriadores, quantFinanceiros],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                        'rgba(201, 203, 207, 0.6)',
                        'rgba(255, 205, 86, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(201, 203, 207, 1)',
                        'rgba(255, 205, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: 'white',
                            font: {
                                size: 16
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Quantidade de Pessoas por Tipo',
                        color: 'white',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: 'white',
                            font: {
                                size: 16
                            }
                        }
                    },
                    y: {
                        ticks: {
                            color: 'white',
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            }
        });
        divPai.appendChild(divGrafico);
    }
    const imoveis = await listarImoveis();
    if (imoveis) {
        quantImoveis = imoveis.length;
        quantImoveisAlugados = imoveis.filter(imovel => imovel.status == "Alugado").length;
        quantImoveisVendidos = imoveis.filter(imovel => imovel.status == "Vendido").length;
        quantImoveisPendentes = imoveis.filter(imovel => imovel.status == "Pendente").length;
        quantImoveisParaAluguel = imoveis.filter(imovel => imovel.tipo == "Aluguel").length;
        quantImoveisParaVenda = imoveis.filter(imovel => imovel.tipo == "Venda").length;
        quantImoveisParaVendaAluguel = imoveis.filter(imovel => imovel.tipo == "Venda e Aluguel").length;
        divGrafico = document.createElement('div');
        divGrafico.id = 'div-grafico-imoveis';
        canvasGrafico = document.createElement('canvas');
        canvasGrafico.id = 'grafico-imoveis';
        divGrafico.appendChild(canvasGrafico);
        new Chart(canvasGrafico.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Pendentes', 'Vendidos', 'Alugados', 'Para Aluguel', 'Para Venda', 'Para Venda/Aluguel'],
                datasets: [{
                    label: 'Quantidade de Imóveis',
                    data: [quantImoveisPendentes, quantImoveisVendidos, quantImoveisAlugados, quantImoveisParaAluguel, quantImoveisParaVenda, quantImoveisParaVendaAluguel],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)',
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: 'white',
                            font: {
                                size: 16
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Quantidade de Imóveis por Tipo',
                        color: 'white',
                        font: {
                            size: 16
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: 'white',
                            font: {
                                size: 16
                            }
                        }
                    },
                    y: {
                        ticks: {
                            color: 'white',
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            }
        });
        divPai.appendChild(divGrafico);
    }

    document.body.insertBefore(divPai, document.body.querySelector('footer'));

    faturamentoTotal = imoveis.filter(imovel => imovel.status === "vendido" || imovel.status === "alugado").reduce((total, imovel) => total + imovel.status === 'vendido' ? imovel.valor_venda : imovel.valor_aluguel, 0);

    // TDOO: Calcular faturamento mensal
});