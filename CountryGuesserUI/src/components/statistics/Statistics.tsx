import { useState, useEffect, useContext } from "react";
import { Container, Typography, Stack } from "@mui/material";
import { GiTrophyCup } from "react-icons/gi";
import { FaGamepad } from "react-icons/fa";
import { RiEyeCloseFill } from "react-icons/ri";
import { MdOutlineErrorOutline } from "react-icons/md";
import Board from './Board';
import Navbar from '../main/Navbar';
import FabMenu from "../main/FabMenu";
import UserContext from "../../services/UserContext";
import { isAuthenticated } from "../../services/AuthService";
import { getPlayerStatistics } from "../../utils/statistics.utils";

const Statistics = () => {
  const [wonGames, setWonGames] = useState(0);
  const [losedGames, setLosedGames] = useState(0);
  const [playedGames, setPlayedGames] = useState(0);
  const [rows, setRows] = useState<{ player_id: number, games_won: number, games_played: number, nickname: string }[]>([]);

  const [currentUser, setCurrentUser] = useContext(UserContext);

  const loadLeaderboard = () => {
    fetch(`https://${process.env.REACT_APP_API_URI}/player/getleaderboard`)
    .then(data => data.json())
    .then(data => {
      setRows(data);
    });
  }

  useEffect(() => {
    document.body.style.backgroundColor = "black"; // Changement de la couleur de fond
    setCurrentUser(isAuthenticated());
    
    loadLeaderboard();

    if (currentUser.credential) { // Récupération des statistiques du joueur
      getPlayerStatistics(currentUser).then(data => {
        setWonGames(data.wonGames);
        setPlayedGames(data.playedGames);
        setLosedGames(data.losedGames); 
      });
    }
  }, [currentUser.credential]);

  return (
    <>
        <Navbar />
        <Container sx={{ mb: 10, width: 1000 }}>
          <Typography color="white" variant="h4" mb={3} mt={5}>Vos statistiques</Typography>
          
          { currentUser.credential ?
            <Stack direction="row" flexWrap="wrap">
              <Stack direction="row" alignItems="center" gap={2} width={250}>
                <GiTrophyCup color="yellow" fontSize={25} />
                <Typography color="white">{ wonGames } parties gagnées</Typography>
              </Stack>

              <Stack direction="row" alignItems="center" gap={2} width={250}>
                <RiEyeCloseFill color="red" fontSize={25} />
                <Typography color="white">{ losedGames } parties perdues</Typography>
              </Stack>

              <Stack direction="row" alignItems="center" gap={2} width={250}>
                <FaGamepad color="white" fontSize={25} />
                <Typography color="white">{ playedGames } parties jouées</Typography>
              </Stack>
            </Stack>
          : <Stack direction="row" alignItems="center" gap={1}>
              <MdOutlineErrorOutline color="white" />
              <Typography color="white" fontStyle="italic">Connectez-vous pour afficher vos statistiques</Typography>
            </Stack>
          }

          <Typography color="white" variant="h4" mb={3} mt={5}>Classement - Multijoueurs</Typography>
          <Board rows={rows} />
        </Container>

        <FabMenu />
    </>
  );
}

export default Statistics;