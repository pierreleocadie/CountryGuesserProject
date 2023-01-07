import { useState, useRef } from "react";
import { Stack, Box, Typography } from "@mui/material";
import { BsPlay, BsPause } from "react-icons/bs";
import Navbar from "../main/Navbar";
import FabMenu from "../main/FabMenu";
import "../../animations/credits.animation.css";
import "./About.styles.css";

const About = () => {
    const creditsRef = useRef<HTMLDivElement>(null);
    const [isCreditsRunning, setIsCreditsRunning] = useState(true);
    
    const handlePauseCredits = () => {
        setIsCreditsRunning(isCreditsRunning => !isCreditsRunning);
        if (creditsRef.current) {
            creditsRef.current.style.animationPlayState = isCreditsRunning ? 'paused' : 'running';
        }
    }
  return (
    <>
        <Navbar />

        <Stack
        justifyContent="center"
        alignItems="center"
        width="100vw"
        height="92vh"
        sx={{ position: "relative", overflow: "hidden", bottom: 0 }}
        onClick={handlePauseCredits}>
            <Box
            ref={creditsRef}
            className="credits about">
                <Typography variant="h3">À propos / Crédits</Typography>
                <br />
                <Typography variant="h5">Développeurs</Typography>
                <br />
                <ul>
                    <li>Jordan BAUMARD</li>
                    <li>Pierre LEOCADIE</li>
                    <li>Charles HURST</li>
                </ul>
                <br />
                <Typography>Étudiants à l'IUT de Paris - Rives de Seine (France)</Typography>
                <br />
                <Typography>Date de réalisation : nov/déc 2022</Typography>
                <br />
                <Typography>Merci de jouer à CountryGuesser !</Typography>
            </Box>
        </Stack>

        {
            isCreditsRunning ? <BsPlay className="playStateIcon" />
            : <BsPause className="playStateIcon" />
        }

        <FabMenu />
    </>
  );
}

export default About;