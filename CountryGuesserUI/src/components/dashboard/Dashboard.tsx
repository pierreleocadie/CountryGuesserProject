import { useEffect, useState, useContext } from "react";
import { Box, Stack, Typography } from "@mui/material";
import { BsFillPersonFill } from 'react-icons/bs';
import { HiUserGroup } from 'react-icons/hi';
import { AiOutlineUsergroupAdd } from "react-icons/ai";
import { Link } from "react-router-dom";
import CustomGameDialog from "./CustomGameDialog";
import Navbar from "../main/Navbar";
import "./Dashboard.styles.css";
import UserContext from "../../services/UserContext";
import { isAuthenticated } from "../../services/AuthService";

const Dashboard = () => {
  const [customGameDialogVisible, setCustomGameDialogVisible] = useState(false);
  const [currentUser, setCurrentUser] = useContext(UserContext);

  useEffect(() => {
    document.body.style.backgroundColor = "black"; // Changement de la couleur de fond
  
    setCurrentUser(isAuthenticated());
  }, []);

  const openCustomGameDialog = () => {
    setCustomGameDialogVisible(true);
  }

  const closeCustomGameDialog = () => {
    setCustomGameDialogVisible(false);
  }

  return (
    <>
      { customGameDialogVisible &&
        <CustomGameDialog open={customGameDialogVisible} onClose={closeCustomGameDialog} />
      }

      <Navbar />

      <Stack
      alignItems="center"
      width="100%"
      sx={{ zIndex: 2, position: "absolute", bottom: 50 }}>
        <Typography
        variant="h5"
        color="white"
        mb={2}
        fontFamily="'Oswald', sans-serif">Lancer une partie</Typography>
        <Stack
        flexDirection="row"
        justifyContent="space-around"
        alignItems="center"
        sx={{ width: 700 }}>
          <Link style={{ textDecoration: "none" }} to="/game">
            <Typography align="center" className="play-btn">
              <BsFillPersonFill />&nbsp;
              1 joueur
            </Typography>
          </Link>
          { currentUser.credential &&
            <>
              <Link style={{ textDecoration: "none" }} to="/game/2">
                <Typography align="center" className="play-btn">
                  <HiUserGroup />&nbsp;
                  2 joueurs
                </Typography>
              </Link>
              <Typography onClick={openCustomGameDialog} align="center" className="play-btn">
                <AiOutlineUsergroupAdd />&nbsp;
                Personnalisé
              </Typography>
            </>
          }
        </Stack>
      </Stack>

      <Box sx={{ position: "fixed", width: "100vw" }}>
        <video autoPlay loop muted style={{ width: "100%" }}>
          <source src="world-video.mp4" type="video/mp4" />
        </video>
      </Box>
    </>
  );
}

export default Dashboard;