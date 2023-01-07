import { useState } from "react";
import { Box, Fab, Typography, Stack, ClickAwayListener } from "@mui/material";
import { Link } from "react-router-dom";
import { BsFillPersonFill } from 'react-icons/bs';
import { HiUserGroup } from 'react-icons/hi';
import { MdAdd } from "react-icons/md";
import './FabMenu.styles.css';

const FabMenu = () => {
    const [menuVisible, setMenuVisible] = useState(false);

  return (
    <ClickAwayListener
    mouseEvent="onMouseDown"
    touchEvent="onTouchStart"
    onClickAway={() => setMenuVisible(false)}>
        <Box
        sx={{ position: "fixed", bottom: 40, right: 40 }}>
            <Box
            width={150}
            sx={{
                backgroundColor: "white",
                borderRadius: 3,
                bottom: 80,
                right: 0,
                position: "absolute",
                display: menuVisible ? 'block' : 'none' }}>
                <Link to="/game" style={{ textDecoration: 'none', color: "black" }}>
                    <Stack
                    flexDirection="row"
                    justifyContent="flex-start"
                    alignItems="center"
                    p={3}
                    className="fab-button"
                    sx={{ borderBottom: "1px solid #f7f9f7" }}>
                        <BsFillPersonFill style={{ marginRight: 15 }} />
                        <Typography>1 joueur</Typography>
                    </Stack>
                </Link>
                <Link to="/game/2" style={{ textDecoration: 'none', color: "black" }}>
                    <Stack
                    flexDirection="row"
                    justifyContent="flex-start"
                    alignItems="center"
                    p={3}
                    className="fab-button">
                        <HiUserGroup style={{ marginRight: 15 }} />
                        <Typography>2 joueurs</Typography>
                    </Stack>
                </Link>
            </Box>

            <Fab
            onClick={() => setMenuVisible(menuVisible => !menuVisible)}
            sx={{ '&:hover svg': { transform: "rotate(90deg)", transitionDuration: "0.2s" }, 'svg': { transform: "rotate(-90deg)", transitionDuration: "0.2s" }, backgroundColor: "#4BA89C" }}
            aria-label="add">
                <MdAdd style={{ fontSize: 25 }} />
            </Fab>
        </Box>
    </ClickAwayListener>
  );
}

export default FabMenu;