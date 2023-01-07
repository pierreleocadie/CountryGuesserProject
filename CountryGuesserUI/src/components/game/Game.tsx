import { useState, useEffect, useContext } from "react";
import { Box, Typography, Alert, Button, Stack } from "@mui/material";
import { useNavigate, useParams } from "react-router-dom";
import Map from './Map';
import WinnerDialog from './dialogs/WinnerDialog';
import LoserDialog from './dialogs/LoserDialog';
import ErrorDialog from "./dialogs/ErrorDialog";
import LoadingBar from '../main/LoadingBar';
import Waiting from "./waiting/Waiting";
import Loader from "../main/Loader";
import { secondsToTime } from "../../utils/time.utils";
import { isAuthenticated } from "../../services/AuthService";
import UserContext from "../../services/UserContext";
import '../../animations/shake.animation.css';
import { useWebSocket } from "react-use-websocket/dist/lib/use-websocket";
import { ReadyState } from "react-use-websocket";
import { useSnackbar } from "notistack";

const Game = () => {
  const navigate = useNavigate();
  const { nbPlayers, nbRounds } = useParams();
  const { enqueueSnackbar } = useSnackbar();

  const [mysteryCountry, setMysteryCountry] = useState({ name: "", flag: "", code: "", latLng: [] });
  const [selectedCountry, setSelectedCountry] = useState({ name: "", code: "" });
  const [canValidate, setCanValidate] = useState(false);
  const [isLoading, setIsLoading] = useState(true);
  const [timer, setTimer] = useState(0);
  const [winner, setWinner] = useState("");
  const [winnerDialogVisible, setWinnerDialogVisible] = useState(false);
  const [loserDialogVisible, setLoserDialogVisible] = useState(false);
  const [errorDialogVisible, setErrorDialogVisible] = useState(false);
  const [losedGame, setLosedGame] = useState(false);
  const [leftClues, setLeftClues] = useState(0);  // Initialisé au chargement de la Map à 3
  const [errors, setErrors] = useState(0);
  const [shake, setShake] = useState(false);
  const [roundCount, setRoundCount] = useState(1);

  const [launchFoundPlayersAnimation, setLaunchFoundPlayersAnimation] = useState(false);
  const [foundPlayers, setFoundPlayers] = useState(false);

  const [socketUrl, setSocketUrl] = useState("");
  const { sendMessage, lastMessage, readyState } = useWebSocket(socketUrl);

  const connectionStatus = {
    [ReadyState.CONNECTING]: 'Connecting',
    [ReadyState.OPEN]: 'Open',
    [ReadyState.CLOSING]: 'Closing',
    [ReadyState.CLOSED]: 'Closed',
    [ReadyState.UNINSTANTIATED]: 'Uninstantiated',
  }[readyState];

  const [currentUser, setCurrentUser] = useContext(UserContext);

  useEffect(() => {
    document.body.style.backgroundColor = "#efeff0";  // Changement de la couleur de fond

    const user = isAuthenticated();
    setCurrentUser(user);

    if (!user.credential && isMultiplayer()) {
      navigate('/game');
    }
  }, []);

  useEffect(() => {
    if (isMultiplayer() && currentUser.credential) {
      setSocketUrl(`wss://${process.env.REACT_APP_WEBSOCKET_URI}?playerCredential=${ currentUser.credential }&roomSize=${ nbPlayers ? nbPlayers : "2" }&maxRounds=${ nbRounds ? nbRounds : "3" }`);
    }
  }, [currentUser]);

  useEffect(() => {
    if (readyState === ReadyState.OPEN)
      prepareMysteryCountry();
  }, [connectionStatus]);

  const handleMapLoaded = () => {
    if (!isMultiplayer()) {
      fetchRandomCountry().then(randomCountry => setMysteryCountry(randomCountry));
    }
    setIsLoading(false);
  }

  useEffect(() => {
    if (lastMessage) {
      const data = JSON.parse(lastMessage.data);

      if (data.errorType === "aPlayerLeft") {
        setErrorDialogVisible(true);
      }

      switch(data.informationType) {
        case "roomFull":  // Tous les joueurs trouvés
          setLaunchFoundPlayersAnimation(true);
          setTimeout(() => {
            setFoundPlayers(true);
          }, 3000);
          break;
        case "roundCreated":
          setMysteryCountry({
            name: data.name,
            flag: data.flag,
            code: data.code,
            latLng: data.latLng,
          });
          setLeftClues(3);
          break;
        case "wrongAnswer":
          setErrors(errors => errors + 1);
          setShake(true);
          break;
        case "roundOver": // Fin d'un round
          if (data.nextRoundId <= (nbRounds ?? 3)) {
            prepareMysteryCountry();
            setRoundCount(roundCount => roundCount + 1);

            if (data.roundWinnerNickname === currentUser.nickname) {
              enqueueSnackbar("Vous avez remporté ce round", { variant: "success" });
            } else {
              enqueueSnackbar("Votre adversaire a remporté ce round", { variant: "info" });
            }
          }
          break;
        case "gameOver":  // Fin de la partie
          setWinner(data.gameWinnerNickname);
          if (data.gameWinnerNickname === currentUser.nickname) {  
            setWinnerDialogVisible(true);
          }
          else {
            setLoserDialogVisible(true);
          }
          break;
        default: break;
      }
    }
  }, [lastMessage]);

  const prepareMysteryCountry = (): void => {
    if (isMultiplayer()) {
      fetchRandomCountry().then(randomCountry => {
        sendMessage(JSON.stringify({
          type: "roundData",
          ...randomCountry,
        }));
      });
    }
  }

  const fetchRandomCountry = (): Promise<any> => {
    return fetch("https://restcountries.com/v2/all/")
    .then(data => data.json())
    .then(data => {
      const randomCountry = data[Math.floor(Math.random() * data.length)];

      return {
        name: randomCountry.translations.fr,
        flag: randomCountry.flag,
        code: randomCountry.alpha2Code.toUpperCase(),
        latLng: randomCountry.latlng,
      };
    });
  }

  const handleValidateAnswer = () => {
    if (isMultiplayer()) {
      sendMessage(JSON.stringify({
        type: "playerResponse",
        playerResponse: selectedCountry.code,
      }));
    }
    else {
      if (selectedCountry.code === mysteryCountry.code) {
        // Bon pays validé
        setWinnerDialogVisible(true);
      }
      else {
        // Mauvais pays validé
        setErrors(errors => errors + 1);
        setShake(true);
      }
    }
  }

  const handleLeave = () => {
    setLosedGame(true);
  }

  const handleReplay = () => {
    // TODO: Réinitialiser tous les states à 0, y compris ceux de map
    // Solution temporaire
    window.location.reload();
  }

  const isMultiplayer = () => nbPlayers !== undefined && nbPlayers !== "1";

  return foundPlayers || !isMultiplayer() ? (
    <>
      <ErrorDialog
      open={errorDialogVisible}
      onReplay={handleReplay} />
      <WinnerDialog
      open={winnerDialogVisible}
      mysteryCountry={mysteryCountry}
      errors={errors}
      timer={timer}
      onReplay={handleReplay} />
      <LoserDialog
      open={loserDialogVisible}
      mysteryCountry={mysteryCountry}
      onReplay={handleReplay}
      winnerName={winner} />
      <LoadingBar visible={isLoading} />

      <Box
      sx={{ height: "90vh", margin: "30px", borderRadius: 5 }}
      overflow={{ xs: "visible", md: "hidden" }}
      bgcolor={{ xs: "transparent", md: "white" }}
      className={ shake ? 'shake' : '' }
      onAnimationEnd={() => setShake(false)}>
        <Stack
        direction={{xs: "column", md: "row"}}>
          <Box mr={5}>
            <Map
            losedGame={losedGame}
            leftClues={leftClues}
            mysteryCountry={mysteryCountry}
            selectedCountry={selectedCountry}
            setSelectedCountry={setSelectedCountry}
            setCanValidate={setCanValidate}
            setTimer={setTimer}
            setLeftClues={setLeftClues}
            onLoad={handleMapLoaded}
            winnerDialogVisible={winnerDialogVisible}
            setLoserDialogVisible={setLoserDialogVisible}
            isMultiplayer={isMultiplayer()} />
          </Box>
          <Stack
          pt={5}
          direction="column"
          justifyContent="space-between"
          overflow="hidden">
            <Box>
              <Typography color="lightgray">À toi de jouer !</Typography>
              <Typography variant="h3">Drapeau à trouver</Typography>
              <Typography variant="h6">Temps : {secondsToTime(timer)}s</Typography>
              { isMultiplayer() && parseInt(nbRounds ?? "3") > 1 && <Typography variant="h6">Tour : { roundCount } / { nbRounds ?? 3 }</Typography>  }
              { errors ? <Typography variant="h6">Erreurs : {errors}</Typography> : null }
            </Box>

            { mysteryCountry.flag ? (
              <Box>
                <img
                alt="Drapeau"
                src={ mysteryCountry.flag }
                style={{ width: "90%", border: "1px solid lightgray" }} />
              </Box>
            ) : (
              <Loader />
            )}

            <Stack direction="column" alignItems="flex-end" mr={4}>
              { selectedCountry.name && (
                  <Alert severity="info">Vous êtes sur le point de valider votre réponse : <b>{ selectedCountry.name }</b></Alert>
              )}

              <Stack
              direction="row"
              alignItems="center"
              justifyContent="space-around"
              flexWrap="wrap"
              width="100%"
              mt={2}
              mb={5}>
                { !isMultiplayer() &&
                  <Button
                  variant="outlined"
                  onClick={handleLeave}>
                    Abandonner
                  </Button>
                }
                <Button
                variant="contained"
                disabled={ !canValidate }
                onClick={handleValidateAnswer}>Confirmer ma réponse</Button>
              </Stack>
            </Stack>
          </Stack>
        </Stack>
      </Box>
    </>
  ) : (
    <Waiting launchFoundPlayersAnimation={launchFoundPlayersAnimation} />
  );
}

export default Game;