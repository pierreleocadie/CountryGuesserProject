import { useRef, useState, useEffect, Dispatch, SetStateAction } from "react";
import { Map as Mapbox, Popup } from "mapbox-gl";
import { Paper, Button, Box } from "@mui/material";
import { circle } from "@turf/turf";

const displayCircle = (map: any, lat: number, lng: number, precision: number) => {
    // precision : 3 = Peu précis / 2 = Précis / 1 = Très précis
    
    const idCircle = "circle-fill";
    const idOutline = "circle-outline";
    const center = [lng, lat];
    const options = {
        steps: 80,
    };
    let radius = 6500;
    switch(precision) {
        case 2:
            radius = 3500;
            break;
        case 1:
            radius = 1700;
            break;
    }

    const circleClue: any = circle(center, radius, options);

    // Suppression du cercle existant
    map.getLayer(idCircle) && map.removeLayer(idCircle);
    map.getSource(idCircle) && map.removeSource(idCircle);
    map.getLayer(idOutline) && map.removeLayer(idOutline);
    map.getSource(idOutline) && map.removeSource(idOutline);

    // Couleur du cercle
    /*map.addLayer({
        "id": idCircle,
        "type": "fill",
        "source": {
            "type": "geojson",
            "data": circleClue
        },
        "paint": {
            "fill-color": "red",
            "fill-opacity": 0.5
        }
    });*/
    // Contour du cercle
    map.addLayer({
        "id": idOutline,
        "type": "line",
        "source": {
            "type": "geojson",
            "data": circleClue
        },
        "paint": {
            "line-color": "blue",
            "line-opacity": 0.5,
            "line-width": 10,
            "line-offset": 5
        },
        "layout": {}
    });

    map.flyTo({
        center: [lng, lat],
        essential: true,
        zoom: 1,
    });
}

const Map = (props: MapProps) => {
    const mapContainer = useRef(null);
    const map: any = useRef(null);
    const [lng, setLng] = useState(2.00);
    const [lat, setLat] = useState(40.00);
    const [zoom, setZoom] = useState(1);

    const [, setTimerInterval] = useState<any>(null);

    useEffect(() => {
        if (map.current) return;

        map.current = new Mapbox({
            container: mapContainer.current || "",
            style: 'mapbox://styles/dorit75/clak5c76z005914o64jbe3you?optimize=true',
            center: [lng, lat],
            zoom,
            accessToken: process.env.REACT_APP_MAPBOX_TOKEN,
        });

        map.current.on('move', () => {
            setLng(map.current.getCenter().lng.toFixed(4));
            setLat(map.current.getCenter().lat.toFixed(4));
            setZoom(map.current.getZoom().toFixed(2));
        });

        map.current.on('click', (e: any) => {
            const { lat, lng } = e.lngLat;
            fetch(`https://api.tiles.mapbox.com/v4/geocode/mapbox.places-country-v1/${lng},${lat}.json?access_token=pk.eyJ1IjoiZG9yaXQ3NSIsImEiOiJjbGFqdjU1bzYwZzBhM3NvMGJ0Z2M1a3F2In0.RddpBuye5jg57iGg25DQTA&language=fr`)
            .then(data => data.json())
            .then(data => {
                const { place_name_fr, properties } = data.features[0];
                new Popup()
                    .setLngLat(e.lngLat.wrap())
                    .setHTML(`<b>Votre choix :</b><br />${place_name_fr}`)
                    .addTo(map.current);

                props.setSelectedCountry({
                    name: place_name_fr,
                    code: properties.short_code.toUpperCase(),
                });
                props.setCanValidate(true);
            });
        });

        map.current.on('load', () => {
            props.onLoad && props.onLoad();
            props.setLeftClues(3);

            // Démarrage du chrono
            setTimerInterval(setInterval(() => props.setTimer(timer => timer + 1), 1000));
        });
    }, []);

    useEffect(() => {
        // Arrêt du chrono
        stopChronoIfExists();
    }, [props.winnerDialogVisible]);

    useEffect(() => {
        // Partie perdue
        if (props.mysteryCountry && props.losedGame) {
            const { latLng } = props.mysteryCountry;

            stopChronoIfExists();

            // Suppression du cercle existant
            map.current.getLayer("circle-fill") && map.current.removeLayer("circle-fill");
            map.current.getSource("circle-fill") && map.current.removeSource("circle-fill");
            map.current.getLayer("circle-outline") && map.current.removeLayer("circle-outline");
            map.current.getSource("circle-outline") && map.current.removeSource("circle-outline");

            map.current.flyTo({
                center: [latLng[1], latLng[0]],
                essential: true,
                zoom: 7,
            });

            setTimeout(() => {
                props.setLoserDialogVisible(true);
            }, 5000);
        }
    }, [props.mysteryCountry, props.losedGame]);

    const handleClueClick = () => {
        props.setLeftClues(leftClues => leftClues - 1);

        const lat = props.mysteryCountry.latLng[0];
        const lng = props.mysteryCountry.latLng[1];
        displayCircle(map.current, lat, lng, props.leftClues);
    }

    const stopChronoIfExists = () => {
        // Arrêt du chrono
        setTimerInterval((timerInterval: any) => {
            clearInterval(timerInterval);
            return null;
        });
    }

  return (
    <Box sx={{ position: "relative", width: "70vw", height: "90vh" }}>
        { props.leftClues > 0 &&
            <Button
            sx={{ position: "absolute", zIndex: 3, top: 0, left: 0, m: 3 }}
            variant="contained"
            onClick={handleClueClick}>
                Utiliser un indice ({ props.leftClues } restant{ props.leftClues > 1 && 's'})
            </Button>
        }
        { props.selectedCountry && props.selectedCountry.name && (
            <Paper sx={{ p: 2, zIndex: 1, position: "absolute", bottom: 0, right: 0, margin: "0 24px 36px 0" }}>
                Pays sélectionné : {props.selectedCountry.name}
            </Paper>
        )}
        <div ref={mapContainer} style={{ width: "100%", height: "90vh" }} />
    </Box>
  );
}

interface MapProps {
    isMultiplayer: boolean;
    losedGame: boolean;
    leftClues: number;
    mysteryCountry: { name: string, flag: string, code: string, latLng: number[] };
    selectedCountry: { name: string, code: string };
    winnerDialogVisible: boolean;   // pour savoir si la partie est terminée
    setSelectedCountry: Dispatch<SetStateAction<{ name: string, code: string }>>;
    setCanValidate: Dispatch<SetStateAction<boolean>>;
    setTimer: Dispatch<SetStateAction<number>>;
    setLeftClues: Dispatch<SetStateAction<number>>;
    setLoserDialogVisible: Dispatch<SetStateAction<boolean>>;
    onLoad: () => void;
}

export default Map;