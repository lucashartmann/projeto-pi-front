let mapa;
let marcador;

export async function carregarMapa(lat, lng) {

    if (!mapa) {

        mapa = L.map("mapa").setView([lat, lng], 16);

        L.tileLayer(
            "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
            {
                attribution: "&copy; OpenStreetMap"
            }
        ).addTo(mapa);

    } else {

        mapa.setView([lat, lng], 16);

        if (marcador) {
            mapa.removeLayer(marcador);
        }

    }

    marcador = L.marker([lat, lng]).addTo(mapa);
}

export async function buscarCoordenadas(endereco) {

    const resposta = await fetch(
        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(endereco)}`
    );

    const dados = await resposta.json();

    if (!dados.length) return null;

    return {
        lat: parseFloat(dados[0].lat),
        lng: parseFloat(dados[0].lon)
    };
}
