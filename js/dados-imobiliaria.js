const ctx = document.getElementById('meuGrafico').getContext('2d');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Vermelho', 'Azul', 'Amarelo'],
        datasets: [{
            label: 'Votos',
            data: [12, 19, 3],
            backgroundColor: [
                'rgb(64, 19, 29)',
                'rgb(9, 39, 60)',
                'rgb(80, 64, 23)'
            ],
            borderColor: [
                'rgb(255, 255, 255)',
                'rgb(255, 255, 255)',
                'rgb(255, 255, 255)'
            ],
            borderWidth: 1,
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
                text: 'Exemplo de Gráfico de Pizza',
                color: 'white',
                font: {
                    size: 16
                }
            }
        }
    }
});

const ctx2 = document.getElementById('myHorizontalBarChart').getContext('2d');

new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ['January', 'February', 'March', 'April', 'May'],
        datasets: [{
            label: 'Sales ($)',
            data: [1200, 1900, 3000, 5000, 2300],
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        scales: {
            x: {
                beginAtZero: true,
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
        },
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
                text: 'Exemplo de Gráfico de Horizontal',
                color: 'white',
                font: {
                    size: 16
                }
            },
        }
    }
});