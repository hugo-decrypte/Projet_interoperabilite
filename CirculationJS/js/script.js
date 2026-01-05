// script.js

// Variables globales
const liensApi = [];
let localisationUtilisateur = { lat: 48.6921, lon: 6.1844 }; // Nancy par défaut
let carte;
let donneesMeteo = null;
let donneesAir = null;

// Ajouter une API à la liste
function ajouterLienApi(nom, url) {
    liensApi.push({ nom, url });
}

// Afficher les liens API
function afficherLiensApi() {
    const liste = document.getElementById('api-list');
    liste.innerHTML = liensApi.map(api =>
        `<li><strong>${api.nom} :</strong><br><a href="${api.url}" target="_blank">${api.url}</a></li>`
    ).join('');
}

// Formater la date
function formaterDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('fr-FR');
}

// Géolocalisation navigateur avec fallback
function obtenirLocalisationUtilisateur() {
    return new Promise(resolve => {
        if (!navigator.geolocation) {
            console.warn('Géolocalisation non supportée, fallback Nancy');
            resolve(localisationUtilisateur);
            return;
        }

        navigator.geolocation.getCurrentPosition(
            position => {
                localisationUtilisateur = {
                    lat: position.coords.latitude,
                    lon: position.coords.longitude
                };
                console.info('Localisation navigateur utilisée', localisationUtilisateur);
                resolve(localisationUtilisateur);
            },
            error => {
                console.warn('Géolocalisation refusée ou échouée, fallback Nancy', error);
                resolve(localisationUtilisateur);
            },
            {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0
            }
        );
    });
}


// Récupérer la météo
async function recupererMeteo() {
        const url = `https://api.open-meteo.com/v1/forecast?latitude=${localisationUtilisateur.lat}&longitude=${localisationUtilisateur.lon}&current=temperature_2m,relative_humidity_2m,precipitation,wind_speed_10m&timezone=Europe/Paris`;
        ajouterLienApi('Météo Open-Meteo', url);

        const response = await fetch(url);
        const data = await response.json();
        donneesMeteo = data.current;

        const html = `
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Température</div>
                    <div class="info-value">${donneesMeteo.temperature_2m}°C</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Humidité</div>
                    <div class="info-value">${donneesMeteo.relative_humidity_2m}%</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Vent</div>
                    <div class="info-value">${donneesMeteo.wind_speed_10m} km/h</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Précipitations</div>
                    <div class="info-value">${donneesMeteo.precipitation} mm</div>
                </div>
            </div>
            <div class="timestamp">
                Dernière mise à jour : ${formaterDate(donneesMeteo.time)} (toutes les 15 minutes)
            </div>
        `;

        document.getElementById('weather-content').innerHTML = html;
}

// Récupérer la qualité de l'air
async function recupererQualiteAir() {
        const url = `https://air-quality-api.open-meteo.com/v1/air-quality?latitude=${localisationUtilisateur.lat}&longitude=${localisationUtilisateur.lon}&current=european_aqi,pm10,pm2_5&timezone=Europe/Paris`;
        ajouterLienApi('Qualité de l’air Open-Meteo', url);

        const response = await fetch(url);
        const data = await response.json();
        donneesAir = data.current;

        const indiceAqi = donneesAir.european_aqi;
        let texteQualite = 'Bon';

        if (indiceAqi > 100) texteQualite = 'Très mauvais';
        else if (indiceAqi > 75) texteQualite = 'Mauvais';
        else if (indiceAqi > 50) texteQualite = 'Moyen';
        else if (indiceAqi > 25) texteQualite = 'Modéré';

        const html = `
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Indice AQI</div>
                    <div class="info-value">${indiceAqi}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Qualité</div>
                    <div class="info-value">${texteQualite}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">PM2.5</div>
                    <div class="info-value">${donneesAir.pm2_5} μg/m³</div>
                </div>
                <div class="info-item">
                    <div class="info-label">PM10</div>
                    <div class="info-value">${donneesAir.pm10} μg/m³</div>
                </div>
            </div>
            <div class="timestamp">
                Dernière mise à jour : ${formaterDate(donneesAir.time)} (toutes les heures)
            </div>
        `;

        document.getElementById('air-content').innerHTML = html;
}

// Générer la recommandation
function genererRecommandation() {
    let html = '<p>';
    let veloAutorise = true;

    if (!donneesMeteo || !donneesAir) {
        html += 'Données insuffisantes pour générer une recommandation.';
    } else {
        if (donneesMeteo.precipitation > 2) {
            veloAutorise = false;
            html += 'Fortes précipitations – déconseillé<br>';
        } else if (donneesMeteo.precipitation > 0.5) {
            html += 'Pluie légère – prévoir un équipement<br>';
        }

        if (donneesMeteo.wind_speed_10m > 30) {
            veloAutorise = false;
            html += 'Vent très fort – déconseillé<br>';
        } else if (donneesMeteo.wind_speed_10m > 20) {
            html += 'Vent modéré – soyez prudent<br>';
        }

        if (donneesMeteo.temperature_2m < 0) {
            html += 'Températures négatives – équipez-vous<br>';
        }

        if (donneesAir.european_aqi > 75) {
            veloAutorise = false;
            html += 'Mauvaise qualité de l’air – évitez les efforts<br>';
        } else if (donneesAir.european_aqi > 50) {
            html += 'Qualité de l’air moyenne<br>';
        }

        if (veloAutorise && html === '<p>') {
            html += 'Bonnes conditions pour utiliser un vélo !';
        } else if (veloAutorise) {
            html += '<br><strong>Conditions acceptables avec précautions</strong>';
        } else {
            html += '<br><strong>Conditions défavorables</strong>';
        }
    }

    html += '</p>';
    document.getElementById('recommendation-content').innerHTML = html;
}

// Récupérer les stations VeloLib
async function recupererStationsVelolib() {
    const urlStations = 'https://api.cyclocity.fr/contracts/nancy/gbfs/v2/station_information.json';
    const urlStatuts = 'https://api.cyclocity.fr/contracts/nancy/gbfs/v2/station_status.json';

    ajouterLienApi('API GBFS Nancy', 'https://api.cyclocity.fr/contracts/nancy/gbfs/gbfs.json');
    ajouterLienApi('Stations VeloLib', urlStations);
    ajouterLienApi('Statuts VeloLib', urlStatuts);

    const [resStations, resStatuts] = await Promise.all([
        fetch(urlStations),
        fetch(urlStatuts)
    ]);

    const donneesStations = await resStations.json();
    const donneesStatuts = await resStatuts.json();

    const mapStatuts = {};
    donneesStatuts.data.stations.forEach(station => {
        mapStatuts[station.station_id] = station;
    });

    return donneesStations.data.stations.map(station => ({
        ...station,
        statut: mapStatuts[station.station_id]
    }));
}

// Initialiser la carte
function initialiserCarte() {
    carte = L.map('map').setView(
        [localisationUtilisateur.lat, localisationUtilisateur.lon],
        13
    );

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(carte);

    L.marker([localisationUtilisateur.lat, localisationUtilisateur.lon])
        .addTo(carte)
        .bindPopup('<strong>Votre position</strong>');
}


// Icône personnalisée pour les stations vélo
const iconeVelo = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/2972/2972185.png',
    iconSize: [48, 48],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32]
});


// Afficher les stations sur la carte
function afficherStations(stations) {
    stations.forEach(station => {
        if (!station.statut) return;

        const velosDisponibles = station.statut.num_bikes_available || 0;
        const placesLibres = station.statut.num_docks_available || 0;

        const marqueur = L.marker(
            [station.lat, station.lon],
            { icon: iconeVelo }
        ).addTo(carte);

        const derniereMaj = station.statut.last_reported
            ? formaterDate(new Date(station.statut.last_reported * 1000))
            : 'Non disponible';

        marqueur.bindPopup(`
            <strong>${station.name}</strong><br>
            Vélos disponibles : ${velosDisponibles}<br>
            Places libres : ${placesLibres}<br>
            <small>MAJ : ${derniereMaj}</small>
        `);
    });
}

// Fonction principale
async function initialiserApplication() {
    await obtenirLocalisationUtilisateur();
    initialiserCarte();

    await Promise.all([
        recupererMeteo(),
        recupererQualiteAir()
    ]);

    genererRecommandation();

    const stations = await recupererStationsVelolib();
    if (stations.length > 0) {
        afficherStations(stations);
    }

    afficherLiensApi();
}

// Lancer l'application
initialiserApplication();
